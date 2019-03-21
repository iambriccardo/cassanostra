<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../db/queries.php";
dieIfInvalidSessionOrRole("CAS");

if (isset($_POST["registerFidelityCard"])) {
    $hasAccount = isset($_POST["hasAccount"]) && $_POST["hasAccount"] == "true";
    $resultMessage = "Carta fedeltà creata con successo!";

    $purifier = new HTMLPurifier();
    $username = $purifier->purify($_POST["username"]);

    if (!$hasAccount) {
        $firstName = $purifier->purify($_POST['firstName']);
        $lastName = $purifier->purify($_POST['lastName']);
        $email = $purifier->purify($_POST['email']);
        $role = "CLI";

        if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($username) && !empty($role))
            if (!attemptRegistration($firstName, $lastName, $email, $username, $role))
                $resultMessage = "Errore durante la registrazione.";
    }

    if (!createFidelityCard($username))
        $resultMessage = "Errore durante la creazione della carta fedeltà.";

    if (!empty($resultMessage)) {
        echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$resultMessage'}))</script>";
    }
}

if ($_POST["hasAccount"]) {
    $script = "<script>
               document.addEventListener('DOMContentLoaded', function() {
                   M.Autocomplete.init(document.getElementById('username'), {
                       data: {;\n";
    foreach (getUsersList() as $user)
        $script .= $user["Username"] . ": null,\n";
    $script .= "}
                   });
               });
              </script>";

    echo $script;
}
?>

<div class="card-panel container centered">
    <span class="card-panel-title">Registra nuova carta fedeltà</span>
    <form method="POST">
        <div class="row">
            <div class="input-field col s12">
                <input id="username" name="username" type="text" class="autocomplete validate" autocomplete="off">
                <label for="username">Username</label>
                <p>
                    <label>
                        <input type="checkbox" name="hasAccount" value="true"
                               onClick="this.form.submit()" <?= $_POST["hasAccount"] ? 'checked="checked"' : "" ?> />
                        <span>Utente già registrato</span>
                    </label>
                </p>
            </div>
            <?php
            if (!isset($_POST["hasAccount"])) {
                echo '<div class="input-field col s12">
                    <input id="firstName" name="firstName" type="text" class="validate">
                    <label for="firstName">Nome</label>
                </div>
                <div class="input-field col s12">
                    <input id="lastName" name="lastName" type="text" class="validate">
                    <label for="lastName">Cognome</label>
                </div>
                <div class="input-field col s12">
                    <input id="email" name="email" type="email" class="validate">
                    <label for="email">Email</label>
                </div>';
            }
            ?>
        </div>
        <div class="right-align">
            <button class="btn waves-effect waves-light" type="submit" name="registerFidelityCard">Registra</button>
        </div>

        <input type="hidden" name="tab" value="1">
    </form>
</div>
