<?php
require_once '../access/accessUtils.php';
require_once '../utils/pageUtils.php';
require_once '../db/queries.php';

checkAccessAndRedirectIfNeeded();

$passwordChangeFailed = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "changePwd")
{
    if (!empty($_POST["newPwd"]))
        $passwordChangeFailed = !attemptPasswordUpdate($_SESSION["username"], $_POST["currentPwd"], $_POST["newPwd"]);
    else
        $passwordChangeFailed = true;
}

?>

<html lang="it">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title><?= getRoleName($_SESSION["role"]) . ' - ' . getMarketName() ?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../lib/materialize/css/materialize.min.css" media="screen,projection"/>

    <!-- Misc Materialize CSS overrides to enforce theming -->
    <style>
        .card-panel.centered {
            margin: .5rem auto 1rem;
        }

        .card-panel-title {
            font-size: 32px;
            font-weight: 300
        }

        form {
            margin: 0;
        }

        form .row {
            margin-bottom: 0;
        }

        nav .brand-logo {
            font-size: 1.8rem;
        }

        .fixed-action-btn {
            right: 32px;
            bottom: 32px;
        }

        .btn, .btn:hover, .btn-floating, .btn-floating:hover {
            background-color: #<?= getAccentColor() ?>;
        }

        .btn:hover, .btn-floating:hover {
            filter: brightness(115%);
        }

        .page-footer {
            background-color: #<?= getAccentColor() ?>;
        }

        .nav-extended {
            background-color: #<?= getAccentColor() ?>;
        }

        input:not(.browser-default):focus:not([readonly]) {
            border-bottom: 1px solid #<?= getAccentColor() ?> !important;
            box-shadow: 0 1px 0 0 #<?= getAccentColor() ?> !important;
        }

        input:not(.browser-default):focus:not([readonly]) + label {
            color: #<?= getAccentColor() ?> !important;
        }

        .select-wrapper input.select-dropdown:focus {
            border-bottom: 1px solid #<?= getAccentColor() ?>;
        }

        .dropdown-content li > a, .dropdown-content li > span {
            color: rgba(0,0,0,0.87);
        }
    </style>
</head>

<body>
    <?php
    printNavbar($_SESSION["role"], $_SESSION["firstName"], $_SESSION["lastName"], $_POST["tab"]);
    printPageContent($_SESSION["role"]);
    ?>

<script type="text/javascript" src="../lib/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../lib/materialize/js/materialize.min.js"></script>
<script>
    $(document).ready(function() {
        // Inizializza i componenti JS di Materialize
        $(".tabs").tabs();
        $(".modal").modal();
        $('select').formSelect();

        var elems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(elems, { coverTrigger: false });

        // Stampa messaggio di errore/riuscita del cambio password
        <?php
        if ($GLOBALS["passwordChangeFailed"] !== null)
        {
            $message = $GLOBALS["passwordChangeFailed"] ? "Aggiornamento della password fallito." : "Aggiornamento della password riuscito.";
            echo "M.toast({html: '$message'});";
        }
        ?>
    });
</script>
</body>
</html>
