<?php

// INICIO Cabecera global de URL de controlador *********************************
use misas\domain\repositories\PlantillaRepository;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$PlantillaRepository = new PlantillaRepository();
$oPlantilla = $PlantillaRepository->findById($Qid_item);

$t_start = $oPlantilla->getT_start()->format('H:i');
$t_end = $oPlantilla->getT_end()->format('H:i');

$oHash = new Hash();
$oHash->setArrayCamposHidden(['id_item' => $Qid_item]);
$oHash->setCamposForm('t_start!t_end');
$param_guardar = $oHash->getParamAjax();


$oHash->setCamposForm('id_item');
$param_quitar = $oHash->getParamAjax();

$a_campos = [
    't_start' => $t_start,
    't_end' => $t_end,
    'param_guardar' => $param_guardar,
    'param_quitar' => $param_quitar,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('horario_tarea.html.twig', $a_campos);