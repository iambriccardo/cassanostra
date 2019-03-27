<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
require_once __DIR__ . "/../../queries/stats.php";
dieIfInvalidSessionOrRole("DIR");
?>

<div class="row">
    <div class="col s12 m12">
        <div class="card-panel">
            <span class="card-panel-title">Classifica cassieri migliori</span>
            <? printHtmlTableFromAssocArray(getCashiersStats(true)) ?>
        </div>
    </div>
    <div class="col s12 m12">
        <div class="card-panel">
            <span class="card-panel-title">Classifica cassieri peggiori</span>
            <? printHtmlTableFromAssocArray(getCashiersStats(false)) ?>
        </div>
    </div>
</div>