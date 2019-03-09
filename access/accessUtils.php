<?php

const BASE_URL = "/cassanostra/";

/**
 * Reindirizza l'utente alla login se non ha effettuato l'accesso, altrimenti lo reindirizza alla home relativa al suo ruolo.
 * Va chiamata in ogni pagina che richiede il login dell'utente.
 * @param string $fallbackPage La pagina al quale l'utente viene reindirizzato se non ha effettuato l'accesso
 */
function checkAccessAndRedirectIfNeeded($fallbackPage = "login.php")
{
    session_start();

    if (!empty($_SESSION['username']))
    {
        // Evita il redirect quando si è già sulla pagina giusta
        if ($_SERVER["REQUEST_URI"] !== BASE_URL . "home/index.php") {
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
 * Effettua il logout dell'utente
 */
function performLogout() {
    if (session_status() === PHP_SESSION_ACTIVE)
        session_destroy();

    checkAccessAndRedirectIfNeeded();
}