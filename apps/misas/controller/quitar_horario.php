<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\repositories\PlantillaRepository;
use web\NullDateTimeLocal;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$PlantillaRepository = new PlantillaRepository();


if (empty($Qid_item)) {
    exit("Error: falta el id_item");
} else {
    $oPlantilla = $PlantillaRepository->findById($Qid_item);
}

$oT_start = new NullDateTimeLocal();
$oPlantilla->setT_start($oT_startseg);
$oT_end = new NullDateTimeLocal();
$oPlantilla->setT_end($oT_end);


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
