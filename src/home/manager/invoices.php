<?php
require_once __DIR__ . "/../../access/accessUtils.php";
require_once __DIR__ . "/../../utils/tableUtils.php";
require_once __DIR__ . "/../../queries/invoices.php";
dieIfInvalidSessionOrRole("DIR");

if ($_POST["tab"] === "3" && isset($_POST['filterByDate']))
{
    if (!empty($_POST['fromDate']) && !empty($_POST['toDate']) && strcmp($_POST['fromDate'], $_POST['toDate']) <= 0)
    {
        $purifier = new HTMLPurifier();
        $fromDate = $purifier->purify($_POST['fromDate']);
        $toDate = $purifier->purify($_POST['toDate']);
        $invoicesList = getAllInvoices($fromDate, $toDate);
    }
    else
        $invoicesList = getAllInvoices();
}
else
    $invoicesList = getAllInvoices();
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
                            <div class="col s12 m4 offset-m1">
                                <label>
                                    Data inizio
                                    <input type="text" class="datepicker" name="fromDate" value="<?= $fromDate ?>">
                                </label>
                            </div>
                            <div class="col s12 m4">
                                <label>
                                    Data fine
                                    <input type="text" class="datepicker" name="toDate" value="<?= $toDate ?>">
                                </label>
                            </div>
                            <div class="col s12 m3">
                                <button class="btn waves-effect waves-light right" type="submit" name="filterByDate">Filtra per data</button>
                            </div>
                        </div>

                        <input type="hidden" name="tab" value="3">
                    </form>
                </div>
            </div>

            <?php
                printHtmlTableFromAssocArray($invoicesList);
            ?>
        </div>
    </div>
</div>