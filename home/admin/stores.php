<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../db/queries.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
dieIfInvalidSessionOrRole("ADM");

$additionFailed = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "2")
{
    if ($_POST["action"] === "addStore")
    {
        $purifier = new HTMLPurifier();
        $storeName = $purifier->purify($_POST["storeName"]);

        if (!empty($storeName))
            $additionFailed = !addNewStore($storeName);
        else
            $additionFailed = true;
    }

    if ($additionFailed !== null)
    {
        $message = $additionFailed ? "Aggiunta fallita." : "Aggiunta riuscita.";
        echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$message'}))</script>";
    }
}

$listData = getStoresList();
?>

<div class="card-panel container centered">
    <span class="card-panel-title">Visualizza punti vendita</span>

    <? printHtmlTableFromAssocArray($listData) ?>
</div>

<div id="addStoreModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Registra un nuovo punto vendita</h3>
            <div class="row">
                <div class="input-field col s12">
                    <input id="storeName" name="storeName" type="text" class="validate">
                    <label for="storeName">Nome negozio</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="modal-close waves-effect waves-green btn-flat">Aggiungi</button>
        </div>

        <input type="hidden" name="tab" value="2">
        <input type="hidden" name="action" value="addStore">
    </form>
</div>

<div class="fixed-action-btn">
    <a class="btn-floating btn-large pulse" onclick="openAddStoreModal()">
        <i class="large material-icons">add</i>
    </a>
</div>

<script>
    function openAddStoreModal()
    {
        M.Modal.getInstance(document.getElementById("addStoreModal")).open();
    }
</script>