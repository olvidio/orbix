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
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qtarea = (integer)filter_input(INPUT_POST, 'tarea');
$Qdia = (string)filter_input(INPUT_POST, 'dia');
$Qsemana = (integer)filter_input(INPUT_POST, 'semana');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');

$PlantillaRepository = new PlantillaRepository();

switch ($Qque) {

    case 'asignar':
        if (empty($Qid_item)) {
            // nuevo
            $Qid_item = $PlantillaRepository->getNewId_item();
            $oPlantilla = new Plantilla();
            $oPlantilla->setId_item($Qid_item);
        } else {
            $oPlantilla = $PlantillaRepository->findById($Qid_item);
        }

        $oPlantilla->setTarea($Qtarea);
        $oPlantilla->setId_ctr($Qid_ubi);
        $oPlantilla->setId_nom($Qid_nom);
        $oPlantilla->setDia($Qdia);
        $oPlantilla->setSemana($Qsemana);


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
