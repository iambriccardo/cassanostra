<?php
require_once 'main.php';

function addNewStore($storeName): bool
{
    $additionSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnPuntoVendita (NomePunto) VALUES (?)")) {
        $statement->bind_param("s", $storeName);
        $statement->execute();
        if ($statement->errno === 0)
            $additionSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $additionSuccessful;
}

function getStoresList()
{
    $connection = connectToDB();
    $result = $connection->query("SELECT ID_PuntoVendita AS Codice, NomePunto AS Nome FROM cnPuntoVendita");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function getCashiersForStore($storeId)
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