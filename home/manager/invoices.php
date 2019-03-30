<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
require_once __DIR__ . "/../../queries/invoices.php";
dieIfInvalidSessionOrRole("DIR");
?>

<div class="row">
    <div class="col s12 m12">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 m6">
                    <span class="card-panel-title">Lista fatture</span>
                </div>
                <div class="col s12 m6">
                    <form method="post">
                        <div class="row">
                            <div class="col s12 m5">
                                <label>
                                    Data inizio
                                    <input type="text" class="datepicker" name="fromDate">
                                </label>
                            </div>
                            <div class="col s12 m5">
                                <label>
                                    Data fine
                                    <input type="text" class="datepicker" name="toDate">
                                </label>
                            </div>
                            <div class="col s12 m2">
                                <button class="btn waves-effect waves-light right" type="submit" name="filterByDate">Filtra per data</button>
                            </div>
                        </div>

                        <input type="hidden" name="tab" value="3">
                    </form>
                </div>
            </div>

            <?php

            if (isset($_POST['filterByDate'])) {
                printHtmlTableFromAssocArray(getAllInvoices($_POST['fromDate'], $_POST['toDate']));
            } else {
                printHtmlTableFromAssocArray(getAllInvoices());
            }

            ?>
        </div>
    </div>
</div>