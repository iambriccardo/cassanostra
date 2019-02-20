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

function login($username, $password)
{
    $connection = connectToDB();

    $query = "SELECT *
    FROM cnUtente
    WHERE Username = '${username}'";

    $result = $connection->query($query);
    $isAllowed = false;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['Password'])) {
                $isAllowed = true;
                break;
            }
        }
    }

    $connection->close();

    return $isAllowed;
}

function register($firstName, $lastName, $email, $username, $password, $role)
{
    $connection = connectToDB();

    $password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO cnUtente
    (Username, Password, Email, Nome, Cognome, Ruolo)
    VALUES ('${username}', '${password}', '${email}', '${firstName}', '${lastName}', '${role}')";

    $connection->query($query);
    $isAllowed = empty(mysqli_error($connection));

    $connection->close();

    return $isAllowed;
}