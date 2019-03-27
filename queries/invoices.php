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

function getIncomingsInvoices(): array {
    $connection = connectToDB();
    $result = $connection->query("SELECT NumeroFattura AS `Numero fattura`, DataFattura AS `Data fattura`, CONCAT(TRUNCATE(SUM(Quantita * PrezzoAcquisto), 2), '€') AS Totale
FROM cnFattura AS F, cnAcquisto AS A
WHERE F.ID_Fattura = A.FK_Fattura 
GROUP BY F.ID_Fattura
ORDER BY DataFattura DESC");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function getExpensesInvoices(): array {
    $connection = connectToDB();
    $result = $connection->query("SELECT NumeroFattura AS `Numero fattura`, DataFattura AS `Data fattura`, CONCAT(TRUNCATE(SUM(Quantita * PrezzoVendita), 2), '€') AS Totale
FROM cnFattura AS F, cnVendita AS V
WHERE F.ID_Fattura = V.FK_Fattura 
GROUP BY F.ID_Fattura
ORDER BY DataFattura DESC");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}
