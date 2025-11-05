<?php
// INICIO Cabecera global de URL de controlador *********************************
use Illuminate\Http\JsonResponse;
use notas\model\entity\Acta;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// El delete es via POST!!!";

$Qacta = (string)filter_input(INPUT_POST, 'acta_num');

if (!empty($Qacta)) {
    $oActa = new Acta($Qacta);
    $oActa->DBCarregar('');
    $oActa->setPdf('');
    if ($oActa->DBGuardar() === FALSE) {
        $error_txt .= $oActa->getErrorTxt();
    }
} else {
    $error_txt = _("No se encuentra el acta");
}

if (!empty($error_txt)) {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
}
(new JsonResponse($jsondata))->send();
exit();