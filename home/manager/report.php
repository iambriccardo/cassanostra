<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/chartUtils.php";
dieIfInvalidSessionOrRole("DIR");
?>

<div class="row">
    <div class="col s12 m12">
        <div class="card-panel"> <div id="splineIncomingsChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generateSplineGraph("Andamento entrate", "Entrate in euro", "splineIncomingsChart", getIncomingsHistory()) ?>
    </div>
    <div class="col s12 m12">
        <div class="card-panel"> <div id="splineExpensesChart" style="height: 370px; width: 100%;"></div> </div>
        <?php generateSplineGraph("Andamento uscite", "Uscite in euro", "splineExpensesChart", getExpensesHistory()) ?>
    </div>
</div>