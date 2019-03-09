<?php
require_once '../access/accessUtils.php';
require_once 'pageUtils.php';

checkAccessAndRedirectIfNeeded();
?>

<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title><?= getRoleName($_SESSION["role"]) . ' - ' . getMarketName() ?></title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../lib/materialize/css/materialize.min.css" media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body>
    <?php
    printNavbar($_SESSION["role"]);
    switch ($_SESSION["role"])
    {
        case "MAG":
            $redirectUrl .= "warehouse";
            break;
        case "ADM":
            $redirectUrl .= "admin";
            break;
        case "DIR":
            $redirectUrl .= "manager";
            break;
        case "CLI":
            $redirectUrl .= "client";
            break;
        case "CAS":
            $redirectUrl .= "cashier";
            break;
        case "FOR":
            $redirectUrl .= "supplier";
            break;
    }
    require "{$redirectUrl}/index.php";
    ?>

<script type="text/javascript" src="../lib/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../lib/materialize/js/materialize.min.js"></script>
</body>
</html>
