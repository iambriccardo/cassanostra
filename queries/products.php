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

function registerCashierInvoice()
{
    $connection = connectToDB();
    if ($connection->multi_query("CREATE TEMPORARY TABLE IF NOT EXISTS t2 AS (SELECT * FROM cnFattura);
                                  INSERT INTO cnFattura (NumeroFattura, DataFattura, FK_Utente, ScontrinoCassa) VALUES
                                   ((SELECT MAX(NumeroFattura)+1 FROM t2 WHERE ScontrinoCassa = 1), CURDATE(), NULL, 1)"))
    {
        $connection->next_result();
        // Ritorna l'ID della riga appena inserita oppure 0 se l'inserimento è fallito
        $insertId = $connection->insert_id;
        $connection->close();
        return $insertId;
    }

    return null;
}

function setCashierInvoiceUser($invoiceId, $clientUsername): bool
{
    $updateSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("UPDATE cnFattura SET FK_Utente = ? WHERE ID_Fattura = ?"))
    {
        $statement->bind_param("si", $clientUsername, $invoiceId);
        $statement->execute();
        if ($statement->errno === 0)
            $updateSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $updateSuccessful;
}

function registerPurchaseInvoice($invoiceNumber, $invoiceDate, $supplierUser)
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnFattura (NumeroFattura, DataFattura, FK_Utente, ScontrinoCassa) VALUES (?, ?, ?, 0)"))
    {
        $statement->bind_param("iss", $invoiceNumber, $invoiceDate, $supplierUser);
        $statement->execute();
        $statement->close();
    }

    // Ritorna l'ID della riga appena inserita oppure 0 se l'inserimento è fallito
    $insertId = $connection->insert_id;
    $connection->close();
    return $insertId;
}

function registerSale($productId, $productAmount, $productPrice, $invoiceId, $cashierId)
{
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnVendita (FK_Prodotto, Quantita, PrezzoVendita, FK_Fattura, FK_Cassa, FK_UtenteCassiere) VALUES (?, ?, ?, ?, ?, ?)"))
    {
        $statement->bind_param("iidiis", $productId, $productAmount, $productPrice, $invoiceId, $cashierId, $_SESSION["username"]);
        $statement->execute();
        $statement->close();
    }

    // Ritorna l'ID della riga appena inserita oppure 0 se l'inserimento è fallito
    $insertId = $connection->insert_id;
    $connection->close();
    return $insertId;
}

function cancelSale($saleId): bool
{
    $cancellationSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("UPDATE cnVendita SET Stornato=1 WHERE ID_Vendita = ?"))
    {
        $statement->bind_param("i", $saleId);
        $statement->execute();
        if ($statement->errno === 0)
            $cancellationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $cancellationSuccessful;
}

function registerPurchase($productId, $productAmount, $productPrice, $invoiceId, $storeId)
{
    $registrationSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnAcquisto (FK_Prodotto, Quantita, PrezzoAcquisto, FK_Fattura, FK_PuntoVendita, FK_UtenteMagazziniere) VALUES (?, ?, ?, ?, ?, ?)"))
    {
        $statement->bind_param("iidiis", $productId, $productAmount, $productPrice, $invoiceId, $storeId, $_SESSION["username"]);
        $statement->execute();
        if ($statement->errno === 0)
            $registrationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $registrationSuccessful;
}

function registerNewProduct($productName, $productBrand, $eanCode, $sellPrice): bool
{
    $registrationSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnProdotto (NomeProdotto, Produttore, EAN_Prodotto, PrezzoVenditaAttuale) VALUES (?, ?, ?, ?)"))
    {
        $statement->bind_param("sssd", $productName, $productBrand, $eanCode, $sellPrice);
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

    if ($statement = $connection->prepare("SELECT ID_Prodotto, NomeProdotto, Produttore, EAN_Prodotto, PrezzoVenditaAttuale FROM cnProdotto WHERE EAN_Prodotto = ?"))
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