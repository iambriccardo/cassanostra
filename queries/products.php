<?php
require_once 'main.php';

function getProductEANsList()
{
    $connection = connectToDB();
    $result = $connection->query("SELECT EAN_Prodotto FROM cnProdotto");

    if ($result == false)
        return null;
    else {
        $eanList = [];
        while ($row = $result->fetch_row())
            $eanList[] = $row[0];

        return $eanList;
    }
}

function registerNewProduct($productName, $productBrand, $eanCode): bool
{
    $registrationSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnProdotto (NomeProdotto, Produttore, EAN_Prodotto) VALUES (?, ?, ?)"))
    {
        $statement->bind_param("sss", $productName, $productBrand, $eanCode);
        $statement->execute();
        if ($statement->errno === 0)
            $registrationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $registrationSuccessful;
}

function getProductDetails($eanCode)
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("SELECT NomeProdotto, Produttore, EAN_Prodotto FROM cnProdotto WHERE EAN_Prodotto = ?"))
    {
        $statement->bind_param("s", $eanCode);
        $statement->execute();

        $result = $statement->get_result();
        $statement->close();
    }

    if ($result == false || $result->num_rows === 0)
        return null;
    else
        return $result->fetch_assoc();
}

function getProductInventory($storeId, $nameOrEanFilter = null)
{
    $connection = connectToDB();

    if (empty($nameOrEanFilter))
    {
        if ($statement = $connection->prepare(
            "SELECT NomeProdotto AS `Nome prodotto`, EAN_Prodotto AS Codice, (ProdAcquistati.Tot-COALESCE(ProdVenduti.Tot, 0)) AS `Quantità disponibile`
            FROM
            (
                ((SELECT FK_Prodotto, SUM(COALESCE(Quantita, 0)) AS Tot FROM cnAcquisto WHERE FK_PuntoVendita = ? GROUP BY FK_Prodotto) AS ProdAcquistati)
                LEFT JOIN cnProdotto ON ProdAcquistati.FK_Prodotto = ID_Prodotto
            ) LEFT JOIN ((SELECT FK_Prodotto, SUM(COALESCE(Quantita, 0)) AS Tot FROM cnVendita, cnCassa WHERE FK_PuntoVendita = ? GROUP BY FK_Prodotto) AS ProdVenduti) ON ProdVenduti.FK_Prodotto = ID_Prodotto
            GROUP BY ID_Prodotto"
        )) {
            $statement->bind_param("ii", $storeId, $storeId);
            $statement->execute();

            $result = $statement->get_result();
            $statement->close();
        }
    }
    else {
        if ($statement = $connection->prepare(
            "SELECT NomeProdotto AS `Nome prodotto`, EAN_Prodotto AS Codice, (ProdAcquistati.Tot-COALESCE(ProdVenduti.Tot, 0)) AS `Quantità disponibile`
            FROM
            (
                ((SELECT FK_Prodotto, SUM(COALESCE(Quantita, 0)) AS Tot FROM cnAcquisto WHERE FK_PuntoVendita = ? GROUP BY FK_Prodotto) AS ProdAcquistati)
                LEFT JOIN cnProdotto ON ProdAcquistati.FK_Prodotto = ID_Prodotto
            ) LEFT JOIN ((SELECT FK_Prodotto, SUM(COALESCE(Quantita, 0)) AS Tot FROM cnVendita, cnCassa WHERE FK_PuntoVendita = ? GROUP BY FK_Prodotto) AS ProdVenduti) ON ProdVenduti.FK_Prodotto = ID_Prodotto
            WHERE NomeProdotto LIKE ? OR EAN_Prodotto LIKE ?
            GROUP BY ID_Prodotto"
        )) {
            $wildcardFilter = "%$nameOrEanFilter%";
            $statement->bind_param("iiss", $storeId, $storeId, $wildcardFilter, $wildcardFilter);
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