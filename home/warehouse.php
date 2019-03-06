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
    
    
    
    <div class="row">
    <nav class="nav-extended">
    <div class="nav-wrapper">
      <a class="brand-logo center">CassaNostra</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a>Esci</a></li>
      </ul>
    </div>
  </nav>

  
    <div class="col s12 m6">
      <div class="card blue-grey darken-1">
        <div class="card-content white-text">
          <span class="card-title">Punti fedeltà</span>
          <p>Per ogni euro speso all'interno di questo esercizio, riceverai un punto.</p>
        </div>
        <div class="card-action">
          <a href="#">This is a link</a>
          <a href="#">This is a link</a>
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