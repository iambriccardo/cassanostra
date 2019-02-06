<?php
session_start();

function showLoader()
{
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

function handleLogin()
{
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($username) && !empty($password)) {
            $_SESSION['username'] = $username;
            header("Location: ../home/home.php");
        }
    }
}

function handleRegistration()
{
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (!empty($username) && !empty($password) && !empty($role)) {
            $_SESSION['username'] = $username;
            header("Location: ../home/home.php");
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Login - CassaNostra</title>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="../frameworks/materialize/css/materialize.min.css"
          media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body class="valign-wrapper">
<?php
require_once 'loginUtils.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    showLoader();

    if (isset($_POST['loginAction']) && !empty($_POST['loginAction'])) {
        handleLogin();
    } else if (isset($_POST['registrationAction']) && !empty($_POST['registrationAction'])) {
        handleRegistration();
    }
} else {
    checkAccessAndRedirect("../home/home.php", "login.php");
    exit();
}

?>
<!--JavaScript at end of body for optimized loading-->
<script type="text/javascript" src="../frameworks/materialize/js/materialize.min.js"></script>
</body>

</html>