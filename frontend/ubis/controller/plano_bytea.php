<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\helpers\MultipartUploadHelper;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';

$_POST = $_REQUEST;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qact = (string)filter_input(INPUT_POST, 'act');
$Qid_direccion = (int)filter_input(INPUT_POST, 'id_direccion');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
if (empty($Qact)) {
    $Qact = (string)filter_input(INPUT_GET, 'act');
    $Qid_direccion = (int)filter_input(INPUT_GET, 'id_direccion');
    $Qobj_dir = (string)filter_input(INPUT_GET, 'obj_dir');
}

switch ($Qact) {
case "eliminar":
    ubis_plano_borrar($Qobj_dir, $Qid_direccion);
    echo "<body onload=\"window.close();\" ></body>";
    break;
case "comprobar":
    $aDatosPlano = ubis_plano_download($Qobj_dir, $Qid_direccion);
    $plano_doc = $aDatosPlano['plano_doc'];
    require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
    ajax_json_response('', [
        'exists' => !empty($plano_doc),
        'text' => empty($plano_doc) ? 'no' : 'si',
    ]);
case "upload":
    if (MultipartUploadHelper::isPostTooLarge()) {
        echo htmlspecialchars(MultipartUploadHelper::textPostMaxExceededPhp(), ENT_QUOTES, 'UTF-8');
        break;
    }
    if (!isset($_FILES['userfile'])) {
        echo htmlspecialchars(_("No se ha recibido ningún archivo."), ENT_QUOTES, 'UTF-8');
        break;
    }
    $upload = ubis_upload_file_from_post($_FILES['userfile']);
    if ($upload['error'] !== UPLOAD_ERR_OK) {
        echo htmlspecialchars(
            MultipartUploadHelper::messageForPhpUploadError($upload['error'], $upload['name']),
            ENT_QUOTES,
            'UTF-8'
        );
        break;
    }
    $fichero = file_get_contents($upload['tmp_name']);
    if (!is_string($fichero)) {
        echo htmlspecialchars(_("No se ha podido leer el archivo."), ENT_QUOTES, 'UTF-8');
        break;
    }
    ubis_plano_upload($Qobj_dir, $Qid_direccion, $upload['filename'], $upload['extension'], $fichero);
    echo "<body onload='window.close();' ></body>";
    break;
case "download":
    $aDatosPlano = ubis_plano_download($Qobj_dir, $Qid_direccion);

    $plano_nom = $aDatosPlano['plano_nom'];
    $plano_extension = $aDatosPlano['plano_extension'];
    $plano_doc = $aDatosPlano['plano_doc'];

    if (empty($plano_doc)) {
        exit(_("no existe un plano"));
    }

    $nom_ext = $plano_nom . "." . $plano_extension;

    switch ($plano_extension) {
        case "odt":
            $ctype = "application/vnd.oasis.opendocument.text";
            break;
        case "pdf":
            $ctype = "application/pdf";
            break;
        case "exe":
            $ctype = "application/octet-stream";
            break;
        case "zip":
            $ctype = "application/zip";
            break;
        case "rtf":
            $ctype = "application/msword";
            break;
        case "doc":
            $ctype = "application/msword";
            break;
        case "xls":
            $ctype = "application/vnd.ms-excel";
            break;
        case "ppt":
            $ctype = "application/vnd.ms-powerpoint";
            break;
        case "gif":
            $ctype = "image/gif";
            break;
        case "png":
            $ctype = "image/png";
            break;
        case "jpeg":
        case "jpg":
            $ctype = "image/jpg";
            break;
        default:
            $ctype = "application/force-download";
    }

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"" . $nom_ext . "\";");
    header("Content-Transfer-Encoding: binary");

    ob_start();
    ob_clean();
    flush();
    if (is_resource($plano_doc)) {
        echo fpassthru($plano_doc);
    } elseif (is_string($plano_doc)) {
        echo $plano_doc;
    }
    die();
case 'adjuntar':
$url = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/ubis/controller/plano_bytea.php';
$oHashComprobar = new HashFront();
$oHashComprobar->setUrl($url);
$oHashComprobar->setCamposForm('id_direccion!obj_dir!act');
$h = $oHashComprobar->linkSinValParams();

$oHash = new HashFront();
$a_camposHidden = [
    'id_direccion' => $Qid_direccion,
    'obj_dir' => $Qobj_dir,
    'act' => 'upload',
];
$camposForm = 'name_file!userfile';
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo('userfile');
$oHash->setArraycamposHidden($a_camposHidden);

$titulo = _("introducir documento");
$txt_btn = ucfirst(_("introducir"));
$act = "upload";
?>
<!-- jQuery -->
<script type="text/javascript"
        src='<?= AppUrlConfig::getNodeModulesBaseUrl() . '/jquery/dist/jquery.min.js'; ?>'></script>
<script>
    fnjs_introducir = function () {
    var id_direccion = $('#id_direccion').val();

    var url = '<?= AppUrlConfig::getPublicAppBaseUrl() ?>/frontend/ubis/controller/plano_bytea.php';
    var parametros = 'act=comprobar&obj_dir=<?= $Qobj_dir?><?= $h ?>&id_direccion=' + id_direccion;

    fnjs_ajax_json({
    url: url,
    data: parametros,
    onSuccess: function (data) {
    if (data.exists === true || data.text === 'si') {
    seguro = confirm(<?= json_encode(_("ya existe un escrito. ¿Desea reemplazarlo?")) ?>);
} else {
    seguro = 1;
}
    if (seguro) {
    $('#name_file').val($('#userfile').val());
    $('#frm_doc1').trigger("submit");
} else {
    //$(siguiente).trigger("focus");
}
}
});
}
</script>
<h2><?= $titulo ?></h2>
<form id="frm_doc1" name="frm_doc1" ENCTYPE="multipart/form-data" method="POST" action="plano_bytea.php">
    <?= $oHash->getCamposHtml(); ?>
    <input type="hidden" id="name_file" name="name_file" value="">
        <?= ucfirst(_("ubicación del fichero")) ?>
        <input type='file' id='userfile' name='userfile' size='30'><br><br>
            <br><input type='button' value="<?= $txt_btn ?>" id='B1' name='B1' onclick="fnjs_introducir();">
</form>
<?php
break;
}
