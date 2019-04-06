<?php
require_once __DIR__ . "/../../access/accessUtils.php";
dieIfInvalidSessionOrRole("ADM");

// Il submit dei form di questa pagina viene gestito in home/index.php, in modo che le modifiche vengano
// applicate prima che logo e titolo della pagina vengano caricati
?>

<style>
    .color-box {
        width: 24px;
        height: 24px;
        display: inline-block;
        position: absolute;
        left: 96px;
        top: 12px;
    }

    .input-field:not(.inline) {
        margin-top: 32px;
    }

    .form-row {
        margin-top: 24px;
    }

    .logo-preview {
        margin-top: 16px;
        max-height: 120px;
        max-width: 100%;
        background-color: #<?= getAccentColor() ?>
    }
</style>

<div class="row">
    <div class="col s12 m6">
        <div class="card-panel">
            <span class="card-panel-title">Personalizza l'aspetto</span>
            <form method="post">
                <div class="input-field">
                    <input id="marketName" name="marketName" type="text" value="<?= getMarketName() ?>" required>
                    <label for="marketName">Nome del negozio/catena</label>
                </div>
                <div class="input-field">
                    <input id="accentColor" class="jscolor {valueElement:'accentColor',styleElement:'colorBox'}"
                           name="accentColor" type="text" value="<?= getAccentColor() ?>" required>
                    <label for="accentColor">Colore principale del tema</label>
                    <div id="colorBox" class="color-box"></div>
                </div>
                <div class="row" style="margin: 24px 0 0">
                    <button class="btn waves-effect waves-light right" name="actionType" value="brandName" type="submit">Salva</button>
                </div>

                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="customization">
            </form>
        </div>
    </div>

    <div class="col s12 m6">
        <div class="card-panel">
            <span class="card-panel-title">Carica il tuo logo</span>
            <form enctype="multipart/form-data" method="post">
                <div class="form-row">
                    Immagine attuale <i>(in caso di trasparenza viene applicato il colore del tema come sfondo)</i>:&nbsp;
                    <?=
                    file_exists(__DIR__ . "/../../res/logo.png") ?
                        '<br>
                         <img alt="Logo"
                         class="logo-preview"
                         src="../res/logo.png" >' : "logo di default"
                    ?>
                </div>
                <div class="form-row">
                    Carica un'immagine (max 3MB, formato PNG):<br>
                    <input style="margin-top: 16px;" type="file" name="logo" accept="image/png" required>
                </div>

                <div class="row" style="margin: 24px 0 0">
                    <?=
                    file_exists(__DIR__ . "/../../res/logo.png")
                        ? '<button form="removeForm" class="btn waves-effect waves-light" name="actionType" value="removeLogo" type="submit">Ripristina logo di default</button>'
                        : ""
                    ?>
                    <button class="btn waves-effect waves-light right" name="actionType" value="logo" type="submit">Carica</button>
                </div>

                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="customization">
            </form>

            <!-- Form nascosta usata per la rimozione del logo attuale -->
            <form id="removeForm" method="post">
                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="customization">
            </form>
        </div>
    </div>
</div>

<!-- Libreria color picker -->
<script type="text/javascript" src="../lib/jscolor.js"></script>