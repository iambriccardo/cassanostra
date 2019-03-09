<?php

require_once __DIR__ . '/../config/configHandler.php';

// TODO tabs
function printNavbar($userRole)
{
    echo '
    <nav class="nav-extended" style="background-color: ' . getAccentColor() . '">
        <div class="nav-wrapper">
            <a class="brand-logo left">' . getMarketName() . '</a>
            <a class="brand-logo center">' . getRoleName($userRole) . '</a>
            <ul id="nav-mobile" class="right">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    ';
}

function getAccentColor() : string
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
    switch ($userRole)
    {
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