<?php
/**
 * @file
 * I metodi in questo file richiedono che i dati passati per parametro non siano vuoti e che
 * siano già filtrati da possibili attacchi XSS.
 * I prepared statement vengono utilizzati in tutte le casistiche in cui vi è la possibilità di attacchi SQL injection.
 */

function connectToDB($host = "localhost", $username = "qvanto", $password = "", $dbName = "my_qvanto"): mysqli
{
    $connection = new mysqli($host, $username, $password, $dbName);

    if ($connection->connect_error) {
        die('Errore di connessione (' . $connection->connect_errno . ') '
            . $connection->connect_error);
    }

    return $connection;
}

function isUserPasswordCorrect($username, $password): bool
{
    $connection = connectToDB();
    $isPwdCorrect = false;

    if ($query = $connection->prepare("SELECT Password FROM cnUtente WHERE Username = ?")) {
        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            if (password_verify($password, $result->fetch_assoc()['Password']))
                $isPwdCorrect = true;
        }

        $query->close();
    }

    $connection->close();
    return $isPwdCorrect;
}

function attemptLogin($username, $password): bool
{
    $connection = connectToDB();
    $isAllowed = false;

    if ($query = $connection->prepare("SELECT * FROM cnUtente WHERE Username = ?")) {
        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['Password'])) {
                    $isAllowed = true;

                    session_start();
                    $_SESSION['username'] = $row['Username'];
                    $_SESSION['firstName'] = $row['Nome'];
                    $_SESSION['lastName'] = $row['Cognome'];
                    $_SESSION['role'] = $row['Ruolo'];
                    break;
                }
            }
        }

        $query->close();
    }

    $connection->close();
    return $isAllowed;
}

function attemptRegistrationAndLogin($firstName, $lastName, $email, $username, $password, $role): bool
{
    $isAllowed = false;

    $registrationSuccessful = attemptRegistration($firstName, $lastName, $email, $username, $role, $password);
    if ($registrationSuccessful)
        $isAllowed = attemptLogin($username, $password);

    return $isAllowed;
}

function attemptRegistration($firstName, $lastName, $email, $username, $role, $company, $password = "cambiami"): bool
{
    $registrationSuccessful = false;
    $connection = connectToDB();
    $hashed_pwd = password_hash($password, PASSWORD_BCRYPT);

    if ($statement = $connection->prepare("INSERT INTO cnUtente (Username, Password, Email, Nome, Cognome, Ruolo, Azienda) VALUES (?, ?, ?, ?, ?, ?, ?)"))
    {
        $companyOnlyIfSupplier = ($role === "FOR" && !empty($company)) ? $company : null;
        $statement->bind_param("sssssss", $username, $hashed_pwd, $email, $firstName, $lastName, $role, $companyOnlyIfSupplier);
        $statement->execute();
        if ($statement->errno === 0)
            $registrationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $registrationSuccessful;
}

function attemptPasswordUpdate($username, $currentPassword, $newPassword): bool
{
    $updateSuccessful = false;
    $connection = connectToDB();

    if ($currentPassword === $newPassword || !isUserPasswordCorrect($username, $currentPassword))
        return false;

    $hashedNewPwd = password_hash($newPassword, PASSWORD_BCRYPT);
    if ($connection->query("UPDATE cnUtente SET Password = '$hashedNewPwd' WHERE Username = '$username'"))
        $updateSuccessful = true;

    return $updateSuccessful;
}

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

/**
 * Ottiene la lista degli utenti, ritornando opzionalmente gli utenti che hanno nel nome, cognome o username
 * la stringa specificata.
 * @param string|null $nameFilter la stringa da usare come parametro di ricerca
 * @return array l'array dei risultati; null in caso di fallimento
 */
function getUsersList(string $nameFilter = null): array
{
    $connection = connectToDB();

    if (empty($nameFilter))
        $result = $connection->query("SELECT Username, Email, Nome, Cognome, Ruolo FROM cnUtente");
    else {
        if ($statement = $connection->prepare(
            "SELECT Nome, Cognome, Username, Email, Ruolo FROM cnUtente WHERE Nome LIKE ? OR Cognome LIKE ? OR Username LIKE ?"
        )) {
            $wildcardFilter = "%$nameFilter%";
            $statement->bind_param("sss", $wildcardFilter, $wildcardFilter, $wildcardFilter);
            $statement->execute();
            $result = $statement->get_result();
        }
    }

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function getStoresList(): array
{
    $connection = connectToDB();
    $result = $connection->query("SELECT ID_PuntoVendita AS Codice, NomePunto AS Nome FROM cnPuntoVendita");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function getProductEANsList(): array
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

function getSuppliersNames(): array
{
    $connection = connectToDB();
    $result = $connection->query("SELECT DISTINCT Azienda FROM cnUtente WHERE Azienda != NULL AND Ruolo = 'FOR'");

    if ($result == false)
        return null;
    else {
        $suppliersNames = [];
        while ($row = $result->fetch_row())
            $suppliersNames[] = $row[0];

        return $suppliersNames;
    }
}

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

function getProductInventory($storeId, $nameOrEanFilter = null): array
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
        }
    }

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
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