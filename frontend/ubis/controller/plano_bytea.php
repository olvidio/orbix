<?php

/**
 * Plano de una dirección (ficha `direcciones_editar`).
 *
 * - `act=comprobar`: JSON `{exists, text, url}`; `url` es la descarga firmada (`tk`) de `/src/ubis/plano_download`,
 *   generada en el momento para que no caduque mientras la ficha sigue abierta.
 * - `act=adjuntar`: ventana para subir el plano; el formulario va por AJAX a `/src/ubis/plano_subir`
 *   con la cápsula `ctx_plano_subir` que firmó la ficha.
 *
 * Quitar el plano lo hace la ficha directamente contra `/src/ubis/plano_borrar` (`ctx_plano_borrar`).
 */

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\MultipartUploadHelper;
use frontend\shared\helpers\SignedDownloadToken;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;

// GET (ventana) y POST (AJAX) en uno; sin las cookies, que $_REQUEST incluye según `request_order` y rompen la firma.
$_POST = array_merge($_GET, $_POST);

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qact = (string)filter_input(INPUT_POST, 'act');
$Qid_direccion = (int)filter_input(INPUT_POST, 'id_direccion');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qctx_plano_subir = (string)filter_input(INPUT_POST, 'ctx_plano_subir');
if (empty($Qact)) {
    $Qact = (string)filter_input(INPUT_GET, 'act');
    $Qid_direccion = (int)filter_input(INPUT_GET, 'id_direccion');
    $Qobj_dir = (string)filter_input(INPUT_GET, 'obj_dir');
    $Qctx_plano_subir = (string)filter_input(INPUT_GET, 'ctx_plano_subir');
}

switch ($Qact) {
case "comprobar":
    $data = PostRequest::getDataFromUrl('/src/ubis/plano_existe', [
        'obj_dir' => $Qobj_dir,
        'id_direccion' => $Qid_direccion,
    ]);
    $existe = !empty($data['existe']);
    AjaxJsonSupport::response('', [
        'exists' => $existe,
        'text' => $existe ? 'si' : 'no',
        'url' => $existe ? SignedDownloadToken::urlPlanoDireccion($Qobj_dir, $Qid_direccion) : '',
    ]);
case 'adjuntar':
$url = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/ubis/controller/plano_bytea.php';
$oHashComprobar = new HashF();
$oHashComprobar->setUrl($url);
$oHashComprobar->setCamposForm('id_direccion!obj_dir!act');
$h = $oHashComprobar->linkSinValParams();

$url_plano_subir = AppUrlConfig::srcBrowserUrl('/src/ubis/plano_subir');
$titulo = _("introducir documento");
$txt_btn = ucfirst(_("introducir"));
?>
<!-- jQuery -->
<script type="text/javascript"
        src='<?= AppUrlConfig::getNodeModulesBaseUrl() . '/jquery/dist/jquery.min.js'; ?>'></script>
<script>
    fnjs_subir = function () {
        let request = $.ajax({
            url: <?= json_encode($url_plano_subir, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
            method: 'POST',
            data: new FormData(document.getElementById('frm_doc1')),
            processData: false,
            contentType: false,
            dataType: 'json'
        });
        request.done(function (json) {
            if (json.success !== true) {
                alert(<?= json_encode(_("respuesta")) ?> + ': ' + json.mensaje);
            } else {
                window.close();
            }
        });
        request.fail(function (xhr) {
            let msg = (xhr.responseJSON && xhr.responseJSON.mensaje) ? xhr.responseJSON.mensaje : <?= json_encode(MultipartUploadHelper::textPostMaxExceededPhp()) ?>;
            alert(msg);
        });
    }
    fnjs_introducir = function () {
        if (!$('#userfile').val()) {
            alert(<?= json_encode(_("No se ha recibido ningún archivo.")) ?>);
            return;
        }
        var url = '<?= AppUrlConfig::getPublicAppBaseUrl() ?>/frontend/ubis/controller/plano_bytea.php';
        var parametros = 'act=comprobar&obj_dir=<?= rawurlencode($Qobj_dir) ?><?= $h ?>&id_direccion=<?= $Qid_direccion ?>';
        $.ajax({url: url, data: parametros, method: 'POST', dataType: 'json'})
            .done(function (json) {
                var data = (json && json.data !== undefined) ? json.data : json;
                if (typeof data === 'string') {
                    try { data = JSON.parse(data); } catch (e) { data = {}; }
                }
                var seguro = true;
                if (data && (data.exists === true || data.text === 'si')) {
                    seguro = confirm(<?= json_encode(_("ya existe un escrito. ¿Desea reemplazarlo?")) ?>);
                }
                if (seguro) {
                    fnjs_subir();
                }
            });
    }
</script>
<h2><?= $titulo ?></h2>
<form id="frm_doc1" name="frm_doc1" ENCTYPE="multipart/form-data" method="POST" onsubmit="return false;">
    <input type="hidden" name="ctx_plano_subir" value="<?= htmlspecialchars($Qctx_plano_subir, ENT_QUOTES, 'UTF-8') ?>">
        <?= ucfirst(_("ubicación del fichero")) ?>
        <input type='file' id='userfile' name='userfile' size='30'><br><br>
            <br><input type='button' value="<?= $txt_btn ?>" id='B1' name='B1' onclick="fnjs_introducir();">
</form>
<?php
break;
}
