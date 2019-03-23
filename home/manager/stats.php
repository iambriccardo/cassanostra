<?php
require_once __DIR__ . "/../../access/accessUtils.php";
dieIfInvalidSessionOrRole("DIR");
?>

<div class="row">
    <div class="col s12 m3">
        <div class="card-panel blue">
            <h6 class="white-text">Prodotto più acquistato</h6>
            <h4 class="white-text"><?php echo getBestSellingProduct(); ?></h4>
        </div>
    </div>
    <div class="col s12 m3">
        <div class="card-panel amber">
            <h6 class="white-text">Marca più acquistata</h6>
            <h4 class="white-text"><?php echo getBestSellingBrand(); ?></h4>
        </div>
    </div>
    <div class="col s12 m3">
        <div class="card-panel green">
            <h6 class="white-text">Entrate mensili</h6>
            <h4 class="white-text"><?php echo getMonthlyIncome() . "€"; ?></h4>
        </div>
    </div>
    <div class="col s12 m3">
        <div class="card-panel pink">
            <h6 class="white-text">Uscite mensili</h6>
            <h4 class="white-text"><?php echo getMonthlyExpenses() . "€"; ?></h4>
        </div>
    </div>
</div>