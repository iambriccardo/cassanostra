<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/chartUtils.php";
require_once __DIR__ . "/../../queries/stats.php";
dieIfInvalidSessionOrRole("DIR");
?>

<style>
    .card-panel {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

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
    <div class="col s12 m6">
        <div class="card-panel"> <div id="pieProductsChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generatePieGraph("Top 5 prodotti", "Il grafico mostra i 5 prodotti più venduti", "pieProductsChart", getMostSellingProducts()) ?>
    </div>
    <div class="col s12 m6">
        <div class="card-panel"> <div id="pieBrandsChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generatePieGraph("Top 5 marchi", "Il grafico mostra le 5 marche più vendute", "pieBrandsChart", getMostSellingBrands()) ?>
    </div>
</div>