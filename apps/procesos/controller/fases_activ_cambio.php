<?php 
use web\Hash;
use web\TiposActividades;
use web\PeriodoQue;
use core\ConfigGlobal;

/**
* Página para cambiar la fase a un grupo de actividades.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		2/8/2011.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//include_once (ConfigGlobal::$dir_programas.'/func_web.php'); 

$ssfsv = '';
$sasistentes='';
$sactividad='';
$snom_tipo='';
$id_tipo_activ = '';

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qid_tipo_activ = (integer) \filter_input(INPUT_POST, 'id_tipo_activ');

$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');

//if (empty($_POST['year'])) $_POST['year']= date('Y'); 


if (!empty($Qid_tipo_activ))  {
	$oTipoActiv= new TiposActividades($Qid_tipo_activ);
} else {
	$oTipoActiv= new TiposActividades();
}

$sfsv=$oTipoActiv->getSfsvText();
$asistentes=$oTipoActiv->getAsistentesText();
$actividad=$oTipoActiv->getActividadText();
$nom_tipo=$oTipoActiv->getNom_tipoText();

$a_sfsv_posibles=$oTipoActiv->getSfsvPosibles();
$a_asistentes_posibles =$oTipoActiv->getAsistentesPosibles();
$a_actividades_posibles=$oTipoActiv->getActividadesPosibles();
$a_nom_tipo_posibles=$oTipoActiv->getNom_tipoPosibles();


$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);

$aOpciones =  array(
					'tot_any' => _('todo el año'),
					'trimestre_1'=>_('primer trimestre'),
					'trimestre_2'=>_('segundo trimestre'),
					'trimestre_3'=>_('tercer trimestre'),
					'trimestre_4'=>_('cuarto trimestre'),
					'separador'=>'---------',
					'otro'=>_('otro')
					);
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

$url_ajax = "apps/procesos/controller/fases_activ_cambio_ajax.php";
$url_ver = "apps/procesos/controller/fases_activ_ver.php";
$url='/programas/actividad_tipo_get.php';

$oHashLista = new Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setcamposForm('que!dl_propia!id_tipo_activ!id_fase_nueva!periodo!year!empiezamax!empiezamin');
$h_lista = $oHashLista->linkSinVal();

$oHashAct = new Hash();
$oHashAct->setUrl($url_ajax);
$oHashAct->setcamposForm('que!dl_propia!id_tipo_activ');
$h_actualizar = $oHashAct->linkSinVal();

	
		
$txt_eliminar = _("¿Esta seguro que desea borrar esta fase?");

$a_campos = ['oPosicion' => $oPosicion,
    'h_lista' => $h_lista,
    'h_actualizar' => $h_actualizar,
    'oActividadTipo' => $oActividadTipo,
    'oFormP' => $oFormP,
    'url_ajax' => $url_ajax,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new core\ViewTwig('procesos/controller');
echo $oView->render('fases_activ_cambio.html.twig',$a_campos);