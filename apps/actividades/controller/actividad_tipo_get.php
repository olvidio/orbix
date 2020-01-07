<?php
use ubis\model\entity\GestorDelegacion;

/**
 * Devuelvo un desplegable con los valores posibles del tipo de actividad
 *  segun el valor de entrada.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qentrada = (string) \filter_input(INPUT_POST, 'entrada');
$Qsalida = (string) \filter_input(INPUT_POST, 'salida');

switch ($Qsalida) {
	case "asistentes":
		$aux=$Qentrada.'.....';
		$oTipoActiv= new web\TiposActividades($aux);
		$a_asistentes_posibles =$oTipoActiv->getAsistentesPosibles();
		$oDespl = new web\Desplegable('iasistentes_val',$a_asistentes_posibles,'',true);
	   	$oDespl->setAction('fnjs_actividad()');
	   	$oDespl->setValBlanco('.');
	   	$oDespl->setOpcion_sel('.');
		echo $oDespl->desplegable();
	break;
	case "actividad":
		$aux=$Qentrada.'....';
		$oTipoActiv= new web\TiposActividades($aux);
		$a_actividades_posibles=$oTipoActiv->getActividadesPosibles();
		$oDespl = new web\Desplegable('iactividad_val',$a_actividades_posibles,'',true);
	   	$oDespl->setAction('fnjs_nom_tipo()');
	   	$oDespl->setValBlanco('.');
	   	$oDespl->setOpcion_sel('.');
		echo $oDespl->desplegable();
	break;
	case "nom_tipo":
		$aux=$Qentrada.'...';
		$oTipoActiv= new web\TiposActividades($aux);
		$a_nom_tipo_posibles=$oTipoActiv->getNom_tipoPosibles();
		$oDespl = new web\Desplegable('inom_tipo_val',$a_nom_tipo_posibles,'',true);
	   	$oDespl->setAction('fnjs_act_id_activ()');
	   	$oDespl->setValBlanco('...');
	   	$oDespl->setOpcion_sel('...');
		echo $oDespl->desplegable();
	 break;
	 case "lugar":
		$Qisfsv = (integer) \filter_input(INPUT_POST, 'isfsv');
		$Qssfsv = (string) \filter_input(INPUT_POST, 'ssfsv');
		$Qopcion_sel = (string) \filter_input(INPUT_POST, 'opcion_sel');
		 
		$oActividadLugar = new \actividades\model\ActividadLugar();
		$oActividadLugar->setIsfsv($Qisfsv);
		$oActividadLugar->setSsfsv($Qssfsv);
		$oActividadLugar->setOpcion_sel($Qopcion_sel);

		$oDesplegableCasas = $oActividadLugar->getLugaresPosibles($Qentrada); 
		echo $oDesplegableCasas->desplegable();
        break;
        // falta tarifa.
        
	 case "dl_org";
		$sfsv=$Qentrada;
		$oGesDl = new GestorDelegacion();
		$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones($sfsv);
		$oDesplDelegacionesOrg->setNombre('dl_org');
		echo $oDesplDelegacionesOrg->desplegable();
	   break;
}
