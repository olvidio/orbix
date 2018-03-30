<?php 
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use asistentes\model as asistentes;
use personas\model as personas;
use ubis\model as ubis;

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

$Qstatus = empty($_POST['status'])? 2 : $_POST['status'];
$Qid_tipo_activ = empty($_POST['id_tipo_activ'])? '' : $_POST['id_tipo_activ'];
$Qid_ubi = empty($_POST['id_ubi'])? '' : $_POST['id_ubi'];
$Qperiodo = empty($_POST['periodo'])? '' : $_POST['periodo'];
$Qinicio = empty($_POST['inicio'])? '' : $_POST['inicio'];
$Qfin = empty($_POST['fin'])? '' : $_POST['fin'];
$Qyear = empty($_POST['year'])? (integer) date('Y') : $_POST['year'];
$Qdl_org = empty($_POST['dl_org'])? '' : $_POST['dl_org'];
$Qempiezamin = empty($_POST['empiezamin'])? date('d/m/Y',mktime(0, 0, 0, date('m'), date('d')-40, date('Y'))) : $_POST['empiezamin'];
$Qempiezamax = empty($_POST['empiezamax'])? date('d/m/Y',mktime(0, 0, 0, date('m')+9, 0, date('Y'))) : $_POST['empiezamax'];
	
// Condiciones de búsqueda.
$aWhere = array();
// Status
if ($Qstatus!=5) {
	$aWhere['status'] = $Qstatus;
}
// Id tipo actividad
if (empty($Qid_tipo_activ)) {
	if (empty($_POST['ssfsv'])) {
		if ($mi_sfsv == 1) $_POST['ssfsv'] = 'sv';
		if ($mi_sfsv == 2) $_POST['ssfsv'] = 'sf';
	}
	$ssfsv = $_POST['ssfsv'];
	$sasistentes = empty($_POST['sasistentes'])? '.' : $_POST['sasistentes'];
	$sactividad = empty($_POST['sactividad'])? '.' : $_POST['sactividad'];
	$snom_tipo = empty($_POST['snom_tipo'])? '...' : $_POST['snom_tipo'];
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
	$Qinicio = $oPeriodo->getF_ini();
	$Qfin = $oPeriodo->getF_fin();
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
if (empty($Qdl_org)) { // si se ha puesto en condición de búsqueda
	$aWhere['dl_org'] = $mi_dele;
}

$oListaPlazas = new \asistentes\model\listaplazas();
$oListaPlazas->setMi_dele($mi_dele);
$oListaPlazas->setWhere($aWhere);
$oListaPlazas->setOperador($aOperador);
$oListaPlazas->setId_tipo_activ($Qid_tipo_activ);

echo "<h3>"._("Actividades de la dl")."</h3>";
// Lo pongo detrás del titulo, por si da error, saber que categoría hace referencia
$oLista = $oListaPlazas->getLista();
echo $oLista->listaPaginada();

/////////////// actividades de otras dl ///////////////////
if (empty($Qdl_org)) { // si se ha puesto en condición de búsqueda
	$aWhere['dl_org'] = $mi_dele;
	$aOperador['dl_org'] = '!=';
}

$oListaPlazas->setWhere($aWhere);
$oListaPlazas->setOperador($aOperador);

echo "<h3>"._("Actividades de otras dl")."</h3>";
// Lo pongo detrás del titulo, por si da error, saber que categoría hace referencia
$oLista = $oListaPlazas->getLista();
echo $oLista->listaPaginada();