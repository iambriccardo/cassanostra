<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../queries/stores.php";
require_once __DIR__ . "/../../lib/htmlpurifier/HTMLPurifier.standalone.php";
dieIfInvalidSessionOrRole("CAS");

$selectedStoreId = null;
$cashiersForSelectedStore = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "0")
{
    $purifier = new HTMLPurifier();
    if ($_POST["action"] === "selectCashier")
    {
        $selectedStoreId = intval($purifier->purify($_POST["store"]));
        if (!empty($selectedStoreId))
             $cashiersForSelectedStore = getCashiersForStore($selectedStoreId);

        if (isset($_POST["openCashier"]))
        {
            $selectedCashier = intval($purifier->purify($_POST["cashier"]));
            if (!empty($selectedCashier))
                $_SESSION['currentOpenCashier'] = $selectedCashier;
        }
    }
}

?>

<?php if (!empty($_SESSION['currentOpenCashier'])) : ?>

<p>ID cassa selezionata: <?= $_SESSION['currentOpenCashier'] ?></p>

<?php else: ?>

<!-- Scelta della cassa da aprire -->
<div class="card-panel container centered">
    <span class="card-panel-title">Apri cassa</span>
    <form method="post">
        <div class="row">
            <div class="col s12 m4">
                Punto vendita:
                <div class="input-field inline" style="vertical-align: unset">
                    <select id="store" name="store" required onchange="this.form.submit()">
                        <?php
                        if ($selectedStoreId == null)
                            echo "<option disabled selected>Seleziona</option>";

                        foreach (getStoresList() as $store)
                        {
                            $selectedString = $selectedStoreId == $store['Codice'] ? "selected" : "";
                            echo "<option value=\"{$store['Codice']}\" $selectedString>{$store['Nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col s12 m6">
                Numero cassa:
                <div class="input-field inline" style="vertical-align: unset">
                    <select id="cashier" name="cashier" <?= ($selectedStoreId == null ? "disabled" : "") ?> required>
                        <option disabled selected>Seleziona</option>
                        <?php
                        if ($selectedStoreId !== null)
                        {
                            foreach ($cashiersForSelectedStore as $cashier)
                                echo "<option value=\"{$cashier['ID_Cassa']}\">{$cashier['NumeroCassa']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn waves-effect waves-light" name="openCashier" style="margin-left: 96px">Apri cassa</button>
            </div>
        </div>

        <input type="hidden" name="tab" value="0">
        <input type="hidden" name="action" value="selectCashier">
    </form>
</div>

<?php endif; ?>
