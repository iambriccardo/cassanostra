<?php
require '../access/loginUtils.php';

session_start();

checkAccessAndRedirect(NULL, "../access/login.php");
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

<body class="valign-wrapper">
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