<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');
$Qid_tipo_proceso = (int)filter_input(INPUT_POST, 'id_tipo_proceso');

$data = PostRequest::getDataFromUrl('/src/procesos/procesos_ver_data', [
    'mod' => $Qmod,
    'id_item' => $Qid_item,
]);

$a_oficinas = $data['a_oficinas'] ?? [];
$a_status = $data['a_status'] ?? [];
$a_fases = $data['a_fases'] ?? [];
$a_tareas = $data['a_tareas'] ?? [];
$a_fases_previas = $data['a_fases_previas'] ?? [];

$oDesplOficinas = new Desplegable('id_of_responsable', $a_oficinas, '', true);
$oDesplStatus = new Desplegable('status', $a_status, $data['status'] ?? '', true);

$oDesplFase = new Desplegable();
$oDesplFase->setOpciones($a_fases);
$oDesplFase->setNombre('id_fase');
$oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
$oDesplFase->setBlanco(true);

$oDesplTarea = new Desplegable();
$oDesplTarea->setOpciones($a_tareas);
$oDesplTarea->setNombre('id_tarea');
$oDesplTarea->setBlanco(true);

if ($Qmod === 'editar') {
    $oDesplOficinas->setOpcion_sel($data['id_of_responsable'] ?? '');
    $oDesplFase->setOpcion_sel($data['id_fase'] ?? '');
    if (!empty($data['id_tarea'])) {
        $oDesplTarea->setOpcion_sel($data['id_tarea']);
    }
}

$aDesplFasesPrevias = [];
$aDesplTareasPrevias = [];
$aMensajes_requisitos = [];
foreach ($a_fases_previas as $fila) {
    $oDesplFasePrevia = new Desplegable();
    $oDesplFasePrevia->setOpciones($a_fases);
    $oDesplFasePrevia->setNombre('id_fase_previa[]');
    $oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
    $oDesplFasePrevia->setBlanco(true);
    if (!empty($fila['id_fase_previa'])) {
        $oDesplFasePrevia->setOpcion_sel($fila['id_fase_previa']);
    }
    $aDesplFasesPrevias[] = $oDesplFasePrevia;

    $oDesplTareaPrevia = new Desplegable();
    $oDesplTareaPrevia->setOpciones($fila['a_tareas_previa'] ?? []);
    $oDesplTareaPrevia->setNombre('id_tarea_previa[]');
    $oDesplTareaPrevia->setBlanco(true);
    if (!empty($fila['id_tarea_previa'])) {
        $oDesplTareaPrevia->setOpcion_sel($fila['id_tarea_previa']);
    }
    $aDesplTareasPrevias[] = $oDesplTareaPrevia;

    $aMensajes_requisitos[] = $fila['mensaje_requisito'] ?? '';
}
$dep_num = count($aDesplFasesPrevias);

$webBase = rtrim(ConfigGlobal::getWeb(), '/');
$url_update = $webBase . '/src/procesos/procesos_update';
$url_depende = $webBase . '/src/procesos/procesos_depende';

$oHash = new Hash();
$oHash->setUrl($url_update);
$oHash->setCamposForm('dep_num!id_fase!id_fase_previa!id_tarea!id_tarea_previa!mensaje_requisito!id_of_responsable!status');
$oHash->setCamposNo('id_fase_previa[]!id_tarea_previa[]!mensaje_requisito[]');
$oHash->setCamposChk('id_tarea_previa');
$a_camposHidden = [
    'id_item' => $Qid_item,
    'id_tipo_proceso' => $Qid_tipo_proceso,
];
$oHash->setArraycamposHidden($a_camposHidden);

$oHashDepende = new Hash();
$oHashDepende->setUrl($url_depende);
$oHashDepende->setCamposForm('acc!valor_depende');
$h_depende = $oHashDepende->linkSinValParams();

$a_campos = [
    'oHash' => $oHash,
    'h_depende' => $h_depende,
    'url_update' => $url_update,
    'url_depende' => $url_depende,
    'oDesplFase' => $oDesplFase,
    'oDesplTarea' => $oDesplTarea,
    'oDesplStatus' => $oDesplStatus,
    'oDesplOficinas' => $oDesplOficinas,
    'dep_num' => $dep_num,
    'aDesplFasesPrevias' => $aDesplFasesPrevias,
    'aDesplTareasPrevias' => $aDesplTareasPrevias,
    'aMensajes_requisitos' => $aMensajes_requisitos,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('procesos_ver.html.twig', $a_campos);
