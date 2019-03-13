<?php
require_once __DIR__ . "/../../access/accessUtils.php";
dieIfInvalidSessionOrRole("ADM");
require_once __DIR__ . "/../../lib/htmlpurifier/HTMLPurifier.standalone.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] == 0)
{
    if ($_POST["action"] === "branding")
    {
        global $config;
        $purifier = new HTMLPurifier();
        $config["marketName"] = $purifier->purify($_POST["marketName"]);
        $config["accentColor"] = $purifier->purify($_POST["accentColor"]);
        writeConfigOnFile();
        header("Refresh: 0");   // reload page to apply modifications
        exit();
    }
}
?>

<style>
    .card-panel-title {
        font-size: 32px;
        font-weight: 300
    }

    .color-box {
        width: 24px;
        height: 24px;
        display: inline-block;
        position: absolute;
        left: 96px;
        top: 12px;
    }

    .input-field {
        margin-top: 32px;
    }
</style>

<div class="row">
    <div class="col s12 m6">
        <div class="card-panel">
            <span class="card-panel-title">Personalizza il branding</span>
            <form method="post">
                <div class="input-field">
                    <input id="marketName" name="marketName" type="text" value="<?= getMarketName() ?>">
                    <label for="marketName">Nome del negozio/catena</label>
                </div>
                <div class="input-field">
                    <input id="accentColor" class="jscolor {valueElement:'accentColor',styleElement:'colorBox'}"
                           name="accentColor" type="text" value="<?= getAccentColor()?>">
                    <label for="accentColor">Colore principale del tema</label>
                    <div id="colorBox" class="color-box"></div>
                </div>
                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="branding">
                <div class="row" style="margin: 24px 0 0">
                    <button class="btn waves-effect waves-light right" type="submit">Salva</button>
                </div>
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