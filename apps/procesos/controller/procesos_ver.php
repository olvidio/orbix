<?php

use core\ViewTwig;
use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenu;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************


$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_tipo_proceso = (integer)filter_input(INPUT_POST, 'id_tipo_proceso');

// para crear un desplegable de oficinas. Uso los de los menus
$oPermMenus = new PermisoMenu;
$aOpcionesOficinas = $oPermMenus->lista_array();
$oDesplOficinas = new  Desplegable('id_of_responsable', $aOpcionesOficinas, '', true);

$a_status = StatusId::getArrayStatus();
$dep_num = 0;
$aDesplFasesPrevias = [];
$aDesplTareasPrevias = [];
$aMensajes_requisitos = [];
// para el form
$ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
$TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
if ($Qmod === 'editar') {
    $oTareaProceso = $TareaProcesoRepository->findById($Qid_item);
    $status = $oTareaProceso->getStatus();
    $oDesplStatus = new Desplegable('status', $a_status, $status, true);
    $id_of_responsable = $oTareaProceso->getId_of_responsable();
    $oDesplOficinas->setOpcion_sel($id_of_responsable);
    $aFases_previas = $oTareaProceso->getJson_fases_previas(TRUE);

    $id_fase = $oTareaProceso->getId_fase();
    $aOpciones = $ActividadFaseRepository->getArrayActividadFases();
    $oDesplFase = new Desplegable();
    $oDesplFase->setBlanco(true);
    $oDesplFase->setOpciones($aOpciones);
    $oDesplFase->setNombre('id_fase');
    $oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
    $oDesplFase->setOpcion_sel($id_fase);
    $oDesplFase->setBlanco(true);
    /* id_tarea */
    $id_tarea = $oTareaProceso->getId_tarea();
    $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
    $aOpciones = $ActividadTareaRepository->getArrayActividadTareas($id_fase);
    $oDesplTarea = new Desplegable();
    $oDesplTarea->setOpciones($aOpciones);
    $oDesplTarea->setBlanco(true);
    $oDesplTarea->setNombre('id_tarea');
    if (!empty($id_tarea)) {
        $oDesplTarea->setOpcion_sel($id_tarea);
    }
    /* id_fase_previa */
    $dep_num = count($aFases_previas);
    foreach ($aFases_previas as $oFaseP) {
        $id_fase_previa = $oFaseP['id_fase'];
        if (empty($id_fase_previa)) continue;
        $id_tarea_previa = $oFaseP['id_tarea'];
        $mensaje_requisito = $oFaseP['mensaje'];
        $aMensajes_requisitos[] = $mensaje_requisito;

        $aOpciones = $ActividadFaseRepository->getArrayActividadFases();
        $oDesplFasePrevia = new Desplegable();
        $oDesplFasePrevia->setOpciones($aOpciones);
        $oDesplFasePrevia->setNombre('id_fase_previa[]');
        $oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
        $oDesplFasePrevia->setOpcion_sel($id_fase_previa);
        $oDesplFasePrevia->setBlanco(true);
        $aDesplFasesPrevias[] = $oDesplFasePrevia;
        /* id_tarea_previa */
        $aOpciones = $ActividadTareaRepository->getArrayActividadTareas($id_fase_previa);
        $oDesplTareaPrevia = new Desplegable();
        $oDesplTareaPrevia->setOpciones($aOpciones);
        $oDesplTareaPrevia->setNombre('id_tarea_previa[]');
        $oDesplTareaPrevia->setOpcion_sel($id_tarea_previa);
        $oDesplTareaPrevia->setBlanco(true);
        $aDesplTareasPrevias[] = $oDesplTareaPrevia;
    }
    if (empty($aFases_previas)) {
        $aOpciones = $ActividadFaseRepository->getArrayActividadFases();
        $oDesplFasePrevia = new Desplegable();
        $oDesplFasePrevia->setOpciones($aOpciones);
        $oDesplFasePrevia->setNombre('id_fase_previa[]');
        $oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
        $oDesplFasePrevia->setBlanco(true);
        $aDesplFasesPrevias[] = $oDesplFasePrevia;

        $oDesplTareaPrevia = new Desplegable('id_tarea_previa', [], '', true);
        $aDesplTareasPrevias[] = $oDesplTareaPrevia;
        $aMensajes_requisitos[] = '';
    }
}
if ($Qmod === 'nuevo') {
    $status = '';
    $oDesplStatus = new Desplegable('status', $a_status, $status, true);

    $aOpciones = $ActividadFaseRepository->getArrayActividadFases();
    $oDesplFase = new Desplegable();
    $oDesplFase->setOpciones($aOpciones);
    $oDesplFase->setNombre('id_fase');
    $oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
    $oDesplFase->setBlanco(true);
    $oDesplTarea = new Desplegable('id_tarea', [], '', true);
    $aOpciones = $ActividadFaseRepository->getArrayActividadFases();
    $oDesplFasePrevia = new Desplegable();
    $oDesplFasePrevia->setOpciones($aOpciones);
    $oDesplFasePrevia->setNombre('id_fase_previa[]');
    $oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
    $oDesplFasePrevia->setBlanco(true);
    $aDesplFasesPrevias[] = $oDesplFasePrevia;
    $oDesplTareaPrevia = new Desplegable('id_tarea_previa[]', [], '', true);
    $aDesplTareasPrevias[] = $oDesplTareaPrevia;
    $aMensajes_requisitos[] = '';
}

$url_ajax = "apps/procesos/controller/procesos_ajax.php";


$oHash = new Hash();
$oHash->setCamposForm('dep_num!id_fase!id_fase_previa!id_tarea!id_tarea_previa!mensaje_requisito!id_of_responsable!status');
$oHash->setCamposNo('que!id_fase_previa[]!id_tarea_previa[]!mensaje_requisito[]');
$oHash->setCamposChk('id_tarea_previa');
$a_camposHidden = [
    'que' => '',
    'id_item' => $Qid_item,
    'id_tipo_proceso' => $Qid_tipo_proceso,
];
$oHash->setArraycamposHidden($a_camposHidden);


$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'oDesplFase' => $oDesplFase,
    'oDesplTarea' => $oDesplTarea,
    'oDesplStatus' => $oDesplStatus,
    'oDesplOficinas' => $oDesplOficinas,
    'dep_num' => $dep_num,
    'aDesplFasesPrevias' => $aDesplFasesPrevias,
    'aDesplTareasPrevias' => $aDesplTareasPrevias,
    'aMensajes_requisitos' => $aMensajes_requisitos,
    'oDesplFasesPrevias' => $oDesplFasePrevia,
    'oDesplTareasPrevias' => $oDesplTareaPrevia,
];

$oView = new ViewTwig('procesos/controller');
$oView->renderizar('procesos_ver.html.twig', $a_campos);