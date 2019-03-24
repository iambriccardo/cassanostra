<?php
require_once 'main.php';

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

function getBestSellingProduct(): string {
    $connection = connectToDB();

    $result = $connection->query("SELECT NomeProdotto
FROM cnVendita, cnProdotto
WHERE ID_Prodotto = FK_Prodotto
GROUP BY ID_Prodotto
HAVING COUNT(*) = (SELECT MAX(T.NumVendite)
FROM (SELECT COUNT(*) AS NumVendite
FROM cnVendita AS V
GROUP BY V.FK_Prodotto) AS T)
LIMIT 1");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['NomeProdotto'];
        }
    }

    return null;
}

function getBestSellingBrand(): string {
    $connection = connectToDB();

    $result = $connection->query("SELECT Produttore
FROM cnVendita, cnProdotto
WHERE ID_Prodotto = FK_Prodotto
GROUP BY ID_Prodotto
HAVING COUNT(*) = (SELECT MAX(T.NumVendite)
FROM (SELECT COUNT(*) AS NumVendite
FROM cnVendita AS V, cnProdotto AS P
WHERE P.ID_Prodotto = V.FK_Prodotto      
GROUP BY P.Produttore) AS T)
LIMIT 1");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['Produttore'];
        }
    }

    return null;
}

function getMonthlyIncome(): string {
    $connection = connectToDB();
    $currentMonth = date('m');

    $result = $connection->query("SELECT SUM(Quantita * PrezzoVendita) AS Entrate
FROM cnVendita
WHERE MONTH(DataOra) = ${currentMonth}");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $income = $row['Entrate'];

            if (empty($income)) $income = '0.00';

            return $income;
        }
    }

    return null;
}

function getMonthlyExpenses(): string {
    $connection = connectToDB();
    $currentMonth = date('m');

    $result = $connection->query("SELECT SUM(Quantita * PrezzoAcquisto) AS Uscite
FROM cnAcquisto
WHERE MONTH(DataOra) = ${currentMonth}");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expenses = $row['Uscite'];

            if (empty($expenses)) $expenses = '0.00';

            return $expenses;
        }
    }

    return null;
}