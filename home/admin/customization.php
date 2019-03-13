<?php
require_once __DIR__ . "/../../access/accessUtils.php";
dieIfInvalidSessionOrRole("ADM");

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] == 0)
{
    // TODO applica impostazioni
}
?>

<style>
    .card-panel-title {
        font-size: 32px;
        font-weight: 300
    }
</style>

<div class="row">
    <div class="col s12 m6">
        <div class="card-panel">
            <span class="card-panel-title">Personalizza il branding</span>
            <form method="post">
                <div class="input-field" style="margin-top: 32px;">
                    <input id="marketName" name="marketName" type="text" value="<?= getMarketName() ?>">
                    <label for="marketName">Nome del negozio/catena</label>
                </div>
                <div class="input-field" style="margin-top: 32px;">
                    <input id="accentColor" name="accentColor" type="text" value="<?= getAccentColor()?>">
                    <label for="accentColor">Colore principale del tema</label>
                </div>
                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="branding">
                <button class="btn waves-effect waves-light" type="submit">Salva</button>
            </form>
        </div>
    </div>

    <div class="col s12 m6">
        <div class="card-panel">
            <span class="card-panel-title">Carica il tuo logo</span>
        </div>
    </div>
</div>

<!-- Libreria color picker -->
<script type="text/javascript" src="../lib/jscolor.js"></script>