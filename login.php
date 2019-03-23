<?php
require 'access/accessUtils.php';
require_once 'utils/pageUtils.php';

checkAccessAndRedirectIfNeeded();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Login - <?= getMarketName() ?></title>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="lib/materialize/css/materialize.min.css" media="screen,projection"/>

    <!-- Misc Materialize CSS overrides to enforce theming -->
    <style>
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

        .btn, .btn:hover {
            background-color: #<?= getAccentColor() ?>;
        }

        .btn:hover {
            filter: brightness(115%);
        }

        input:not(.browser-default):focus:not([readonly]) {
            border-bottom: 1px solid #<?= getAccentColor() ?> !important;
            box-shadow: 0 1px 0 0 #<?= getAccentColor() ?> !important;
        }

        input:not(.browser-default):focus:not([readonly]) + label {
            color: #<?= getAccentColor() ?> !important;
        }
    </style>
</head>

<body class="valign-wrapper" style="height: 100vh">
<div class="container">
    <div class="row">
        <div class="col s12 m6 offset-m3">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Login</span>
                    <form action="access/accessHandler.php" method="POST">
                        <div class="row">
                            <div class="col s12">
                                <p>Benvenuto in <?= getMarketName() ?>!<br>Accedi con le tue credenziali.</p>
                            </div>
                            <div class="input-field col s12">
                                <input id="username" name="username" type="text" required>
                                <label for="username">Username</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="password" name="password" type="password" required>
                                <label for="password">Password</label>
                                <p>Non hai un account? <a href="register.php">Registrati.</a></p>
                            </div>
                        </div>
                        <div class="right-align">
                            <button class="btn waves-effect waves-light" type="submit"
                                    name="loginAction">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="lib/materialize/js/materialize.min.js"></script>
</body>

</html>