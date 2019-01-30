<?php

function showLoader() {
    echo "<div class='full-width center-align'>
    <div class='preloader-wrapper active'>
        <div class='spinner-layer spinner-blue-only'>
            <div class='circle-clipper left'>
                <div class='circle'></div>
            </div>
            <div class='gap-patch'>
                <div class='circle'></div>
            </div>
            <div class='circle-clipper right'>
                <div class='circle'></div>
            </div>
        </div>
    </div>
</div>";
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Login - CassaNostra</title>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="../frameworks/materialize/css/materialize.min.css" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="styles.css" />
</head>

<body class="valign-wrapper">
    <?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    showLoader();
} else {
    header("Location: login.php");
    exit();
}

?>
    <!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="../frameworks/materialize/js/materialize.min.js"></script>
</body>

</html>