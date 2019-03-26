<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../queries/products.php";
require_once __DIR__ . "/../../queries/stores.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
dieIfInvalidSessionOrRole("MAG");

$listData = [];
$nameOrEanFilter = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "0" && $_POST["action"] == "inventoryList") {
    $purifier = new HTMLPurifier();
    $storeId = $purifier->purify($_POST['store']);
    $nameOrEanFilter = $purifier->purify($_POST['nameOrEanFilter']);
    $listData = getProductInventory($storeId, $nameOrEanFilter);
}
else
    $listData = getProductInventory(1);
?>

<div class="card-panel container centered">
    <span class="card-panel-title">Visualizza inventario</span>
    <form method="post">
        <div class="row">
            <div class="col s12 m4">
                Punto vendita:
                <div class="input-field inline" style="vertical-align: unset">
                    <select id="store" name="store" required>
                        <?php
                        foreach (getStoresList() as $store)
                        {
                            $selectedString = $storeId === $store['Codice'] ? "selected" : "";
                            echo "<option value=\"{$store['Codice']}\" $selectedString>{$store['Nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col s12 m6">
                Filtra per nome o codice a barre:
                <div class="input-field inline" style="vertical-align: unset">
                    <input id="nameOrEanFilter" name="nameOrEanFilter" type="text" value="<?= $nameOrEanFilter ?>">
                </div>
                <button class="btn waves-effect waves-light" type="submit" style="margin-left: 32px">Aggiorna</button>
            </div>
        </div>

        <input type="hidden" name="tab" value="0">
        <input type="hidden" name="action" value="inventoryList">
    </form>

    <? printHtmlTableFromAssocArray($listData) ?>
</div>
