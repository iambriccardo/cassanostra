<?php
require_once '../access/loginUtils.php';

session_start();

//checkAccessAndRedirectIfNeeded();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Home - CassaNostra</title>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="../frameworks/materialize/css/materialize.min.css" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="styles.css" />
</head>

<body>



    <div class="row">
        <nav class="nav-extended  blue-grey darken-4">
            <div class="nav-wrapper">
                <a class="brand-logo center">CassaNostra</a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a>Esci</a></li>
                </ul>
            </div>
        </nav>


        <div class="col s12 m12 l6 center offset-l3">
            <div class="card grey lighten-5">
                <div class="card-content black-text">
                    <span class="card-title">Inserimento prodotti nel magazzino</span>
                    <div class="row">
                        <div class="input-field col s12">
                            <select>
                                <option value="" disabled selected>Scegli il punto vendita dove vuoi operare</option>
                                <option value="1">Spini di Gardolo</option>
                                <option value="2">Montevaccino</option>
                                <option value="3">Canova</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m12 l6 center offset-l3">
            <table class="striped highlight responsive-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Marca</th>
                        <th>Prezzo</th>
                        <th>Quantita'</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Alvin</td>
                        <td>Eclair</td>
                        <td>$0.87</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>Alan</td>
                        <td>Jellybean</td>
                        <td>$3.76</td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>Jonathan</td>
                        <td>Lollipop</td>
                        <td>$7.00</td>
                        <td>7 miliardi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="fixed-action-btn">
        <a class="btn-floating btn-large blue-grey darken-4">
            <i class="large material-icons">add</i>
        </a>
        <ul>
            <li><a class="btn-floating red"><i class="material-icons">insert_chart</i></a></li>
            <li><a class="btn-floating yellow darken-1"><i class="material-icons">format_quote</i></a></li>
            <li><a class="btn-floating green"><i class="material-icons">publish</i></a></li>
            <li><a class="btn-floating blue"><i class="material-icons">attach_file</i></a></li>
        </ul>
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
    <script type="text/javascript" src="../frameworks/jquery/jquery-3.3.1.min.js">
    </script>

    <script type="text/javascript" src="../frameworks/materialize/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            var instances = M.FormSelect.init(elems, options);
        });

        // Or with jQuery

        $(document).ready(function() {
            $('select').formSelect();
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.fixed-action-btn');
            var instances = M.FloatingActionButton.init(elems, options);
        });

        // Or with jQuery

        $(document).ready(function() {
            $('.fixed-action-btn').floatingActionButton();
        });

    </script>

</body>

</html>
