<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../queries/stores.php";
require_once __DIR__ . "/../../queries/products.php";
dieIfInvalidSessionOrRole("CAS");

$selectedStoreId = null;
$cashiersForSelectedStore = null;
$errorMessage = null;

function handleSubmit()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "0")
    {
        global $selectedStoreId, $cashiersForSelectedStore, $errorMessage;

        $purifier = new HTMLPurifier();
        if ($_POST["action"] === "selectCashier")
        {
            $selectedStoreId = intval($_POST["store"]);
            if (!empty($selectedStoreId))
                $cashiersForSelectedStore = getCashiersForStore($selectedStoreId);

            if (isset($_POST["openCashier"]))
            {
                $selectedCashier = intval($_POST["cashier"]);
                if (!empty($selectedCashier))
                    $_SESSION['currentOpenCashier'] = $selectedCashier;
            }
        }
        else if ($_POST["action"] === "invoice")
        {
            if ($_POST["submitType"] === "registerEntry")
            {
                // La prima volta che viene passato un prodotto, crea una nuova fattura nel DB per poter salvare le vendite
                if (empty($_SESSION["currentInvoiceId"]))
                {
                    $invoiceId = registerCashierInvoice();
                    if ($invoiceId == null)
                    {
                        $errorMessage = "Impossibile registrare la fattura.";
                        return;
                    }
                    $_SESSION["currentInvoiceId"] = $invoiceId;
                }

                $productInfo = getProductDetails($purifier->purify($_POST["productEan"]));
                if ($productInfo == null)
                {
                    $errorMessage = "Codice prodotto inesistente.";
                    return;
                }
                $productInfo['amount'] = intval($_POST["productAmount"]);

                if ($productInfo['amount'] <= 0)
                {
                    $errorMessage = "Quantità di prodotto nulla.";
                    return;
                }

                $saleId = registerSale($productInfo["ID_Prodotto"], $productInfo['amount'], $productInfo["PrezzoVenditaAttuale"], $_SESSION["currentInvoiceId"], $_SESSION['currentOpenCashier']);
                if ($saleId == null)
                {
                    $errorMessage = "Errore durante la registrazione della vendita.";
                    return;
                }

                $productInfo["entryId"] = $saleId;
                if (!isset($_SESSION["invoiceEntries"]))
                    $_SESSION["invoiceEntries"] = [];

                $_SESSION["invoiceEntries"][] = $productInfo;
            }
            else if (!empty($_POST['cancelEntry']))
            {
                $cancelledSaleId = intval($_POST['cancelEntry']);
                $cancellationSuccessful = cancelSale($cancelledSaleId);
                if (!$cancellationSuccessful)
                    $errorMessage = "Errore durante lo storno della vendita.";
                else
                {
                    for ($i = 0; $i < count($_SESSION["invoiceEntries"]); $i++)
                    {
                        if ($_SESSION["invoiceEntries"][$i]["entryId"] === $cancelledSaleId)
                        {
                            unset($_SESSION["invoiceEntries"][$i]);
                            break;
                        }
                    }
                }
            }
        }
    }
}

handleSubmit();
if ($GLOBALS["errorMessage"] != null)
    echo "<script>document.addEventListener('DOMContentLoaded', () => M.toast({html: '{$GLOBALS['errorMessage']}'}))</script>";
?>

<?php if (!empty($_SESSION['currentOpenCashier'])) : ?>

<style>
    .expand-vertically {
        height: calc(100vh - 135px);
        min-height: 400px;
        overflow: auto;
    }

    .row {
        margin-bottom: 0;
    }
</style>

<div class="row">
    <form method="post">
        <div class="col s12 m6">
            <div class="card-panel expand-vertically">
                <span class="card-panel-title">Scontrino <?= ($_SESSION["currentInvoiceId"] ? "#{$_SESSION["currentInvoiceId"]}" : "") ?></span>
                <p><b>Cliente: </b> <?= $_SESSION["currentClient"] ? "{$_SESSION["currentClient"]["name"]} {$_SESSION["currentClient"]["surname"]}" : "anonimo" ?></p>

                <ul class="collection">
                    <li class="collection-item">
                        <div class='row'>
                            <div class='col s6'><b>Nome prodotto</b></div>
                            <div class='col s2'><b>Quantità</b></div>
                            <div class='col s3'><b>Prezzo</b></div>
                        </div>
                    </li>
                    <?php
                    foreach ($_SESSION["invoiceEntries"] as $productInfo)
                    {
                        echo "<li class=\"collection-item\">
                            <div class='row valign-wrapper'>
                              <div class='col s6'>{$productInfo['NomeProdotto']}</div>
                              <div class='col s2'>{$productInfo['amount']}</div>
                              <div class='col s3'>€" .($productInfo['amount'] * $productInfo["PrezzoVenditaAttuale"]) . "</div>
                              <div class='col s1 right'>
                                <button type='submit' class='waves-effect waves-light btn-flat' name='cancelEntry' value='{$productInfo['entryId']}'>
                                    <i style=\"font-size: 1.5rem\" class=\"material-icons\">close</i>
                                </button>
                              </div>
                            </div> 
                          </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col s12 m6">
            <div class="card-panel expand-vertically">
                <input type="text" id="productEan" name="productEan" minlength="13" maxlength="13" autocomplete="off">
                <input type="number" id="productAmount" name="productAmount" value="1">
                <label for="productAmount">Quantità</label>
                <button class='btn waves-effect waves-light' type="submit" name="submitType" value="registerEntry">Aggiungi</button>
            </div>
        </div>

        <input type="hidden" name="tab" value="0">
        <input type="hidden" name="action" value="invoice">
    </form>
</div>

<!-- Inizializza dati per l'autocomplete del codice prodotto - non funziona qui -->
<!--<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.Autocomplete.init(document.getElementById('productEan'), {
            data: {
                <?php
                //foreach (getProductEANsList() as $eanCode)
                    //echo "\"$eanCode\": null,\n";
                ?>
            },
            limit: 5
        });
    });
</script>-->

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
