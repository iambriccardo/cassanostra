<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../db/queries.php";
dieIfInvalidSessionOrRole("ADM");

$registrationFailed = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] == 1)
{
    if ($_POST["action"] == "register")
    {
        global $registrationFailed;

        $purifier = new HTMLPurifier();
        $firstName = $purifier->purify($_POST['firstName']);
        $lastName = $purifier->purify($_POST['lastName']);
        $email = $purifier->purify($_POST['email']);
        $username = $purifier->purify($_POST['username']);
        $role = $purifier->purify($_POST["role"]);

        if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($username) && !empty($role))
            $registrationFailed = !attemptRegistration($firstName, $lastName, $email, $username, $role);
        else
            $registrationFailed = true;
    }

    if ($registrationFailed !== null)
    {
        $message = $registrationFailed ? "Registrazione fallita." : "Registrazione riuscita.";
        echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$message'}))</script>";
    }
}
?>

<p>Utenti</p>

<div id="registerModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Registra un nuovo utente</h3>
            <div class="row">
                <div class="input-field col s6">
                    <input id="firstName" name="firstName" type="text" class="validate">
                    <label for="firstName">Nome</label>
                </div>
                <div class="input-field col s6">
                    <input id="lastName" name="lastName" type="text" class="validate">
                    <label for="lastName">Cognome</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <input id="username" name="username" type="text" class="validate">
                    <label for="username">Username</label>
                </div>
                <div class="input-field col s6">
                    <input id="email" name="email" type="text" class="validate">
                    <label for="email">Email</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <select id="role" name="role">
                        <option value="CLI" selected>Cliente</option>
                        <option value="MAG">Magazziniere</option>
                        <option value="DIR">Direttore</option>
                        <option value="CAS">Cassiere</option>
                        <option value="FOR">Fornitore</option>
                    </select>
                    <label for="role">Ruolo</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="modal-close waves-effect waves-green btn-flat">Registra</button>
        </div>

        <input type="hidden" name="tab" value="1">
        <input type="hidden" name="action" value="register">
    </form>
</div>

<div class="fixed-action-btn">
    <a class="btn-floating btn-large pulse" onclick="openRegisterModal()">
        <i class="large material-icons">add</i>
    </a>
</div>

<script>
    function openRegisterModal()
    {
        M.Modal.getInstance(document.getElementById("registerModal")).open();
    }
</script>