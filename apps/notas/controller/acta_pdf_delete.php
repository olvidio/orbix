<?php
// INICIO Cabecera global de URL de controlador *********************************
use notas\model\entity\Acta;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// El delete es via POST!!!";

$Qacta = (string) \filter_input(INPUT_POST, 'acta_num');

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
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();