<?php

const BASE_URL = "/cassanostra/";

/**
 * Reindirizza l'utente alla login se non ha effettuato l'accesso, altrimenti lo reindirizza alla home relativa al suo ruolo.
 * Inoltre, qualora l'utente stesse usando HTTP, lo porta alla versione HTTPS della stessa pagina.
 * @param string $fallbackPage La pagina al quale l'utente viene reindirizzato se non ha effettuato l'accesso
 */
function checkAccessAndRedirectIfNeeded($fallbackPage = "login.php")
{
    // Se l'utente si sta connettendo con HTTP, reindirizzalo sulla versione HTTPS della pagina corrente
    if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
    {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit();
    }

    session_start();

    if (!empty($_SESSION['username']))
    {
        // Il controllo dell'URL tiene conto di eventuali parametri GET
        $homeUrlWithGetParams = BASE_URL . "home/index.php" . (!empty($_SERVER["QUERY_STRING"]) ? "?{$_SERVER["QUERY_STRING"]}" : "");
        if ($_SERVER["REQUEST_URI"] !== $homeUrlWithGetParams) {
            header("Location: " . BASE_URL . "home/index.php");
            exit();
        }
    }
    else
    {
        if ($_SERVER["REQUEST_URI"] !== BASE_URL . $fallbackPage) {
            header("Location: " . BASE_URL . $fallbackPage);
            exit();
        }
    }
}

/**
 * Termina l'esecuzione dello script in cui è chiamato se la sessione non è attiva o il ruolo dell'utente non corrisponde
 * a quello richiesto.
 * Viene utilizzato nelle sottopagine della home degli utenti, in modo che il loro contenuto non possa essere mostrato
 * quando non sono caricate all'interno dello script home/index.php.
 * @param $expectedRole string Ruolo richiesto
 */
function dieIfInvalidSessionOrRole($expectedRole)
{
    if (session_status() !== PHP_SESSION_ACTIVE || $_SESSION["role"] !== $expectedRole)
        die();
}

/**
 * Effettua il logout dell'utente
 */
function performLogout() {
    if (session_status() === PHP_SESSION_ACTIVE)
        session_destroy();

    checkAccessAndRedirectIfNeeded();
}