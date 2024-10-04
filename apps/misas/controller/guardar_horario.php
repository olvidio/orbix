<?php

// INICIO Cabecera global de URL de controlador *********************************

use encargossacd\model\entity\EncargoHorario;
use misas\domain\repositories\PlantillaRepository;
use web\DateTimeLocal;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
$Qt_start = (string)filter_input(INPUT_POST, 't_start');
$Qt_end = (string)filter_input(INPUT_POST, 't_end');

if (empty($Qid_item_h)) {
    exit("Error: falta el id_item");
} else {
    $oEncargoHorario = new EncargoHorario($Qid_item_h);
    $oEncargoHorario->DBCarregar();
}

if (!empty($Qt_start)) {
    $oEncargoHorario->setH_ini($Qt_start);
}
if (!empty($Qt_end)) {
    $oEncargoHorario->setH_fin($Qt_end);
}


if ($oEncargoHorario->DBGuardar() === FALSE) {
    $error_txt .= $oEncargoHorario->getErrorTxt();
}


if (empty($error_txt)) {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'ok';
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $error_txt;
}

//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();
