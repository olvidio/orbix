<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\repositories\PlantillaRepository;
use web\DateTimeLocal;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qt_start = (string)filter_input(INPUT_POST, 't_start');
$Qt_end = (string)filter_input(INPUT_POST, 't_end');

$PlantillaRepository = new PlantillaRepository();


if (empty($Qid_item)) {
    exit("Error: falta el id_item");
} else {
    $oPlantilla = $PlantillaRepository->findById($Qid_item);
}

if (!empty($Qt_start)) {
    $oT_start = DateTimeLocal::createFromFormat('H:i',$Qt_start);
    $oPlantilla->setT_start($oT_start);
}
if (!empty($Qt_end)) {
    $oT_end = DateTimeLocal::createFromFormat('H:i', $Qt_end);
    $oPlantilla->setT_end($oT_end);
}


if ($PlantillaRepository->Guardar($oPlantilla) === FALSE) {
    $error_txt .= $PlantillaRepository->getErrorTxt();
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
