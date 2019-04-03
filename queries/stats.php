<?php
require_once 'main.php';

function getBestSellingProduct(): string {
    $connection = connectToDB();

    $result = $connection->query("SELECT NomeProdotto
FROM cnVendita, cnProdotto
WHERE ID_Prodotto = FK_Prodotto AND Stornato = 0
GROUP BY ID_Prodotto
ORDER BY SUM(Quantita) DESC
LIMIT 1");

    if ($result->num_rows > 0)
        return $result->fetch_row()[0];

    return null;
}

function getBestSellingBrand(): string {
    $connection = connectToDB();

    $result = $connection->query("SELECT Produttore
FROM cnVendita, cnProdotto
WHERE ID_Prodotto = FK_Prodotto AND Stornato = 0
GROUP BY Produttore
ORDER BY COUNT(*) DESC
LIMIT 1");

    if ($result->num_rows > 0)
        return $result->fetch_row()[0];

    return null;
}

function getMonthlyIncome(): string {
    $connection = connectToDB();
    $currentMonth = date('m');

    $result = $connection->query("SELECT CONCAT(TRUNCATE(SUM(Quantita * PrezzoVendita), 2), '€') AS Entrate
FROM cnVendita
WHERE MONTH(DataOra) = ${currentMonth} AND Stornato = 0");

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

    $result = $connection->query("SELECT CONCAT(TRUNCATE(SUM(Quantita * PrezzoAcquisto), 2), '€') AS Uscite
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

function getMostSellingBrands(): array {
    $connection = connectToDB();
    $dataPoints = array();

    $result = $connection->query("SELECT COUNT(*) AS ProdottiVenduti, P.Produttore 
FROM cnVendita AS V, cnProdotto AS P 
WHERE V.FK_Prodotto = P.ID_Prodotto AND Stornato = 0
GROUP BY P.Produttore 
ORDER BY ProdottiVenduti DESC 
LIMIT 5");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($dataPoints, array("label" => $row['Produttore'], "y" => $row['ProdottiVenduti']));
        }
    }

    return $dataPoints;
}

function getMostSellingProducts(): array {
    $connection = connectToDB();
    $dataPoints = array();

    $result = $connection->query("SELECT SUM(V.Quantita) AS QuantitaVendute, P.NomeProdotto 
FROM cnVendita AS V, cnProdotto AS P 
WHERE V.FK_Prodotto = P.ID_Prodotto AND Stornato = 0
GROUP BY P.ID_Prodotto 
ORDER BY QuantitaVendute DESC 
LIMIT 5");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($dataPoints, array("label" => $row['NomeProdotto'], "y" => $row['QuantitaVendute']));
        }
    }

    return $dataPoints;
}

function getIncomingsHistory(): array {
    $connection = connectToDB();
    $dataPoints = array();

    $result = $connection->query("SELECT SUM((PrezzoVendita * Quantita)) AS Entrata, DataOra 
FROM cnVendita 
WHERE Stornato = 0
GROUP BY YEAR(DataOra), MONTH(DataOra), DAY(DataOra) 
ORDER BY YEAR(DataOra), MONTH(DataOra), DAY(DataOra) ASC");

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

    $result = $connection->query("SELECT SUM((PrezzoAcquisto * Quantita)) AS Uscita, DataOra 
FROM cnAcquisto 
GROUP BY YEAR(DataOra), MONTH(DataOra), DAY(DataOra) 
ORDER BY YEAR(DataOra), MONTH(DataOra), DAY(DataOra) ASC");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($dataPoints, array("label" => date("d/m/Y", strtotime($row['DataOra'])), "y" => $row['Uscita']));
        }
    }

    return $dataPoints;
}

function getCashiersStats($bestStats) {
    $connection = connectToDB();
    $result = $connection->query("SELECT CONCAT(Nome, ' ', Cognome) AS `Nome cassiere`, T1.NumVendite AS `Prodotti venduti`, CONCAT(TRUNCATE((T2.NumStorni / T1.NumVendite) * 100, 2), '%') AS `Percentuale storni`
FROM (
SELECT FK_UtenteCassiere, COUNT(*) NumVendite
FROM cnVendita
GROUP BY FK_UtenteCassiere) AS T1,
(SELECT FK_UtenteCassiere, COUNT(*) NumStorni
FROM cnVendita
WHERE Stornato = 1
GROUP BY FK_UtenteCassiere) AS T2,
(SELECT FK_UtenteCassiere, COUNT(*) NumNonStorni
FROM cnVendita
WHERE Stornato = 0
GROUP BY FK_UtenteCassiere) AS T3, cnUtente
WHERE T1.FK_UtenteCassiere = T2.FK_UtenteCassiere AND T2.FK_UtenteCassiere = T3.FK_UtenteCassiere AND T1.FK_UtenteCassiere = Username
ORDER BY " . ($bestStats ? "T1.NumVendite" : "`Percentuale storni`") . " DESC");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}