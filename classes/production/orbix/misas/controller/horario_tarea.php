<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use encargossacd\model\entity\EncargoHorario;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');

$oEncargoHorario = new EncargoHorario($Qid_item_h);

$t_start = $oEncargoHorario->getH_ini();
$t_end = $oEncargoHorario->getH_fin();

$oHash = new Hash();
$oHash->setArrayCamposHidden(['id_item_h' => $Qid_item_h]);
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

$oView = new ViewTwig('misas/controller');
echo $oView->render('horario_tarea.html.twig', $a_campos);