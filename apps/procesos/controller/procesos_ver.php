<?php
use actividades\model\entity\ActividadAll;
use procesos\model\entity\Proceso;
use procesos\model\entity\GestorActividadFase;
use procesos\model\entity\GestorActividadTarea;
use procesos\model\entity\GestorProceso;
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

$a_status = ActividadAll::ARRAY_STATUS_TXT;
// para el form
if ($Qmod == 'editar') {

	$oFicha = new Proceso(array('id_item'=>$Qid_item));
	$n_orden=$oFicha->getN_orden();	
	$status=$oFicha->getStatus();	
	$oDesplStatus= new Desplegable('status',$a_status,$status,true);
	$of_responsable=$oFicha->getOf_responsable();	
	$mensaje_requisito=$oFicha->getMensaje_requisito();	

	$v1=$oFicha->getId_fase();	
		$oGesFase = new GestorActividadFase();
		$oDesplFase=$oGesFase->getListaActividadFases();
		$oDesplFase->setNombre('id_fase');
		$oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
		$oDesplFase->setOpcion_sel($v1);
		$oDesplFase->setBlanco(true);
		/* id_tarea */
		$v2=$oFicha->getId_tarea();	
		$oGesTarea = new GestorActividadTarea();
		$oDesplTarea=$oGesTarea->getListaActividadTareas($v1);
		$oDesplTarea->setNombre('id_tarea');
		if (!empty($v2)) { $oDesplTarea->setOpcion_sel($v2); }
		$oDesplTarea->setBlanco(true);
	/* id_fase_previa */
	$v1=$oFicha->getId_fase_previa();	
	if (!empty($v1)) {
		$oDesplFasePrevia=$oGesFase->getListaActividadFases();
		$oDesplFasePrevia->setNombre('id_fase_previa');
		$oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
		$oDesplFasePrevia->setOpcion_sel($v1);
		$oDesplFasePrevia->setBlanco(true);
		/* id_tarea_previa */
		$v2=$oFicha->getId_tarea_previa();	
		$oGesTarea = new GestorActividadTarea();
		$oDesplTareaPrevia=$oGesTarea->getListaActividadTareas($v1);
		$oDesplTareaPrevia->setNombre('id_tarea_previa');
		if (!empty($v2)) { $oDesplTareaPrevia->setOpcion_sel($v2); }
	   	$oDesplTareaPrevia->setBlanco(true);
	} else {
		$oDesplFasePrevia=$oGesFase->getListaActividadFases();
		$oDesplFasePrevia->setNombre('id_fase_previa');
		$oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
		$oDesplFasePrevia->setBlanco(true);
		$oDesplTareaPrevia= new Desplegable('id_tarea_previa',array(),'',true);
	}
}
if ($Qmod == 'nuevo') {
	// lo pongo el último
	$oGesProceso = new GestorProceso();
	$oUltimoProceso = $oGesProceso->getProcesos(array('id_tipo_proceso'=>$Qid_tipo_proceso,'_ordre'=>'n_orden'));
	$num=count($oUltimoProceso);
	$n_orden=$num + 1;
	$oFicha = new Proceso();
	$status='';
	$oDesplStatus= new Desplegable('status',$a_status,$status,true);
	$of_responsable='';
	$mensaje_requisito='';
		$oGesFase = new GestorActividadFase();
		$oDesplFase=$oGesFase->getListaActividadFases();
		$oDesplFase->setNombre('id_fase');
		$oDesplFase->setAction('fnjs_get_depende(\'#id_fase\',\'#id_tarea\')');
		$oDesplFase->setBlanco(true);
		$oDesplTarea= new Desplegable('id_tarea',array(),'',true);
		$oDesplFasePrevia=$oGesFase->getListaActividadFases();
		$oDesplFasePrevia->setNombre('id_fase_previa');
		$oDesplFasePrevia->setAction('fnjs_get_depende(\'#id_fase_previa\',\'#id_tarea_previa\')');
		$oDesplFasePrevia->setBlanco(true);
		$oDesplTareaPrevia= new Desplegable('id_tarea_previa',array(),'',true);
}

$url_ajax = "apps/procesos/controller/procesos_ajax.php";


$oHash = new web\Hash();
$oHash->setCamposNo('que');
$oHash->setCamposForm('id_fase!id_fase_previa!id_tarea!id_tarea_previa!mensaje_requisito!of_responsable!status');
$a_camposHidden = [
    'que' => '',
    'id_item' => $Qid_item, 
    'id_tipo_proceso' => $Qid_tipo_proceso,
    'n_orden' => $n_orden,
];
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'of_responsable' => $of_responsable,
    'mensaje_requisito' => $mensaje_requisito, 
    'oDesplFase' => $oDesplFase,
    'oDesplTarea' => $oDesplTarea,
    'oDesplStatus' => $oDesplStatus,
    'oDesplFasePrevia' => $oDesplFasePrevia,
    'oDesplTareaPrevia' => $oDesplTareaPrevia,
    ];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('procesos_ver.html.twig',$a_campos);