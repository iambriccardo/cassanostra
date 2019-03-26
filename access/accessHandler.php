<?php
require_once '../queries/users.php';
require_once "../lib/htmlpurifier/HTMLPurifier.standalone.php";

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
    if (isset($_POST['username']) && isset($_POST['password']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($username) && !empty($password))
            attemptLogin($username, $password);

        checkAccessAndRedirectIfNeeded();
    }
}

function handleClientRegistration()
{
    if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']))
    {
        $purifier = new HTMLPurifier();
        $firstName = $purifier->purify($_POST['firstName']);
        $lastName = $purifier->purify($_POST['lastName']);
        $email = $purifier->purify($_POST['email']);
        $username = $purifier->purify($_POST['username']);
        $role = "CLI";

        if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($username) && !empty($password))
            attemptRegistrationAndLogin($firstName, $lastName, $email, $username, $password, $role);

        checkAccessAndRedirectIfNeeded("register.php");
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Login - CassaNostra</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../lib/materialize/css/materialize.min.css" media="screen,projection"/>
</head>

<body class="valign-wrapper">
<?php
require_once 'accessUtils.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    showLoader();

    if (isset($_POST['loginAction'])) {
        handleLogin();
    } else if (isset($_POST['registrationAction'])) {
        handleClientRegistration();
    }
} else {
    checkAccessAndRedirectIfNeeded();
}

?>
<script type="text/javascript" src="../lib/materialize/js/materialize.min.js"></script>
</body>

</html>