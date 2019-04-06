<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../queries/stores.php";
require_once __DIR__ . "/../../queries/products.php";
require_once __DIR__ . "/../../queries/clients.php";
require_once __DIR__ . "/../../queries/invoices.php";
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
                    $_SESSION['currentCashRegister'] = $selectedCashier;
            }
        }
        else if ($_POST["action"] === "invoice")
        {
            // La prima volta che viene passato un prodotto o letta una carta, crea una nuova fattura nel DB per poter salvare le vendite
            if (empty($_SESSION['cashier']["currentInvoiceId"]))
            {
                $invoiceId = registerCashierInvoice();
                if ($invoiceId == null)
                {
                    $errorMessage = "Impossibile registrare la fattura.";
                    return;
                }
                $_SESSION['cashier']["currentInvoiceId"] = $invoiceId;
            }

            if ($_POST["submitType"] === "registerEntry")
            {
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

                $saleId = registerSale($productInfo["ID_Prodotto"], $productInfo['amount'], $productInfo["PrezzoVenditaAttuale"], $_SESSION['cashier']["currentInvoiceId"], $_SESSION['currentCashRegister']);
                if ($saleId == null)
                {
                    $errorMessage = "Errore durante la registrazione della vendita.";
                    return;
                }

                $productInfo["entryId"] = $saleId;
                if (!isset($_SESSION['cashier']["invoiceEntries"]))
                    $_SESSION['cashier']["invoiceEntries"] = [];

                $_SESSION['cashier']["invoiceEntries"][] = $productInfo;
            }
            else if ($_POST["submitType"] === "scanCard")
            {
                $cardCode = intval($_POST["cardCode"]);
                $clientData = getClientDataFromFidelityCardNumber($cardCode);
                if ($clientData == null)
                {
                    $errorMessage = "Numero carta non valido.";
                    return;
                }

                $updateSuccessful = setCashierInvoiceUser($_SESSION['cashier']["currentInvoiceId"], $clientData["Username"]);
                if (!$updateSuccessful)
                {
                    $errorMessage = "Impossibile aggiornare i dati relativi allo scontrino. Riprova.";
                    return;
                }

                $_SESSION['cashier']['currentClient'] = $clientData;
            }
            else if ($_POST["submitType"] === "closeInvoice")
            {
                $_SESSION['cashier']['invoiceClosed'] = true;
            }
            else if ($_POST["submitType"] === "nextClient")
            {
                unset($_SESSION['cashier']);
            }
            else if (!empty($_POST['cancelEntry']))
            {
                $cancelledSaleId = intval($_POST['cancelEntry']);
                $cancellationSuccessful = cancelSale($cancelledSaleId);
                if (!$cancellationSuccessful)
                    $errorMessage = "Errore durante lo storno della vendita.";
                else
                {
                    for ($i = 0; $i < count($_SESSION['cashier']["invoiceEntries"]); $i++)
                    {
                        if ($_SESSION['cashier']["invoiceEntries"][$i]["entryId"] === $cancelledSaleId)
                        {
                            array_splice($_SESSION['cashier']["invoiceEntries"], $i, 1);
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

<?php if (!empty($_SESSION['currentCashRegister'])) : ?>

<style>
    .expand-vertically {
        height: calc(100vh - 135px);
        min-height: 500px;
        overflow: auto;
    }

    .flex-col-reverse {
        display: flex;
        flex-direction: column-reverse;
        row-gap: 32px;
    }

    .calc-style .btn {
        width: 100%;
        height: 80px;
        line-height: 80px;
        margin: 8px;
        text-align: left;
        font-size: 16px;
        border-radius: 8px;
    }

    .col .row.calc-style {
        margin-right: 0;
    }

    .row {
        margin-bottom: 0;
    }
</style>

<div class="row">
    <form method="post">
        <div class="col s12 m6">
            <div class="card-panel expand-vertically">
                <span class="card-panel-title">Scontrino <?= ($_SESSION['cashier']["currentInvoiceId"] ? "#{$_SESSION['cashier']["currentInvoiceId"]}" : "") ?></span>
                <p><b>Cliente: </b> <?= $_SESSION['cashier']["currentClient"] ? "{$_SESSION['cashier']["currentClient"]["Nome"]} {$_SESSION['cashier']["currentClient"]["Cognome"]}" : "anonimo" ?></p>

                <ul class="collection">
                    <li class="collection-item">
                        <div class='row'>
                            <div class='col s6'><b>Nome prodotto</b></div>
                            <div class='col s2'><b>Quantità</b></div>
                            <div class='col s3'><b>Prezzo</b></div>
                        </div>
                    </li>
                    <?php
                    foreach ($_SESSION['cashier']["invoiceEntries"] as $productInfo)
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

                    // Mostra il totale se lo scontrino è chiuso
                    if ($_SESSION['cashier']['invoiceClosed'])
                    {
                        $totalPrice = 0;
                        foreach ($_SESSION['cashier']["invoiceEntries"] as $productInfo)
                            $totalPrice += $productInfo['amount'] * $productInfo["PrezzoVenditaAttuale"];

                        echo "<li class='collection-item'>
                            <div class='row valign-wrapper'>
                              <div class='col s6'><b>Totale</b></div>
                              <div class='col offset-s2 s4'><b>€$totalPrice</b></div>
                            </div>
                          </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div class="col s12 m6">
            <div class="card-panel expand-vertically flex-col-reverse">
                <div class="row calc-style">
                    <div class="row">
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('7')">7</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('8')">8</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('9')">9</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="swapTextInputs()">
                                <i id="cardBtnIcon" class="material-icons" style="font-size: 1.6rem">credit_card</i>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('4')">4</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('5')">5</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('6')">6</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="backSpaceInsideFieldContent()">C</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('1')">1</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('2')">2</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('3')">3</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="clearFieldContent()">AC</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('0')">0</a>
                        </div>
                        <div class="col s3">
                            <a class="btn white grey-text text-darken-4" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?> onclick="appendStringIntoField('00')">00</a>
                        </div>
                        <div class="col s6">
                            <button type="submit" name="submitType" value="<?= ($_SESSION['cashier']['invoiceClosed'] ? "nextClient" : "closeInvoice") ?>" class="btn waves waves-light">
                                <?= ($_SESSION['cashier']['invoiceClosed'] ? "Prossimo cliente" : "Chiudi scontrino") ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="productInput" class="input-field col s7">
                        <input type="text" id="productEan" name="productEan" minlength="13" maxlength="13" autocomplete="off" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?>>
                        <label for="productEan">Codice prodotto</label>
                    </div>
                    <div id="cardInput" class="input-field col s7 hidden">
                        <input type="number" id="cardCode" name="cardCode" autocomplete="off" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?>>
                        <label for="cardCode">Codice tessera</label>
                    </div>
                    <div class="input-field col s3">
                        <input type="number" id="productAmount" name="productAmount" value="1" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?>>
                        <label for="productAmount">Quantità</label>
                    </div>
                    <div class="input-field col s2 right-align">
                        <button id="scanBtn" class='btn waves-effect waves-light' type="submit" name="submitType" value="registerEntry" <?= ($_SESSION['cashier']['invoiceClosed'] ? "disabled" : "") ?>>
                            Aggiungi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="tab" value="0">
        <input type="hidden" name="action" value="invoice">
    </form>
</div>

<script>
    function swapTextInputs()
    {
        var cardInput = document.getElementById('cardInput');
        var productInput = document.getElementById('productInput');
        var cardBtnIcon = document.getElementById('cardBtnIcon');

        var productAmountField = document.getElementById('productAmount');
        var scanButton = document.getElementById('scanBtn');

        if (cardInput.classList.contains('hidden'))
        {
            productInput.classList.add('hidden');
            cardInput.classList.remove('hidden');
            cardBtnIcon.innerText = 'shopping_cart';
            productAmountField.disabled = true;
            scanButton.value = 'scanCard';
        }
        else
        {
            cardInput.classList.add('hidden');
            productInput.classList.remove('hidden');
            cardBtnIcon.innerText = 'credit_card';
            productAmountField.disabled = false;
            scanButton.value = 'registerEntry';
        }
    }

    function appendStringIntoField(string)
    {
        var cardInputContainer = document.getElementById('cardInput');
        var productInputContainer = document.getElementById('productInput');
        var cardCodeInput = document.getElementById('cardCode');
        var productEanInput = document.getElementById('productEan');

        if (!cardInputContainer.classList.contains('hidden'))
        {
            cardCodeInput.focus();
            cardCodeInput.value += string;
        }
        else if (!productInputContainer.classList.contains('hidden'))
        {
            productEanInput.focus();
            productEanInput.value += string;
        }
    }

    function clearFieldContent()
    {
        var cardInputContainer = document.getElementById('cardInput');
        var productInputContainer = document.getElementById('productInput');
        var cardCodeInput = document.getElementById('cardCode');
        var productEanInput = document.getElementById('productEan');

        if (!cardInputContainer.classList.contains('hidden'))
        {
            cardCodeInput.focus();
            cardCodeInput.value = '';
        }
        else if (!productInputContainer.classList.contains('hidden'))
        {
            productEanInput.focus();
            productEanInput.value = '';
        }
    }

    function backSpaceInsideFieldContent()
    {
        var cardInputContainer = document.getElementById('cardInput');
        var productInputContainer = document.getElementById('productInput');
        var cardCodeInput = document.getElementById('cardCode');
        var productEanInput = document.getElementById('productEan');

        if (!cardInputContainer.classList.contains('hidden'))
        {
            cardCodeInput.focus();
            cardCodeInput.value = cardCodeInput.value.substring(0, cardCodeInput.value.length-2);
        }
        else if (!productInputContainer.classList.contains('hidden'))
        {
            productEanInput.focus();
            productEanInput.value = productEanInput.value.substring(0, productEanInput.value.length-2);
        }
    }
</script>

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
                        if ($GLOBALS["selectedStoreId"] == null)
                            echo "<option disabled selected>Seleziona</option>";

                        foreach (getStoresList() as $store)
                        {
                            $selectedString = $GLOBALS["selectedStoreId"] == $store['Codice'] ? "selected" : "";
                            echo "<option value=\"{$store['Codice']}\" $selectedString>{$store['Nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col s12 m6">
                Numero cassa:
                <div class="input-field inline" style="vertical-align: unset">
                    <select id="cashier" name="cashier" <?= ($GLOBALS["selectedStoreId"] == null ? "disabled" : "") ?> required>
                        <option disabled selected>Seleziona</option>
                        <?php
                        if ($GLOBALS["selectedStoreId"] !== null)
                        {
                            foreach ($GLOBALS["cashiersForSelectedStore"] as $cashier)
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
