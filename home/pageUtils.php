<?php

require_once '../config/configHandler.php';

// TODO tabs
function printNavbar($userRole)
{
    echo '
    <nav class="nav-extended" style="background-color: ' . getAccentColor() . '">
        <div class="nav-wrapper">
            <a class="brand-logo center">' . getMarketName() . '</a>
            <ul id="nav-mobile" class="right">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    ';
}

function getAccentColor()
{
    global $config;
    return $config["accentColor"];
}

function getMarketName()
{
    global $config;
    return $config["marketName"];
}