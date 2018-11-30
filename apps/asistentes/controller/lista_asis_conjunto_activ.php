<?php 
use actividades\model\entity as actividades;

/**
* Lista los asistentes de una relación de actividades seleccionada
*
* 
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qstatus = (integer) \filter_input(INPUT_POST, 'status');
$Qstatus = empty($Qstatus)? actividades\ActividadAll::STATUS_ACTUAL : $Qstatus;
$Qid_tipo_activ = (string) \filter_input(INPUT_POST, 'id_tipo_activ');
$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (integer) \filter_input(INPUT_POST, 'year');
$Qyear = empty($Qyear)? date('Y') : $Qyear;
$Qdl_org = (string) \filter_input(INPUT_POST, 'dl_org');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = empty($Qempiezamin)? date('Y-m-d',mktime(0, 0, 0, date('m'), date('d')-40, date('Y'))) : $Qempiezamin;
$Qempiezamax = empty($Qempiezamax)? date('Y-m-d',mktime(0, 0, 0, date('m')+9, 0, date('Y'))) : $Qempiezamax;
	
// Condiciones de búsqueda.
$aWhere = array();
// Status
if ($Qstatus!=5) {
	$aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
	$Qsfsv = (string) \filter_input(INPUT_POST, 'sfsv');
	$Qsasistentes = (string) \filter_input(INPUT_POST, 'sasistentes');
	$Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
	$Qsnom_tipo = (string) \filter_input(INPUT_POST, 'snom_tipo');
	
	if (empty($Qssfsv)) {
		if ($mi_sfsv == 1) $Qssfsv = 'sv';
		if ($mi_sfsv == 2) $Qssfsv = 'sf';
	}
	$ssfsv = $Qssfsv;
	$sasistentes = empty($Qsasistentes)? '.' : $Qsasistentes;
	$sactividad = empty($Qsactividad)? '.' : $Qsactividad;
	$snom_tipo = empty($Qsnom_tipo)? '...' : $Qsnom_tipo;
	$oTipoActiv= new web\TiposActividades();
	$oTipoActiv->setSfsvText($ssfsv);
	$oTipoActiv->setAsistentesText($sasistentes);
	$oTipoActiv->setActividadText($sactividad);
	$Qid_tipo_activ=$oTipoActiv->getId_tipo_activ();
} else {
	$oTipoActiv= new web\TiposActividades($Qid_tipo_activ);
	$ssfsv=$oTipoActiv->getSfsvText();
	$sasistentes=$oTipoActiv->getAsistentesText();
	$sactividad=$oTipoActiv->getActividadText();
	$nom_tipo=$oTipoActiv->getNom_tipoText();
}
if ($Qid_tipo_activ!='......') {
	$aWhere['id_tipo_activ'] = "^$Qid_tipo_activ";
	$aOperador['id_tipo_activ'] = '~';
} 
// Lugar
if (!empty($Qid_ubi)) {
	$aWhere['id_ubi']=$Qid_ubi;
}
// periodo.
if (empty($Qperiodo) || $Qperiodo == 'otro') {
	$Qinicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
	$Qfin = empty($Qfin)? $Qempiezamax : $Qfin;
} else {
	$oPeriodo = new web\Periodo();
	$any=empty($Qyear)? date('Y')+1 : $Qyear;
	$oPeriodo->setAny($any);
	$oPeriodo->setPeriodo($Qperiodo);
	$Qinicio = $oPeriodo->getF_ini_iso();
	$Qfin = $oPeriodo->getF_fin_iso();
}
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
	$aWhere['f_fin'] = "'$Qinicio','$Qfin'";
	$aOperador['f_fin'] = 'BETWEEN';
} else {
	$aWhere['f_ini'] = "'$Qinicio','$Qfin'";
	$aOperador['f_ini'] = 'BETWEEN';
}
// dl Organizadora.
if (!empty($Qdl_org)) {
   $aWhere['dl_org'] = $Qdl_org; 
}
// Publicar
if (!empty($Qmodo) && $Qmodo == 'publicar') {
   $aWhere['publicado'] = 'f'; 
}
$aWhere['_ordre'] = 'f_ini';

//Para ver el tema plazas. Dos tablas:
//Listar primero las que organiza la dl, después el resto
$mi_dele = core\ConfigGlobal::mi_dele();

/////////////// actividades de mi dl ///////////////////
// si se ha puesto en condición de búsqueda
if (empty($Qdl_org) || $Qdl_org == $mi_dele) {
	$aWhere['dl_org'] = $mi_dele;

	$oListaPlazasDl = new \asistentes\model\listaplazas();
	$oListaPlazasDl->setMi_dele($mi_dele);
	$oListaPlazasDl->setWhere($aWhere);
	$oListaPlazasDl->setOperador($aOperador);
	$oListaPlazasDl->setId_tipo_activ($Qid_tipo_activ);
}
/////////////// actividades de otras dl ///////////////////
// si se ha puesto en condición de búsqueda
if (empty($Qdl_org) || $Qdl_org != $mi_dele) {
	if (!empty($Qdl_org)) {
		$aWhere['dl_org'] = $Qdl_org;
		$aOperador['dl_org'] = '=';
	} else {
		$aWhere['dl_org'] = $mi_dele;
		$aOperador['dl_org'] = '!=';
	}
	
	$oListaPlazasOtras = new \asistentes\model\listaplazas();
	$oListaPlazasOtras->setMi_dele($mi_dele);
	$oListaPlazasOtras->setWhere($aWhere);
	$oListaPlazasOtras->setOperador($aOperador);
	$oListaPlazasOtras->setId_tipo_activ($Qid_tipo_activ);
}


if (!empty($oListaPlazasDl)) {
	echo "<h3>".ucfirst(_("actividades de la dl"))."</h3>";
	// Lo pongo detrás del titulo, por si da error, saber que categoría hace referencia
	$oListaDl = $oListaPlazasDl->getLista();
	echo $oListaDl->listaPaginada();
}
if (!empty($oListaPlazasOtras)) {
	echo "<h3>".ucfirst(_("actividades de otras dl"))."</h3>";
	// Lo pongo detrás del titulo, por si da error, saber que categoría hace referencia
	$oListaOtras = $oListaPlazasOtras->getLista();
	echo $oListaOtras->listaPaginada();
}
