<?php
/**
 * @file
 * I metodi contenuti in questa cartella richiedono che i dati passati per parametro non siano vuoti e che
 * siano già filtrati da possibili attacchi XSS.
 * I prepared statement vengono utilizzati in tutte le casistiche in cui sono presenti campi inseriti dall'utente.
 */

function connectToDB($host = "localhost", $username = "qvanto", $password = "", $dbName = "my_qvanto"): mysqli
{
    $connection = new mysqli($host, $username, $password, $dbName);

    if ($connection->connect_error) {
        die('Errore di connessione al database (' . $connection->connect_errno . ') '
            . $connection->connect_error);
    }

    return $connection;
}