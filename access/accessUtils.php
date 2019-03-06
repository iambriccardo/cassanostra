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

    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        $redirectUrl = BASE_URL . "home/";
        switch ($_SESSION["role"])
        {
            case "MAG":
                $redirectUrl .= "warehouse.php";
                break;
            case "ADM":
                $redirectUrl .= "admin.php";
                break;
            case "DIR":
                $redirectUrl .= "manager.php";
                break;
            case "CLI":
                $redirectUrl .= "client.php";
                break;
            case "CAS":
                $redirectUrl .= "cashier.php";
                break;
            case "FOR":
                $redirectUrl .= "supplier.php";
                break;
        }

        // Evita il redirect quando si è già sulla pagina giusta
        if ($_SERVER["REQUEST_URI"] !== $redirectUrl) {
            header("Location: " . $redirectUrl);
            exit();
        }
    }
    else {
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
    session_start();
    session_destroy();
    checkAccessAndRedirectIfNeeded();
}