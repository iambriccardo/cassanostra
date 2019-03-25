<?php
require_once 'main.php';

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

function getProductsNumberByBrand(): array {
    $connection = connectToDB();
    $dataPoints = array();

    $result = $connection->query("SELECT COUNT(*) AS NumProdotti, Produttore FROM `cnProdotto` GROUP BY Produttore ORDER BY NumProdotti DESC LIMIT 10");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($dataPoints, array("label" => $row['Produttore'], "y" => $row['NumProdotti']));
        }
    }

    return $dataPoints;
}

function getIncomingsHistory(): array {
    $connection = connectToDB();
    $dataPoints = array();

    $result = $connection->query("SELECT (PrezzoVendita * Quantita) AS Entrata, DataOra FROM cnVendita ORDER BY DataOra ASC");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($dataPoints, array("label" => date("d/m/Y", strtotime($row['DataOra'])), "y" => $row['Entrata']));
        }
    }

    return $dataPoints;
}

function getExpensesHistory(): array {
    $connection = connectToDB();
    $dataPoints = array();

    $result = $connection->query("SELECT (PrezzoAcquisto * Quantita) AS Uscita, DataOra FROM cnAcquisto ORDER BY DataOra ASC");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($dataPoints, array("label" => date("d/m/Y", strtotime($row['DataOra'])), "y" => $row['Uscita']));
        }
    }

    return $dataPoints;
}

