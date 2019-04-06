<?php
require_once 'main.php';

function addNewStore(string $storeName)
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnPuntoVendita (NomePunto) VALUES (?)")) {
        $statement->bind_param("s", $storeName);
        $statement->execute();
        $statement->close();
    }

    // Ritorna l'ID della riga appena inserita oppure 0 se l'inserimento è fallito
    $insertId = $connection->insert_id;
    $connection->close();
    return $insertId;
}

function addCashRegistersToStore(int $storeId, int $registersAmount): bool
{
    $allInsertionsSuccessful = true;
    $connection = connectToDB();

    $connection->query("CREATE TEMPORARY TABLE IF NOT EXISTS t2 AS (SELECT * FROM cnCassa);");
    for ($i = 0; $i < $registersAmount; $i++)
    {
        if ($statement = $connection->prepare("INSERT INTO cnCassa (FK_PuntoVendita, NumeroCassa)
                                              VALUES (?, (SELECT COALESCE(MAX(NumeroCassa), 0)+1+$i FROM t2 WHERE FK_PuntoVendita = ?))")) {
            $statement->bind_param("ii", $storeId, $storeId);
            $statement->execute();
            if ($statement->errno !== 0 && $allInsertionsSuccessful)
                $allInsertionsSuccessful = false;

            $statement->close();
        }
    }

    $connection->close();
    return $allInsertionsSuccessful;
}

function getStoresList()
{
    $connection = connectToDB();
    $result = $connection->query("SELECT ID_PuntoVendita AS Codice, NomePunto AS Nome, COALESCE(casseNegozi.numCasse, 0) AS `Numero casse`
                                  FROM cnPuntoVendita LEFT JOIN (SELECT FK_PuntoVendita, COUNT(*) AS numCasse FROM cnCassa GROUP BY FK_PuntoVendita) AS casseNegozi ON FK_PuntoVendita = ID_PuntoVendita
                                  ORDER BY ID_PuntoVendita ASC");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function getCashiersForStore(int $storeId)
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("SELECT ID_Cassa, NumeroCassa FROM cnCassa WHERE FK_PuntoVendita = ?"))
    {
        $statement->bind_param("i", $storeId);
        $statement->execute();

        $result = $statement->get_result();
        $statement->close();
    }

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}