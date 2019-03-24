<?php
require_once 'main.php';

/**
 * Ottiene la lista degli utenti, ritornando opzionalmente gli utenti che hanno nel nome, cognome o username
 * la stringa specificata.
 * @param string|null $nameFilter la stringa da usare come parametro di ricerca
 * @return array l'array dei risultati; null in caso di fallimento
 */
function getUsersList(string $nameFilter = null)
{
    $connection = connectToDB();

    if (empty($nameFilter))
        $result = $connection->query("SELECT Username, Email, Nome, Cognome, Ruolo FROM cnUtente");
    else {
        if ($statement = $connection->prepare(
            "SELECT Nome, Cognome, Username, Email, Ruolo FROM cnUtente WHERE Nome LIKE ? OR Cognome LIKE ? OR Username LIKE ?"
        )) {
            $wildcardFilter = "%$nameFilter%";
            $statement->bind_param("sss", $wildcardFilter, $wildcardFilter, $wildcardFilter);
            $statement->execute();

            $result = $statement->get_result();
            $statement->close();
        }
    }

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function getSuppliers()
{
    $connection = connectToDB();
    $result = $connection->query("SELECT Username, Nome, Cognome, Azienda FROM cnUtente WHERE Azienda IS NOT NULL AND Ruolo = 'FOR'");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function createFidelityCard(string $username, int $points = 0): bool
{
    $creationSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnCartaFedelta (SaldoPunti, FK_Utente) VALUES (?, ?)")) {
        $statement->bind_param("is", $points, $username);
        $statement->execute();
        if ($statement->errno === 0)
            $creationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $creationSuccessful;
}

function getFidelityCardData(string $username): array
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("SELECT * FROM cnCartaFedelta WHERE FK_Utente = ?")) {
        $statement->bind_param("s", $username);
        $statement->execute();

        $result = $statement->get_result();

        while ($row = $result->fetch_assoc()) {
            return $row;
        }

        $statement->close();
    }

    $connection->close();
    return array();
}