<?php
require_once '../access/loginUtils.php';

session_start();

//checkAccessAndRedirectIfNeeded();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Home - CassaNostra</title>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="../frameworks/materialize/css/materialize.min.css"
          media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body>
<nav class="nav-extended blue-grey darken-4">
    <div class="nav-wrapper">
        <a class="brand-logo center" href="#">Cassanostra</a>
    </div>
</nav>

<div class="row">
    <div class="col s12 m3">
        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="col s12 m6 offset-m3">
                        <img src="../res/fidelity_card.png">
                    </div>
                    <div class="col s12 m12">
                        <span class="card-title">Fidelity card</span>
                    </div>

                    <div class="col s12 m12">
                        <p>Card no. 23546567</p>
                        <p>Saldo 30$</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col s12 m9">
        <div class="card">
            <div class="card-content">
                <div class="row">

                </div>
            </div>
        </div>
    </div>
</div>
<?php

if (isset($_SESSION['username'])) {
    if (!empty($_SESSION['username'])) {
        $username = $_SESSION['username'];

        echo "You are logged in as ${username}";
    }
}

?>
<!--JavaScript at end of body for optimized loading-->
<script type="text/javascript" src="../frameworks/materialize/js/materialize.min.js"></script>
</body>
</html>