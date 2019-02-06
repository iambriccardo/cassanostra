<?php
require 'loginUtils.php';

session_start();

checkAccessAndRedirect("../home/home.php", NULL);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Registrati - CassaNostra</title>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="../frameworks/materialize/css/materialize.min.css"
          media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body class="valign-wrapper">
<div class="container">
    <div class="row">
        <div class="col s6 offset-s3">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Login</span>
                    <form action="accessHandler.php" method="POST">
                        <div class="row">
                            <div class="col s12">
                                <p>Benvenuto in CassaNostra, registrati per accedere al servizio.</p>
                            </div>
                            <div class="input-field col s12">
                                <input id="username" name="username" type="text" class="validate">
                                <label for="username">Nome utente</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="password" name="password" type="password" class="validate">
                                <label for="password">Password</label>
                            </div>
                            <div class="input-field col s12">
                                <select name="role">
                                    <option value="" disabled selected>Scegli un ruolo</option>
                                    <option value="MAG">Magazziniere</option>
                                    <option value="DIR">Direttore</option>
                                    <option value="CLI">Cliente</option>
                                    <option value="CAS">Cassiere</option>
                                    <option value="FOR">Fornitore</option>
                                </select>
                                <label>Materialize Select</label>
                            </div>
                        </div>
                        <div class="right-align">
                            <button class="btn waves-effect waves-light  indigo accent-4" type="submit"
                                    name="registrationAction">
                                Registrati
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--JavaScript at end of body for optimized loading-->
<script type="text/javascript" src="../frameworks/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../frameworks/materialize/js/materialize.min.js"></script>
<script>
    $(document).ready(function () {
        $('select').formSelect();
    });
</script>
</body>

</html>