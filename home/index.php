<?php
require_once '../access/accessUtils.php';
require_once 'pageUtils.php';

checkAccessAndRedirectIfNeeded();
?>

<html lang="it">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title><?= getRoleName($_SESSION["role"]) . ' - ' . getMarketName() ?></title>


    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../lib/materialize/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body>
    <?php
    printNavbar($_SESSION["role"], $_SESSION["firstName"], $_SESSION["lastName"], $_POST["tab"]);
    printPageContent($_SESSION["role"]);
    ?>

<script type="text/javascript" src="../lib/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../lib/materialize/js/materialize.min.js"></script>
<script>
    // Inizializza le tab di Materialize se presenti
    $(document).ready(function() {
        tabs = $(".tabs");
        if (tabs != null)
            tabs.tabs();
    });
</script>
</body>
</html>
