<?php

use frontend\shared\model\ViewNewPhtml;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$Qid_item_h = (int)filter_input(INPUT_POST, 'id_item_h');

$EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
$oEncargoHorario = $EncargoHorarioRepository->findById($Qid_item_h);

$t_start = $oEncargoHorario !== null ? $oEncargoHorario->getH_ini() : '';
$t_end = $oEncargoHorario !== null ? $oEncargoHorario->getH_fin() : '';

$oHash = new Hash();
$oHash->setArrayCamposHidden(['id_item_h' => $Qid_item_h]);
$oHash->setUrl('apps/misas/controller/guardar_horario.php');
$oHash->setCamposForm('t_start!t_end');
$param_guardar = $oHash->getParamAjax();

$oHash->setUrl('apps/misas/controller/quitar_horario.php');
$oHash->setCamposForm('id_item');
$param_quitar = $oHash->getParamAjax();

$a_campos = [
    't_start' => $t_start,
    't_end' => $t_end,
    'param_guardar' => $param_guardar,
    'param_quitar' => $param_quitar,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('horario_tarea.phtml', $a_campos);
