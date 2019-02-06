<?php

function connectToDB($host = "localhost", $username = "qvanto", $password = "", $dbName = "my_qvanto")
{
    $mysqli = new mysqli($host, $username, $password, $dbName);

    if ($mysqli->connect_error) {
        die('Errore di connessione (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
    } else {
        echo 'Connesso. ' . $mysqli->host_info . "\n";
    }

    return $mysqli;
}

function login($username, $password)
{
    $connection = connectToDB();

    $password = password_hash($password, PASSWORD_BCRYPT);

    $query = "SELECT
    FROM cnUtenti
    WHERE Username = '${username}' AND Password = ${password}";

    return $connection->query($query);
}

function register($firstName, $lastName, $username, $password, $role)
{
    $connection = connectToDB();
}

?>