<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../db/queries.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
dieIfInvalidSessionOrRole("ADM");

$registrationFailed = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "1") {
    if ($_POST["action"] == "register") {
        $purifier = new HTMLPurifier();
        $firstName = $purifier->purify($_POST['firstName']);
        $lastName = $purifier->purify($_POST['lastName']);
        $email = $purifier->purify($_POST['email']);
        $username = $purifier->purify($_POST['username']);
        $role = $purifier->purify($_POST["role"]);
        $company = $purifier->purify($_POST["company"]);

        if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($username) && !empty($role))
            $registrationFailed = !attemptRegistration($firstName, $lastName, $email, $username, $role, $company);
        else
            $registrationFailed = true;
    }

    if ($registrationFailed !== null) {
        $message = $registrationFailed ? "Registrazione fallita." : "Registrazione riuscita.";
        echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$message'}))</script>";
    }
}

$listData = [];
$nameFilter = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "1" && $_POST["action"] == "list") {
    $purifier = new HTMLPurifier();
    $nameFilter = $purifier->purify($_POST['nameFilter']);
    $listData = getUsersList($nameFilter);
} else
    $listData = getUsersList();

?>

<div class="card-panel container centered">
    <span class="card-panel-title">Visualizza utenti</span>
    <form method="post">
        Filtra per nome:
        <div class="input-field inline" style="vertical-align: unset">
            <input id="nameFilter" name="nameFilter" type="text" value="<?= $nameFilter ?>">
        </div>
        <button class="btn waves-effect waves-light" type="submit" style="margin-left: 32px">Aggiorna</button>

        <input type="hidden" name="tab" value="1">
        <input type="hidden" name="action" value="list">
    </form>

    <? printHtmlTableFromAssocArray($listData) ?>
</div>

<div id="registerModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Registra un nuovo utente</h3>
            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="firstName" name="firstName" type="text" required>
                    <label for="firstName">Nome</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="lastName" name="lastName" type="text" required>
                    <label for="lastName">Cognome</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="username" name="username" type="text" required>
                    <label for="username">Username</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="email" name="email" type="email" required>
                    <label for="email">Email</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <select id="role" name="role" required onchange="showOrHideCompanyField()">
                        <option value="CLI" selected>Cliente</option>
                        <option value="MAG">Magazziniere</option>
                        <option value="DIR">Direttore</option>
                        <option value="CAS">Cassiere</option>
                        <option value="FOR">Fornitore</option>
                    </select>
                    <label for="role">Ruolo</label>
                </div>
            </div>
            <div id="companyRow" class="row hidden">
                <div class="input-field col s12">
                    <input type="text" name="company" id="company">
                    <label for="company">Azienda</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="waves-effect waves-green btn-flat">Registra</button>
        </div>

        <input type="hidden" name="tab" value="1">
        <input type="hidden" name="action" value="register">
    </form>
</div>

<div class="fixed-action-btn">
    <a class="btn-floating btn-large pulse" onclick="M.Modal.getInstance(document.getElementById('registerModal')).open()">
        <i class="large material-icons">add</i>
    </a>
</div>

<script>
    function showOrHideCompanyField()
    {
        if (document.getElementById("role").value === "FOR")
            document.getElementById("companyRow").classList.remove('hidden');
        else
            document.getElementById("companyRow").classList.add('hidden');
    }
</script>