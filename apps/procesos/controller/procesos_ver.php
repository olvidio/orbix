<?php
use actividades\model\entity\ActividadAll;
use menus\model\PermisoMenu;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadTarea;
use procesos\model\entity\TareaProceso;
use web\Desplegable;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************


$Qmod = (string) \filter_input(INPUT_POST, 'mod');
$Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
$Qid_tipo_proceso = (integer) \filter_input(INPUT_POST, 'id_tipo_proceso');

// para crear un desplegable de oficinas. Uso los de los menus
$oPermMenus = new PermisoMenu;
$aOpcionesOficinas = $oPermMenus->lista_array();
$oDesplOficinas = new  Desplegable('id_of_responsable',$aOpcionesOficinas,'',true);


$oActividad = new ActividadAll();
$a_status = $oActividad->getArrayStatus();
$dep_num = 0;
$aDesplFasesPrevias = [];
$aDesplTareasPrevias = [];
$aMensajes_requisitos = [];
// para el form
if ($Qmod == 'editar') {

	$oTareaProceso = new TareaProceso(array('id_item'=>$Qid_item));
	$status = $oTareaProceso->getStatus();	
	$oDesplStatus = new Desplegable('status',$a_status,$status,true);
	$id_of_responsable = $oTareaProceso->getId_of_responsable();	
	$oDesplOficinas->setOpcion_sel($id_of_responsable);
	$aFases_previas = $oTareaProceso->getJson_fases_previas(TRUE);
	
	$id_fase=$oTareaProceso->getId_fase();	
    $oGesFase = new GestorActividadFase();
    $oDesplFase=$oGesFase->getListaActividadFases();
    $oDesplFase->setNombre('id_fase');
    $oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
    $oDesplFase->setOpcion_sel($id_fase);
    $oDesplFase->setBlanco(true);
    /* id_tarea */
    $id_tarea=$oTareaProceso->getId_tarea();	
    $oGesTarea = new GestorActividadTarea();
    $oDesplTarea=$oGesTarea->getListaActividadTareas($id_fase);
    $oDesplTarea->setNombre('id_tarea');
    if (!empty($id_tarea)) { $oDesplTarea->setOpcion_sel($id_tarea); }
    $oDesplTarea->setBlanco(true);
	/* id_fase_previa */
    $dep_num = count($aFases_previas);
	foreach ($aFases_previas as $oFaseP) {
	    $id_fase_previa = $oFaseP['id_fase'];
	    if (empty($id_fase_previa)) continue;
	    $id_tarea_previa = $oFaseP['id_tarea'];
	    $mensaje_requisito = $oFaseP['mensaje'];
        $aMensajes_requisitos[] = $mensaje_requisito;
	
		$oDesplFasePrevia=$oGesFase->getListaActividadFases();
		$oDesplFasePrevia->setNombre('id_fase_previa[]');
		$oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
		$oDesplFasePrevia->setOpcion_sel($id_fase_previa);
		$oDesplFasePrevia->setBlanco(true);
		$aDesplFasesPrevias[] = $oDesplFasePrevia;
		/* id_tarea_previa */
		$oGesTarea = new GestorActividadTarea();
		$oDesplTareaPrevia=$oGesTarea->getListaActividadTareas($id_fase_previa);
		$oDesplTareaPrevia->setNombre('id_tarea_previa[]');
        $oDesplTareaPrevia->setOpcion_sel($id_tarea_previa);
	   	$oDesplTareaPrevia->setBlanco(true);
	   	$aDesplTareasPrevias[] = $oDesplTareaPrevia;
	}
	if (empty($aFases_previas)) {
		$oDesplFasePrevia=$oGesFase->getListaActividadFases();
		$oDesplFasePrevia->setNombre('id_fase_previa[]');
		$oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
		$oDesplFasePrevia->setBlanco(true);
		$aDesplFasesPrevias[] = $oDesplFasePrevia;
		
		$oDesplTareaPrevia= new Desplegable('id_tarea_previa',array(),'',true);
	   	$aDesplTareasPrevias[] = $oDesplTareaPrevia;
        $aMensajes_requisitos[] = '';
	}
}
if ($Qmod == 'nuevo') {
	$status = '';
	$oDesplStatus = new Desplegable('status',$a_status,$status,true);

    $oGesFase = new GestorActividadFase();
    $oDesplFase=$oGesFase->getListaActividadFases();
    $oDesplFase->setNombre('id_fase');
    $oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
    $oDesplFase->setBlanco(true);
    $oDesplTarea= new Desplegable('id_tarea',array(),'',true);
    $oDesplFasePrevia=$oGesFase->getListaActividadFases();
    $oDesplFasePrevia->setNombre('id_fase_previa[]');
    $oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
    $oDesplFasePrevia->setBlanco(true);
    $aDesplFasesPrevias[] = $oDesplFasePrevia;
    $oDesplTareaPrevia= new Desplegable('id_tarea_previa[]',array(),'',true);
    $aDesplTareasPrevias[] = $oDesplTareaPrevia;
    $aMensajes_requisitos[] = '';
}

$url_ajax = "apps/procesos/controller/procesos_ajax.php";


$oHash = new web\Hash();
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
    ];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('procesos_ver.html.twig',$a_campos);