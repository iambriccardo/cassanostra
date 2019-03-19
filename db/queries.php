<?php
/**
 * @file
 * I metodi in questo file richiedono che i dati passati per parametro non siano vuoti e che
 * siano già filtrati da possibili attacchi XSS.
 * I prepared statement vengono utilizzati in tutte le casistiche in cui vi è la possibilità di attacchi SQL injection.
 */

function connectToDB($host = "localhost", $username = "qvanto", $password = "", $dbName = "my_qvanto") : mysqli
{
    $connection = new mysqli($host, $username, $password, $dbName);

    if ($connection->connect_error) {
        die('Errore di connessione (' . $connection->connect_errno . ') '
            . $connection->connect_error);
    }

    return $connection;
}

function isUserPasswordCorrect($username, $password) : bool
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

function attemptLogin($username, $password) : bool
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

function attemptRegistrationAndLogin($firstName, $lastName, $email, $username, $password, $role) : bool
{
    $isAllowed = false;

    $registrationSuccessful = attemptRegistration($firstName, $lastName, $email, $username, $role, $password);
    if ($registrationSuccessful)
        $isAllowed = attemptLogin($username, $password);

    return $isAllowed;
}

function attemptRegistration($firstName, $lastName, $email, $username, $role, $password = "cambiami") : bool
{
    $registrationSuccessful = false;
    $connection = connectToDB();
    $hashed_pwd = password_hash($password, PASSWORD_BCRYPT);

    if ($statement = $connection->prepare("INSERT INTO cnUtente (Username, Password, Email, Nome, Cognome, Ruolo) VALUES (?, ?, ?, ?, ?, ?)"))
    {
        $statement->bind_param("ssssss", $username, $hashed_pwd, $email, $firstName, $lastName, $role);
        $statement->execute();
        if ($statement->errno === 0)
            $registrationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $registrationSuccessful;
}

function attemptPasswordUpdate($username, $currentPassword, $newPassword) : bool
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

function addNewStore($storeName) : bool
{
    $additionSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnPuntoVendita (NomePunto) VALUES (?)"))
    {
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
function getUsersList(string $nameFilter = null) : array
{
    $connection = connectToDB();

    if (empty($nameFilter))
        $result = $connection->query("SELECT Username, Email, Nome, Cognome, Ruolo FROM cnUtente");
    else
    {
        if ($statement = $connection->prepare(
            "SELECT Username, Email, Nome, Cognome, Ruolo FROM cnUtente WHERE Nome LIKE ? OR Cognome LIKE ? OR Username LIKE ?"
        ))
        {
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

function getStoresList() : array
{
    $connection = connectToDB();
    $result = $connection->query("SELECT ID_PuntoVendita AS Codice, NomePunto AS Nome FROM cnPuntoVendita");

    if ($result == false)
        return null;
    else
        return $result->fetch_all(MYSQLI_ASSOC);
}

function createFidelityCard(string $username, int $points = 0) : bool {
    $creationSuccessful = false;
    $connection = connectToDB();

    if ($statement = $connection->prepare("INSERT INTO cnCartaFedelta (SaldoPunti, FK_Utente) VALUES (?, ?)"))
    {
        $statement->bind_param("is", $points, $username);
        $statement->execute();
        if ($statement->errno === 0)
            $creationSuccessful = true;

        $statement->close();
    }

    $connection->close();
    return $creationSuccessful;
}

/**
 * Genera il codice per la tabella HTML dato il risultato di una query sotto forma di array associativo.
 * Le intestazioni delle colonne sono stampate utilizzando array_keys, pertanto è importante che l'array sia il
 * risultato di una chiamata a mysqli_result::fetch_all passando come parametro MYSQLI_ASSOC.
 * @param array $assocResultArray risultato della query
 * @param string $htmlClasses (facoltativo) le classi HTML usate per stilizzare la tabella
 * @return string HTML da stampare nella pagina
 */
function generateTableHtmlFromQueryResult(array $assocResultArray, string $htmlClasses = "responsive-table striped") : string
{
    // TODO
}
