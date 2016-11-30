<?php
use actividadplazas\model as actividadplazas;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


switch ($_POST['que']) {
	case "update":
		$obj = json_decode($_POST['data']);
		//print_r($obj);
		$dl = json_decode($_POST['colName']);
		//print_r($dl);
		$id_activ =$obj->id;
		$dl_org =$obj->dlorg;
		$plazas =$obj->$dl;
		// $dl puede ser dlx-c para las concedidas, o dlx-p para las pedidas.
		$dl_sigla = substr($dl, 0, -2);
		// buscar el id de la dl
		$id_dl = 0;
		$gesDelegacion = new ubis\model\GestorDelegacion();
		$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl'=>$dl_sigla));
		if (is_array($cDelegaciones) && count($cDelegaciones)) {
			$id_dl = $cDelegaciones[0]->getId_dl();
		}
		//Si es la dl_org, son plazas concedidas, sino pedidas.
		$mi_dele = core\ConfigGlobal::mi_dele();
		$oActividadPlazasDl = new actividadplazas\ActividadPlazasDl(array('id_activ'=>$id_activ,'id_dl'=>$id_dl,'dl_tabla'=>$mi_dele));
		//if ($mi_dele == $dl_org) {
			$oActividadPlazasDl->setPlazas($plazas);
			
		//print_r($oActividadPlazasDl);
		if ($oActividadPlazasDl->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		//$oPosicion = new web\Posicion();
		//echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
		  
		break;
}