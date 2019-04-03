<?php
require_once 'main.php';

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

function setCashierInvoiceUser(int $invoiceId, string $clientUsername): bool
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

function registerPurchaseInvoice(int $invoiceNumber, string $invoiceDate, string $supplierUser)
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

function getSupplierInvoices(string $supplierUser, string $fromDate = null)
{
    $connection = connectToDB();

    if (empty($fromDate))
    {
        $result = $connection->query("SELECT tab1.NumeroFattura AS `Numero fattura`, tab1.DataFattura AS `Data fattura`, CONCAT('€', tab2.Totale) AS Totale
                                      FROM (SELECT ID_Fattura, NumeroFattura, DataFattura FROM cnFattura WHERE FK_Utente = '$supplierUser') AS tab1,
                                           (SELECT SUM(PrezzoAcquisto) as Totale, FK_Fattura FROM cnAcquisto GROUP BY FK_Fattura) AS tab2
                                      WHERE tab1.ID_Fattura = tab2.FK_Fattura
                                      ORDER BY tab1.DataFattura DESC");
    }
    else
    {
        if ($statement = $connection->prepare("SELECT tab1.NumeroFattura AS `Numero fattura`, tab1.DataFattura AS `Data fattura`, CONCAT('€', tab2.Totale) AS Totale
                                           FROM (SELECT ID_Fattura, NumeroFattura, DataFattura FROM cnFattura WHERE FK_Utente = ? AND DataFattura < ?) AS tab1,
                                                (SELECT SUM(PrezzoAcquisto) as Totale, FK_Fattura FROM cnAcquisto GROUP BY FK_Fattura) AS tab2
                                           WHERE tab1.ID_Fattura = tab2.FK_Fattura
                                           ORDER BY tab1.DataFattura DESC"))
        {
            $statement->bind_param("ss", $supplierUser, $fromDate);
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

function getAllInvoices($fromDate = "", $toDate = "") {
    $invoices = array();
    $connection = connectToDB();
    $dateFilterClause = "";

    if (!empty($fromDate) && !empty($toDate))
        $dateFilterClause = "AND DataFattura >= '${fromDate}' AND DataFattura <= '${toDate}'";

    $result = $connection->query("SELECT NumeroFattura AS `Numero fattura`, DataFattura AS `Data fattura`, CONCAT('+', TRUNCATE(SUM(Quantita * PrezzoVendita), 2), ' €') AS Totale
FROM cnFattura AS F, cnVendita AS V
WHERE F.ID_Fattura = V.FK_Fattura ${dateFilterClause} AND Stornato = 0
GROUP BY F.ID_Fattura
UNION
SELECT NumeroFattura AS `Numero fattura`, DataFattura AS `Data fattura`, CONCAT('-', TRUNCATE(SUM(Quantita * PrezzoAcquisto), 2), ' €') AS Totale
FROM cnFattura AS F, cnAcquisto AS A
WHERE F.ID_Fattura = A.FK_Fattura ${dateFilterClause}
GROUP BY F.ID_Fattura
ORDER BY `Data fattura`");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            array_push($invoices, array("Numero fattura" => $row['Numero fattura'], "Data fattura" => $row['Data fattura'], "Totale" => $row['Totale']));
        }
    }

    return $invoices;
}