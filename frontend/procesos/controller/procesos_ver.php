<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\procesos\helpers\ProcesosPostInput;
use frontend\procesos\helpers\ProcesosPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qmod = ProcesosPostInput::postString('mod');
$Qid_item = ProcesosPostInput::postInt('id_item');
$Qid_tipo_proceso = ProcesosPostInput::postInt('id_tipo_proceso');

$data = PostRequest::getDataFromUrl('/src/procesos/procesos_ver_data', [
    'mod' => $Qmod,
    'id_item' => $Qid_item,
]);
$ver = ProcesosPayload::verFromPayload($data);

$oDesplOficinas = new Desplegable('id_of_responsable', $ver['a_oficinas'], '', true);
$oDesplStatus = new Desplegable('status', $ver['a_status'], $ver['status'], true);

$oDesplFase = new Desplegable();
$oDesplFase->setOpciones($ver['a_fases']);
$oDesplFase->setNombre('id_fase');
$oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
$oDesplFase->setBlanco(true);

$oDesplTarea = new Desplegable();
$oDesplTarea->setOpciones($ver['a_tareas']);
$oDesplTarea->setNombre('id_tarea');
$oDesplTarea->setBlanco(true);

if ($Qmod === 'editar') {
    $oDesplOficinas->setOpcion_sel($ver['id_of_responsable']);
    $oDesplFase->setOpcion_sel($ver['id_fase']);
    if ($ver['id_tarea'] !== '') {
        $oDesplTarea->setOpcion_sel($ver['id_tarea']);
    }
}

$aDesplFasesPrevias = [];
$aDesplTareasPrevias = [];
$aMensajes_requisitos = [];
foreach ($ver['a_fases_previas'] as $fila) {
    $previa = ProcesosPayload::fasePreviaRow($fila);
    $oDesplFasePrevia = new Desplegable();
    $oDesplFasePrevia->setOpciones($ver['a_fases']);
    $oDesplFasePrevia->setNombre('id_fase_previa[]');
    $oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
    $oDesplFasePrevia->setBlanco(true);
    if ($previa['id_fase_previa'] !== '') {
        $oDesplFasePrevia->setOpcion_sel($previa['id_fase_previa']);
    }
    $aDesplFasesPrevias[] = $oDesplFasePrevia;

    $oDesplTareaPrevia = new Desplegable();
    $oDesplTareaPrevia->setOpciones($previa['a_tareas_previa']);
    $oDesplTareaPrevia->setNombre('id_tarea_previa[]');
    $oDesplTareaPrevia->setBlanco(true);
    if ($previa['id_tarea_previa'] !== '') {
        $oDesplTareaPrevia->setOpcion_sel($previa['id_tarea_previa']);
    }
    $aDesplTareasPrevias[] = $oDesplTareaPrevia;

    $aMensajes_requisitos[] = $previa['mensaje_requisito'];
}
$dep_num = count($aDesplFasesPrevias);

$apiBase = AppUrlConfig::getApiBaseUrl();
$url_update = AppUrlConfig::srcBrowserUrl('/src/procesos/procesos_update');
$url_depende = AppUrlConfig::srcBrowserUrl('/src/procesos/procesos_depende');

$oHash = new HashFront();
$oHash->setUrl($url_update);
$oHash->setCamposForm('dep_num!id_fase!id_fase_previa!id_tarea!id_tarea_previa!mensaje_requisito!id_of_responsable!status');
$oHash->setCamposNo('id_fase_previa[]!id_tarea_previa[]!mensaje_requisito[]');
$oHash->setCamposChk('id_tarea_previa');
$a_camposHidden = [
    'id_item' => $Qid_item,
    'id_tipo_proceso' => $Qid_tipo_proceso,
];
$oHash->setArraycamposHidden($a_camposHidden);

$oHashDepende = new HashFront();
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

$oView = new ViewNewTwig('frontend/procesos/controller');
$oView->renderizar('procesos_ver.html.twig', $a_campos);
