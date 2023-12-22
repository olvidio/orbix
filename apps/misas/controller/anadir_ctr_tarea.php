<?php

// INICIO Cabecera global de URL de controlador *********************************

use misas\domain\entity\Plantilla;
use misas\domain\repositories\PlantillaRepository;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qid_tarea = (integer)filter_input(INPUT_POST, 'id_tarea');

$PlantillaRepository = new PlantillaRepository();

switch ($Qque) {
//faltaría comprobar que no está.

    case 'anadir':
        $Qsemana=-1;
        $Qdia='MON';
        $Qid_item = $PlantillaRepository->getNewId_item();
        $oPlantilla = new Plantilla();
            $oPlantilla->setId_item($Qid_item);

        $oPlantilla->setTarea($Qid_tarea);
        $oPlantilla->setId_ctr($Qid_ubi);
        $oPlantilla->setSemana($Qsemana);
 //       $oPlantilla->setT_start(null);
 //       $oPlantilla->setT_end(null);
 

        if ($PlantillaRepository->Guardar($oPlantilla) === FALSE) {
            $error_txt .= $PlantillaRepository->getErrorTxt();
        }
        break;
    case 'quitar':
        $oPlantilla = $PlantillaRepository->findById($Qid_item);
        if ($PlantillaRepository->Eliminar($oPlantilla) === FALSE) {
            $error_txt .= $PlantillaRepository->getErrorTxt();
        }
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
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
