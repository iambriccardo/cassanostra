<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../queries/products.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
dieIfInvalidSessionOrRole("ADM");

$successOrErrorMessage = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "3")
{
    $purifier = new HTMLPurifier();
    // Aggiunta nuovo prodotto
    if ($_POST["action"] === "newProduct")
    {
        $productName = $purifier->purify($_POST["name"]);
        $productBrand = $purifier->purify($_POST["brand"]);
        $eanCode = $purifier->purify($_POST["barcode"]);
        $sellPrice = floatval(str_replace(',', '.', $_POST["sellPrice"]));

        if (!empty($productName) && !empty($productBrand) && !empty($eanCode))
        {
            if (registerNewProduct($productName, $productBrand, $eanCode, $sellPrice))
                $successOrErrorMessage = "Registrazione del prodotto riuscita.";
            else
                $successOrErrorMessage = "Registrazione del prodotto fallita.";
        }
        else
            $successOrErrorMessage = "Registrazione fallita: dati del prodotto mancanti.";
    }
    // Modifica del prezzo
    else if ($_POST["action"] === "editPrice")
    {
        $eanCode = $purifier->purify($_POST["productCode"]);
        $sellPrice = floatval(str_replace(',', '.', $_POST["sellPrice"]));

        $updateSuccessful = updateProductPrice($eanCode, $sellPrice);
        if ($updateSuccessful)
            $successOrErrorMessage = "Aggiornamento del prezzo riuscito.";
        else
            $successOrErrorMessage = "Aggiornamento del prezzo fallito.";
    }
}

if ($successOrErrorMessage != null)
    echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$successOrErrorMessage'}))</script>";

$productsList = getProductsList();
?>

<div class="card-panel container centered">
    <span class="card-panel-title">Visualizza prodotti registrati</span>

    <? printHtmlTableFromAssocArray($productsList) ?>
</div>

<div class="fixed-action-btn">
    <a class="btn-floating btn-large pulse">
        <i class="large material-icons">edit</i>
    </a>
    <ul>
        <li>
            <a class="btn-floating white" onclick="M.Modal.getInstance(document.getElementById('editPriceModal')).open()">
                <i class="material-icons grey-text text-darken-4">attach_money</i>
            </a>
        </li>
        <li>
            <a class="btn-floating white" onclick="M.Modal.getInstance(document.getElementById('newProductModal')).open()">
                <i class="material-icons grey-text text-darken-4">add</i>
            </a>
        </li>
    </ul>
</div>

<div id="newProductModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Registra un nuovo prodotto</h3>
            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="name" name="name" type="text" required>
                    <label for="name">Nome prodotto</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="brand" name="brand" type="text" required>
                    <label for="brand">Produttore</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="barcode" name="barcode" type="text" minlength="13" maxlength="13" required>
                    <label for="barcode">Codice EAN</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="sellPrice" name="sellPrice" type="text" required>
                    <label for="sellPrice">Prezzo di vendita (€)</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="waves-effect waves-green btn-flat">Registra</button>
        </div>

        <input type="hidden" name="tab" value="3">
        <input type="hidden" name="action" value="newProduct">
    </form>
</div>

<div id="editPriceModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Modifica il prezzo di un prodotto esistente</h3>
            <div class="row">
                <div class="input-field col s12 m6">
                    <select id="product" name="productCode" required>
                        <option disabled selected>Seleziona un prodotto</option>
                        <?php
                        foreach ($productsList as $productInfo)
                            echo "<option value='{$productInfo["Barcode"]}'>{$productInfo["Nome prodotto"]} ({$productInfo["Barcode"]})</option>";
                        ?>
                    </select>
                    <label for="product">Nome prodotto</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="sellPrice" name="sellPrice" type="text" required>
                    <label for="sellPrice">Prezzo di vendita (€)</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="waves-effect waves-green btn-flat">Aggiorna</button>
        </div>

        <input type="hidden" name="tab" value="3">
        <input type="hidden" name="action" value="editPrice">
    </form>
</div>