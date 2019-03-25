<?php
require_once 'main.php';

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

function getClientRecentActivities(string $username)
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("SELECT * 
FROM cnFattura AS F, cnVendita AS V, cnProdotto AS P
WHERE F.FK_Utente = ? AND F.ID_Fattura = V.FK_Fattura AND V.FK_Prodotto = P.ID_Prodotto
ORDER BY F.DataFattura, V.DataOra DESC")) {
        $statement->bind_param("s", $username);
        $statement->execute();

        $result = $statement->get_result();

        $statement->close();
        $connection->close();

        return $result;
    }

    return null;
}