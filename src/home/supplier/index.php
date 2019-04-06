<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
require_once __DIR__ . "/../../queries/invoices.php";
dieIfInvalidSessionOrRole("FOR");

$fromDateFilter = null;
if ($_POST["action"] === "filter")
{
    $purifier = new HTMLPurifier();
    $fromDateFilter = $purifier->purify($_POST["fromDate"]);
    $invoicesList = getSupplierInvoices($_SESSION["username"], $fromDateFilter);
}
else
    $invoicesList = getSupplierInvoices($_SESSION["username"]);
?>

<div class="card-panel container centered">
    <span class="card-panel-title">Storico fatture</span>
    <form method="post">
        Mostra più vecchi di:
        <div class="input-field inline" style="vertical-align: unset">
            <input class="datepicker" type="text" id="fromDate" name="fromDate" value="<?= $fromDateFilter ?>">
        </div>
        <button class="btn waves-effect waves-light" type="submit" style="margin-left: 32px">Aggiorna</button>

        <input type="hidden" name="action" value="filter">
    </form>

    <? printHtmlTableFromAssocArray($invoicesList) ?>
</div>