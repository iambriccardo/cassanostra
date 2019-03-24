<?php
require_once __DIR__ . "/../../access/accessUtils.php";
dieIfInvalidSessionOrRole("MAG");

$successOrErrorMessage = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "1")
{
    $purifier = new HTMLPurifier();
    if ($_POST["action"] === "newProduct")
    {
        $productName = $purifier->purify($_POST["name"]);
        $productBrand = $purifier->purify($_POST["brand"]);
        $eanCode = $purifier->purify($_POST["barcode"]);

        if (!empty($productName) && !empty($productBrand) && !empty($eanCode))
        {
            if (registerNewProduct($productName, $productBrand, $eanCode))
                $successOrErrorMessage = "Registrazione del prodotto riuscita.";
            else
                $successOrErrorMessage = "Registrazione del prodotto fallita.";
        }
        else
            $successOrErrorMessage = "Registrazione fallita: dati del prodotto mancanti.";
    }
    else if ($_POST["action"] === "invoice")
    {
        $_SESSION["warehouseInvoice"]["number"] = $purifier->purify($_POST["invoiceNr"]);
        $_SESSION["warehouseInvoice"]["date"] = $purifier->purify($_POST["invoiceDate"]);
        $_SESSION["warehouseInvoice"]["supplierUser"] = $purifier->purify($_POST["supplierUser"]);
        $_SESSION["warehouseInvoice"]["store"] = $purifier->purify($_POST["store"]);

        if ($_POST["submitType"] === "addProduct")
        {
            if (!isset($_SESSION["warehouseInvoice"]["products"]))
                $_SESSION["warehouseInvoice"]["products"] = [];

            $productInfo = getProductDetails($_POST["productCode"]);
            if ($productInfo == null)
                $successOrErrorMessage = "Codice prodotto non registrato.";
            else
            {
                $productInfo["Quantita"] = $purifier->purify($_POST["productAmount"]);
                $productInfo["PrezzoAcquisto"] = floatval($purifier->purify($_POST["price"]));
                if (!empty($productInfo["Quantita"]) && !empty($productInfo["PrezzoAcquisto"]))
                    $_SESSION["warehouseInvoice"]["products"][] = $productInfo;
                else
                    $successOrErrorMessage = "Non sono stati forniti tutti i dati.";
            }
        }
    }

    if ($successOrErrorMessage != null)
        echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '$successOrErrorMessage'}))</script>";
}
?>

<style>
    .section {
        padding-top: 0;
    }

    #add-product-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 0 8px;
    }
</style>

<!-- Inizializza dati per l'autocomplete del codice prodotto -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Autocomplete.init(document.getElementById('productCode'), {
            data: {
                <?php
                foreach (getProductEANsList() as $eanCode)
                    echo "\"$eanCode\": null,\n";
                ?>
            },
            limit: 5
        });
    });
</script>

<div class="card-panel container centered">
    <span class="card-panel-title">Inserisci una nuova fattura di acquisto</span>
    <form method="post">
        <div class="row">
            Seleziona punto vendita:
            <div class="input-field inline" style="vertical-align: unset">
                <select id="store" name="store" required>
                    <?php
                    foreach (getStoresList() as $store)
                    {
                        $selectedString = $_SESSION["warehouseInvoice"]["store"] === $store['Codice'] ? "selected" : "";
                        echo "<option value=\"{$store['Codice']}\" $selectedString>{$store['Nome']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="section">
            <h5>Dati fattura</h5>
            <div class="row">
                <div class="input-field col s12 m4">
                    <input type="text" id="invoiceNr" name="invoiceNr" value="<?= $_SESSION["warehouseInvoice"]["number"] ?>" required>
                    <label for="invoiceNr">Numero fattura</label>
                </div>
                <div class="input-field col s12 m4">
                    <input class="datepicker" type="text" id="invoiceDate" name="invoiceDate" value="<?= $_SESSION["warehouseInvoice"]["date"] ?>" required>
                    <label for="invoiceDate">Data fattura</label>
                </div>
                <div class="input-field col s12 m4">
                    <select id="supplierUser" name="supplierUser" required>
                        <option disabled selected>Seleziona un fornitore</option>
                        <?php
                        foreach (getSuppliers() as $supplierUser)
                        {
                            $selectedString = $supplierUser['Username'] === $_SESSION["warehouseInvoice"]["supplierUser"] ? "selected" : "";
                            echo "<option value='{$supplierUser['Username']}' $selectedString>
                                    {$supplierUser['Nome']} {$supplierUser['Cognome']} ({$supplierUser['Azienda']})
                                  </option>";
                        }
                        ?>
                    </select>
                    <label for="supplierUser">Referente fornitore</label>
                </div>
            </div>
        </div>
        <div class="section">
            <h5>Distinte</h5>
            <div class="row">
                <div class="input-field col s12 m4">
                    <input type="text" id="productCode" name="productCode" minlength="13" maxlength="13" autocomplete="off">
                    <label for="productCode">Codice prodotto</label>

                    <!-- bottone inline per aprire la modal di registrazione prodotto -->
                    <a id="add-product-btn" class="waves-effect waves-teal btn-flat" onclick="M.Modal.getInstance(document.getElementById('newProductModal')).open()">
                        <i style="font-size: 1.6rem" class="material-icons">add_circle</i>
                    </a>
                </div>
                <div class="input-field col s12 m3">
                    <input type="number" id="productAmount" name="productAmount">
                    <label for="productAmount">Quantità</label>
                </div>
                <div class="input-field col s12 m3">
                    <input type="text" id="price" name="price">
                    <label for="price">Prezzo di acquisto unitario (€)</label>
                </div>
                <div class="input-field col s2 right-align">
                    <button class="btn waves-effect waves-light" type="submit" name="submitType" value="addProduct">Aggiungi</button>
                </div>
            </div>
        </div>

        <ul class="collection">
            <?php
            foreach ($_SESSION["warehouseInvoice"]["products"] as $productInfo)
                echo "<li class=\"collection-item\">{$productInfo['NomeProdotto']}, {$productInfo['Quantita']} x €{$productInfo['PrezzoAcquisto']}</li>";
            ?>
        </ul>

        <input type="hidden" name="tab" value="1">
        <input type="hidden" name="action" value="invoice">
    </form>
</div>

<div id="newProductModal" class="modal">
    <form method="post">
        <div class="modal-content">
            <h3>Registra un nuovo prodotto</h3>
            <div class="row">
                <div class="input-field col s12 m4">
                    <input id="name" name="name" type="text" required>
                    <label for="name">Nome prodotto</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="brand" name="brand" type="text" required>
                    <label for="brand">Produttore</label>
                </div>
                <div class="input-field col s12 m4">
                    <input id="barcode" name="barcode" type="text" minlength="13" maxlength="13" required>
                    <label for="barcode">Codice EAN</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annulla</a>
            <button type="submit" class="waves-effect waves-green btn-flat">Registra</button>
        </div>

        <input type="hidden" name="tab" value="1">
        <input type="hidden" name="action" value="newProduct">
    </form>
</div>