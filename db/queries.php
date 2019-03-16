<?php

function connectToDB($host = "localhost", $username = "qvanto", $password = "", $dbName = "my_qvanto")
{
    $connection = new mysqli($host, $username, $password, $dbName);

    if ($connection->connect_error) {
        die('Errore di connessione (' . $connection->connect_errno . ') '
            . $connection->connect_error);
    }

    return $connection;
}

function attemptLogin($username, $password)
{
    $connection = connectToDB();
    $isAllowed = false;

    if ($query = $connection->prepare("SELECT * FROM cnUtente WHERE Username = ?")) {
        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['Password'])) {
                    $isAllowed = true;

                    session_start();
                    $_SESSION['username'] = $row['Username'];
                    $_SESSION['firstName'] = $row['Nome'];
                    $_SESSION['lastName'] = $row['Cognome'];
                    $_SESSION['role'] = $row['Ruolo'];
                    break;
                }
            }
        }

        $query->close();
    }

    $connection->close();

    return $isAllowed;
}

function attemptRegistrationAndLogin($firstName, $lastName, $email, $username, $password, $role)
{
    $isAllowed = false;

    $registrationSuccessful = attemptRegistration($firstName, $lastName, $email, $username, $role, $password);
    if ($registrationSuccessful)
        $isAllowed = attemptLogin($username, $password);

    return $isAllowed;
}

function attemptRegistration($firstName, $lastName, $email, $username, $role, $password = "cambiami")
{
    $registrationSuccessful = false;
    $connection = connectToDB();
    $hashed_pwd = password_hash($password, PASSWORD_BCRYPT);

    if ($statement = $connection->prepare("INSERT INTO cnUtente (Username, Password, Email, Nome, Cognome, Ruolo) VALUES (?, ?, ?, ?, ?, ?)"))
    {
        $statement->bind_param("ssssss", $username, $hashed_pwd, $email, $firstName, $lastName, $role);
        $statement->execute();
        if ($statement->errno === 0)
            $registrationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $registrationSuccessful;
}

function addNewStore($storeName)
{
    $additionSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnPuntoVendita (NomePunto) VALUES (?)"))
    {
        $statement->bind_param("s", $storeName);
        $statement->execute();
        if ($statement->errno === 0)
            $additionSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $additionSuccessful;
}

function getUsersList()
{
    $connection = connectToDB();
    $result = $connection->query("SELECT Username, Email, Nome, Cognome, Ruolo FROM cnUtente");

    if ($result == false)
        return null;
    else
        return $result->fetch_all();
}

function generateTableHtmlFromQueryResult($headerNames, $resultRows)
{
    // TODO
}