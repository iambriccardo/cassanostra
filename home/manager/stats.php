<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/chartUtils.php";
require_once __DIR__ . "/../../queries/stats.php";
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
    <div class="col s12 m12">
        <div class="card-panel"> <div id="pieBrandsChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generatePieGraph("Top 10 produttori", "Il grafico mostra i 10 produttori con il maggior numero di prodotti", "pieBrandsChart", getProductsNumberByBrand()) ?>
    </div>
    <div class="col s12 m12">
        <div class="card-panel"> <div id="splineIncomingsChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generateSplineGraph("Andamento entrate", "Entrate in euro", "splineIncomingsChart", getIncomingsHistory()) ?>
    </div>
    <div class="col s12 m12">
        <div class="card-panel"> <div id="splineExpensesChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generateSplineGraph("Andamento uscite", "Uscite in euro", "splineExpensesChart", getExpensesHistory()) ?>
    </div>
</div>