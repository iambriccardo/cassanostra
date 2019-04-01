<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../queries/stores.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
dieIfInvalidSessionOrRole("ADM");

$message = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "2")
{
    if ($_POST["action"] === "addStore")
    {
        $purifier = new HTMLPurifier();
        $storeName = $purifier->purify($_POST["storeName"]);
        $registersAmount = intval($_POST["registersAmount"]);

        if (!empty($storeName))
        {
            $storeId = addNewStore($storeName);
            if ($storeId !== 0)
            {
                if ($registersAmount > 0 && $registersAmount <= 50)
                {
                    $registerAdditionsOk = addCashRegistersToStore($storeId, $registersAmount);
                    if ($registerAdditionsOk)
                        $message = "Aggiunta riuscita.";
                    else
                        $message = "Inserimento delle casse fallito.";
                }
                else
                    $message = "Numero di casse fuori dal range consentito.";
            }
            else
                $message = "Aggiunta punto vendita fallito.";
        }
    }
    else if ($_POST["action"] === "addRegisters")
    {
        $storeId = intval($_POST["store"]);
        $registersAmount = intval($_POST["registersAmount"]);

        if (!empty($storeId))
        {
            if ($registersAmount > 0 && $registersAmount <= 50)
            {
                $registerAdditionsOk = addCashRegistersToStore($storeId, $registersAmount);
                if ($registerAdditionsOk)
                    $message = "Aggiunta riuscita.";
                else
                    $message = "Aggiunta delle casse fallita.";
            }
            else
                $message = "Numero di casse fuori dal range consentito.";
        }
    }
}

if ($message !== null)
    echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$message'}))</script>";
?>

<div class="card-panel container centered">
    <span class="card-panel-title">Punti vendita</span>

    <? printHtmlTableFromAssocArray(getStoresList()) ?>
</div>

<div id="addStoreModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Registra un nuovo punto vendita</h3>
            <div class="row">
                <div class="input-field col s12 m8">
                    <input id="storeName" name="storeName" type="text" required>
                    <label for="storeName">Nome negozio</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="registersAmount" name="registersAmount" type="number" required>
                    <label for="registersAmount">Numero di casse</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="waves-effect waves-green btn-flat">Aggiungi</button>
        </div>

        <input type="hidden" name="tab" value="2">
        <input type="hidden" name="action" value="addStore">
    </form>
</div>

<div id="addRegistersModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Aggiungi casse ad un punto vendita esistente</h3>
            <div class="row">
                <div class="input-field col s12 m6">
                    Punto vendita:
                    <div class="input-field inline" style="vertical-align: unset">
                        <select id="store" name="store" required">
                            <?php
                            if ($GLOBALS["selectedStoreId"] == null)
                                echo "<option disabled selected>Seleziona</option>";

                            foreach (getStoresList() as $store)
                                echo "<option value=\"{$store['Codice']}\">{$store['Nome']}</option>";
                            ?>
                        </select>
                    </div>
                </div>
                <div class="input-field col s12 m6">
                    <div class="input-field inline">
                        <input id="registersAmount" name="registersAmount" type="number" required>
                        <label for="registersAmount">Numero di casse</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="waves-effect waves-green btn-flat">Aggiungi</button>
        </div>

        <input type="hidden" name="tab" value="2">
        <input type="hidden" name="action" value="addRegisters">
    </form>
</div>

<div class="fixed-action-btn">
    <a class="btn-floating btn-large pulse">
        <i class="large material-icons">add</i>
    </a>
    <ul>
        <li><a class="btn-floating white" onclick="openAddCashRegistersModal()"><i class="material-icons grey-text text-darken-4">local_atm</i></a></li>
        <li><a class="btn-floating white" onclick="openAddStoreModal()"><i class="material-icons grey-text text-darken-4">store</i></a></li>
    </ul>
</div>

<script>
    function openAddStoreModal() {
        M.Modal.getInstance(document.getElementById("addStoreModal")).open();
    }

    function openAddCashRegistersModal() {
        M.Modal.getInstance(document.getElementById("addRegistersModal")).open();
    }
</script>