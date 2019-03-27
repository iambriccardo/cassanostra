<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
require_once __DIR__ . "/../../queries/invoices.php";
dieIfInvalidSessionOrRole("DIR");
?>

<div class="row">
    <div class="col s12 m12">
        <div class="card-panel">
            <span class="card-panel-title">Lista fatture entrate</span>
            <? printHtmlTableFromAssocArray(getIncomingsInvoices()) ?>
        </div>
    </div>
    <div class="col s12 m12">
        <div class="card-panel">
            <span class="card-panel-title">Lista fatture uscite</span>
            <? printHtmlTableFromAssocArray(getExpensesInvoices()) ?>
        </div>
    </div>
</div>