<?php

require_once __DIR__ . '/../config/configHandler.php';
require 'NavbarTab.php';

// Definisce le tab per i vari ruoli
$tabs = [
    "ADM" => [
        new NavbarTab("Personalizzazione", "customization.php"),
        new NavbarTab("Gestione utenti", "users.php"),
        new NavbarTab("Gestione punti vendita", "stores.php")
    ],
    "DIR" => [
        new NavbarTab("Statistiche", "stats.php"),
        new NavbarTab("Cassieri", "cashiers.php"),
        new NavbarTab("Bilancio generale", "report.php")
    ],
    "CAS" => [
        new NavbarTab("Registratore di cassa", "cashRegister.php"),
        new NavbarTab("Carte fedeltà", "fidelityCard.php")
    ],
    "CLI" => [
        new NavbarTab("Sommario", "summary.php")
    ],
    "MAG" => [
        new NavbarTab("Inventario prodotti", "inventory.php"),
        new NavbarTab("Inserimento fattura di acquisto", "purchaseInvoice.php")
    ]
];

/**
 * Stampa nella pagina il contenuto della navbar a seconda del ruolo dell'utente.
 * Se la home per quest'ultimo ha più tab, è possibile specificare quale tab viene automaticamente selezionata al caricamento della pagina.
 */
function printNavbar($userRole, $userFirstName, $userLastName, $selectedTab)
{
    global $tabs;

    // Barra principale
    $navbarHtml = '
    <nav class="nav-extended">
        <div class="nav-wrapper">
            <img alt="' . getMarketName() . '"
                 style="max-height: 64px; width: auto; padding: 8px;"
                 class="brand-logo left"
                 src="' . (file_exists(__DIR__ . "/../res/logo.png") ? '../res/logo.png' : '../res/default_logo.png') . '" />
            <a class="brand-logo center hide-on-small-and-down">' . getRoleName($userRole) . '</a>
            <ul id="nav-mobile" class="right">
                <li>
                    <a class="dropdown-trigger" data-target="user-dropdown">
                        <div class="valign-wrapper">
                            <i class="small material-icons">account_circle</i>
                            <span class="hide-on-small-and-down">' . "&emsp;$userFirstName $userLastName&nbsp;" . '</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>';

    // Tabs
    if (array_key_exists($userRole, $tabs)) {
        $navbarHtml .= '<div class="nav-content">
                        <ul class="tabs tabs-transparent">';

        $index = 0;
        foreach ($tabs[$userRole] as $tab) {
            $navbarHtml .= "<li class=\"tab\"><a ";
            if (!empty($selectedTab) && $selectedTab == $index)
                $navbarHtml .= 'class="active" ';

            $navbarHtml .= "href=\"#{$index}\">{$tab->name()}</a></li>";
            $index++;
        }
        $navbarHtml .= '</ul></div>';
    }

    $navbarHtml .= '</nav>';
    // Contenuto del dropdown dell'utente e della modal per il cambio della password
    $navbarHtml .= '<ul id="user-dropdown" class="dropdown-content">
                        <li><a onclick="M.Modal.getInstance(document.getElementById(\'pwdModal\')).open()">Cambia password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                    
                    <div id="pwdModal" class="modal">
                        <form method="post">
                            <div class="modal-content">
                                <h3>Cambia password</h3>
                                <div class="row">
                                    <div class="input-field col s12 m6">
                                        <input id="currentPwd" name="currentPwd" type="password" required>
                                        <label for="currentPwd">Password attuale</label>
                                    </div>
                                    <div class="input-field col s12 m6">
                                        <input id="newPwd" name="newPwd" type="password" required>
                                        <label for="newPwd">Nuova password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
                                <button type="submit" class="waves-effect waves-green btn-flat">Aggiorna</button>
                            </div>
                            <input type="hidden" name="action" value="changePwd">
                        </form>
                    </div>';
    echo $navbarHtml;
}

/**
 * Stampa il contenuto della pagina home relativa al ruolo utente specificato
 * @param $userRole string Il ruolo dell'utente
 */
function printPageContent($userRole)
{
    global $tabs;
    switch ($userRole) {
        case "MAG":
            $redirectUrl = "warehouse";
            break;
        case "ADM":
            $redirectUrl = "admin";
            break;
        case "DIR":
            $redirectUrl = "manager";
            break;
        case "CLI":
            $redirectUrl = "client";
            break;
        case "CAS":
            $redirectUrl = "cashier";
            break;
        case "FOR":
            $redirectUrl = "supplier";
            break;
    }

    // Il contenuto delle singole tab viene caricato tutto assieme, poi Materialize mostrerà soltanto il div della tab corrispondente.
    // Il codice JavaScript che inizializza le tab è in home/summary.php. Per maggiori dettagli: materializecss.com/tabs.html
    if (array_key_exists($userRole, $tabs)) {
        $index = 0;
        foreach ($tabs[$userRole] as $tab) {
            echo "<div id=\"$index\">";
            require "{$redirectUrl}/{$tab->page()}";
            echo "</div>";
            $index++;
        }
    } else
        require "{$redirectUrl}/index.php";
}

/**
 * Ritorna il colore del tema di default NON preceduto da #
 */
function getAccentColor(): string
{
    global $config;
    return $config["accentColor"];
}

function getMarketName(): string
{
    global $config;
    return $config["marketName"];
}

function getRoleName($userRole): string
{
    switch ($userRole) {
        case "MAG":
            return "Magazzino";
        case "ADM":
            return "Amministrazione";
        case "DIR":
            return "Direttore";
        case "CLI":
            return "Cliente";
        case "CAS":
            return "Cassiere";
        case "FOR":
            return "Fornitore";
        default:
            return "?";
    }
}