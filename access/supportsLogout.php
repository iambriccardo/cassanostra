<?php
/**
 * Questo script viene importato dalle pagine home dei vari tipi di utente, per far sì che aggiungendo ?logout all'URL
 * si proceda al logout.
 */

require_once 'accessUtils.php';

if (isset($_GET["logout"]))
    performLogout();