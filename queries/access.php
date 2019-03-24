<?php
require_once 'main.php';

function isUserPasswordCorrect($username, $password): bool
{
    $connection = connectToDB();
    $isPwdCorrect = false;

    if ($query = $connection->prepare("SELECT Password FROM cnUtente WHERE Username = ?")) {
        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            if (password_verify($password, $result->fetch_assoc()['Password']))
                $isPwdCorrect = true;
        }

        $query->close();
    }

    $connection->close();
    return $isPwdCorrect;
}

function attemptLogin($username, $password): bool
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

function attemptRegistrationAndLogin($firstName, $lastName, $email, $username, $password, $role): bool
{
    $isAllowed = false;

    $registrationSuccessful = attemptRegistration($firstName, $lastName, $email, $username, $role, $password);
    if ($registrationSuccessful)
        $isAllowed = attemptLogin($username, $password);

    return $isAllowed;
}

function attemptRegistration($firstName, $lastName, $email, $username, $role, $company = null, $password = "cambiami"): bool
{
    $registrationSuccessful = false;
    $connection = connectToDB();
    $hashed_pwd = password_hash($password, PASSWORD_BCRYPT);

    if ($statement = $connection->prepare("INSERT INTO cnUtente (Username, Password, Email, Nome, Cognome, Ruolo, Azienda) VALUES (?, ?, ?, ?, ?, ?, ?)"))
    {
        $companyOnlyIfSupplier = ($role === "FOR" && !empty($company)) ? $company : null;
        $statement->bind_param("sssssss", $username, $hashed_pwd, $email, $firstName, $lastName, $role, $companyOnlyIfSupplier);
        $statement->execute();
        if ($statement->errno === 0)
            $registrationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $registrationSuccessful;
}

function attemptPasswordUpdate($username, $currentPassword, $newPassword): bool
{
    $updateSuccessful = false;
    $connection = connectToDB();

    if ($currentPassword === $newPassword || !isUserPasswordCorrect($username, $currentPassword))
        return false;

    $hashedNewPwd = password_hash($newPassword, PASSWORD_BCRYPT);
    if ($connection->query("UPDATE cnUtente SET Password = '$hashedNewPwd' WHERE Username = '$username'"))
        $updateSuccessful = true;

    return $updateSuccessful;
}