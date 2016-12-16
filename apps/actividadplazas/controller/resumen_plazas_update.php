<?php
use actividades\model as actividades;
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

$que = (string)  filter_input(INPUT_POST, 'que');

switch ($que) {
	case "ceder":
		$id_activ = (integer)  filter_input(INPUT_POST, 'id_activ');
		$num_plazas = (integer)  filter_input(INPUT_POST, 'num_plazas');
		$dl = (string)  filter_input(INPUT_POST, 'dl');

		$mi_dele = core\ConfigGlobal::mi_dele();
		// buscar el id de la dl
		$id_dl = 0;
		$gesDelegacion = new ubis\model\GestorDelegacion();
		$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl'=>$mi_dele));
		if (is_array($cDelegaciones) && count($cDelegaciones)) {
			$id_dl = $cDelegaciones[0]->getId_dl();
		}
		//Si es la dl_org, son plazas concedidas, sino pedidas.
		$oActividadPlazasDl = new actividadplazas\ActividadPlazasDl(array('id_activ'=>$id_activ,'id_dl'=>$id_dl,'dl_tabla'=>$mi_dele));
		
		$json_cedidas = $oActividadPlazasDl->getCedidas();
		$oCedidas = json_decode($json_cedidas);
		if ($num_plazas == 0) {
			unset($oCedidas->$dl);
		} else {
			$oCedidas->$dl = $num_plazas;
		}
		$json_cedidas = json_encode($oCedidas);
		$oActividadPlazasDl->setCedidas($json_cedidas);
			
		//print_r($oActividadPlazasDl);
		if ($oActividadPlazasDl->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		//$oPosicion = new web\Posicion();
		//echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
		break;
}