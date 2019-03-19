<?php
require_once __DIR__ . "/../../access/accessUtils.php";
dieIfInvalidSessionOrRole("ADM");
require_once __DIR__ . "/../../lib/htmlpurifier/HTMLPurifier.standalone.php";

$uploadFailureReason = null;
function handleSubmit()
{
    $customLogoPath = __DIR__ . "/../../res/logo.png";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["tab"] === "0")
    {
        if ($_POST["action"] === "branding")
        {
            global $config;
            $purifier = new HTMLPurifier();
            $config["marketName"] = $purifier->purify($_POST["marketName"]);
            $config["accentColor"] = $purifier->purify($_POST["accentColor"]);
            writeConfigOnFile();
        }
        else if ($_POST["action"] === "logo")
        {
            global $uploadFailureReason;
            if ($_FILES["logo"]["error"] === UPLOAD_ERR_NO_FILE)
            {
                $uploadFailureReason = "Nessun file selezionato!";
                return;
            }

            // Controlla che l'upload non sia fallito
            if ($_FILES["logo"]["error"] !== UPLOAD_ERR_OK)
            {
                $uploadFailureReason = "Caricamento fallito!";
                return;
            }

            // Controlla che il file non superi i 3MB
            if ($_FILES["logo"]["size"] > 3145728)
            {
                $uploadFailureReason = "File troppo grande.";
                return;
            }

            // Controlla che sia un'immagine PNG valida
            $imageInfo = getimagesize($_FILES["logo"]["tmp_name"]);
            if (!$imageInfo || $imageInfo[2] !== IMAGETYPE_PNG)
            {
                $uploadFailureReason = "Il file caricato non è nel formato richiesto.";
                return;
            }

            $moveSuccessful = move_uploaded_file($_FILES['logo']['tmp_name'], $customLogoPath);
            if (!$moveSuccessful)
                $uploadFailureReason = "Errore nel salvataggio del file caricato.";
        }
        else if ($_POST["action"] === "removeLogo")
        {
            if (file_exists($customLogoPath))
                unlink($customLogoPath);
        }

        // TODO avvisare necessità reload
    }
}

handleSubmit();
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
</style>

<div class="row">
    <div class="col s12 m6">
        <div class="card-panel">
            <span class="card-panel-title">Personalizza l'aspetto</span>
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
            <form enctype="multipart/form-data" method="post">
                <div class="form-row">
                    Immagine attuale <i>(in caso di trasparenza viene applicato il colore del tema come sfondo)</i>:&nbsp;
                    <?=
                    file_exists(__DIR__ . "/../../res/logo.png") ?
                        '<br>
                         <img alt="Logo"
                         style="margin-top: 16px; max-height: 120px; background-color: #' . getAccentColor() . '"
                         src="../res/logo.png" >' : "logo di default"
                    ?>
                </div>
                <div class="form-row">
                    Carica un'immagine (max 3MB, formato PNG):<br>
                    <input style="margin-top: 16px;" type="file" name="logo" accept="image/png">
                    <?=
                    $GLOBALS["uploadFailureReason"] != null
                        ? "<div class=\"form-row\"><b>Errore:</b> {$GLOBALS["uploadFailureReason"]}</div>"
                        : ""
                    ?>
                </div>
                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="logo">
                <div class="row" style="margin: 24px 0 0">
                    <?=
                    file_exists(__DIR__ . "/../../res/logo.png")
                        ? '<button form="removeForm" class="btn waves-effect waves-light" type="submit">Ripristina logo di default</button>'
                        : ""
                    ?>
                    <button class="btn waves-effect waves-light right" type="submit">Carica</button>
                </div>
            </form>

            <!-- Form nascosta usata per la rimozione del logo attuale -->
            <form id="removeForm" method="post">
                <input type="hidden" name="tab" value="0">
                <input type="hidden" name="action" value="removeLogo">
            </form>
        </div>
    </div>
</div>

<!-- Libreria color picker -->
<script type="text/javascript" src="../lib/jscolor.js"></script>