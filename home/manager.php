<?php
require_once 'pageUtils.php';
require_once '../access/supportsLogout.php';
require_once '../access/accessUtils.php';

//checkAccessAndRedirectIfNeeded();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Direttore - <?= getMarketName() ?></title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../lib/materialize/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body>
    <? printNavbar() ?>

<?php

    if (isset($_SESSION['username'])) {
        if (!empty($_SESSION['username'])) {
            $username = $_SESSION['username'];

            echo "You are logged in as ${username}";
        }
    }

?>
<!--JavaScript at end of body for optimized loading-->
<script type="text/javascript" src="../lib/materialize/js/materialize.min.js"></script>
</body>
</html>