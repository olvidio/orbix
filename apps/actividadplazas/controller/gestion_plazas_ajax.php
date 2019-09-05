<?php
/**
 * Controlador encargado de actualizar las plazas en las actividades
 * 
 */
use actividades\model\entity as actividades;
use actividadplazas\model\entity as actividadplazas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$que = (string)  filter_input(INPUT_POST, 'que');

switch ($que) {
	case "update":
		$data = (string)  filter_input(INPUT_POST, 'data');
		$colName = (string)  filter_input(INPUT_POST, 'colName');
		$obj = json_decode($data);
		//print_r($obj);
		$dl = json_decode($colName);
		//print_r($dl);
		$id_activ =$obj->id;
		$dl_org =$obj->dlorg;
		$plazas =$obj->$dl;

		$mi_dele = core\ConfigGlobal::mi_delef();
		//Para las plazas totales
		if ($dl == 'tot' && $mi_dele == $dl_org ) {
			$oActividadDl = new actividades\ActividadDl(array('id_activ'=>$id_activ));
			$oActividadDl->DBCarregar();
			$oActividadDl->setPlazas($plazas);
			if ($oActividadDl->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
			}
		} else { //para el resto
			// $dl puede ser dlx-c para las concedidas, o dlx-p para las pedidas.
			$dl_sigla = substr($dl, 0, -2);
			// buscar el id de la dl
			$id_dl = 0;
			$gesDelegacion = new ubis\model\entity\GestorDelegacion();
			$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl'=>$dl_sigla));
			if (is_array($cDelegaciones) && count($cDelegaciones)) {
				$id_dl = $cDelegaciones[0]->getId_dl();
			}
			//Si es la dl_org, son plazas concedidas, sino pedidas.
			$oActividadPlazasDl = new actividadplazas\ActividadPlazasDl(array('id_activ'=>$id_activ,'id_dl'=>$id_dl,'dl_tabla'=>$mi_dele));
			$oActividadPlazasDl->DBCarregar();
			$oActividadPlazasDl->setPlazas($plazas);
				
			//print_r($oActividadPlazasDl);
			if ($oActividadPlazasDl->DBGuardar() === false) {
				echo _("hay un error, no se ha guardado");
			}
			//$oPosicion = new web\Posicion();
			//echo $oPosicion->ir_a("usuario_form.php?quien=usuario&id_usuario=".$_POST['id_usuario']);
		}  
		break;
	case 'lst_propietarios':
		$id_nom = (integer)  filter_input(INPUT_POST, 'id_nom');
		$id_activ = (integer)  filter_input(INPUT_POST, 'id_activ');
		
		$oPersona = \personas\model\entity\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err = "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
			exit($msg_err);
		}
		$obj_pau = str_replace("personas\\model\\entity\\",'',get_class($oPersona));
		$dl_de_paso = FALSE;
		if ($obj_pau === 'PersonaEx') {
			if (!empty($id_nom)) { //caso de modificar
				$dl_de_paso = $oPersona->getDl();
			} else {
			
			}
		}
		// valor por defecto
		$propietario = core\ConfigGlobal::mi_delef().">".$dl_de_paso;
		$gesActividadPlazas = new \actividadplazas\model\GestorResumenPlazas();
		$gesActividadPlazas->setId_activ($id_activ);
		$oDesplPosiblesPropietarios = $gesActividadPlazas->getPosiblesPropietarios($dl_de_paso);
		$oDesplPosiblesPropietarios->setNombre('propietario');
		$oDesplPosiblesPropietarios->setOpcion_sel($propietario);
		$oDesplPosiblesPropietarios->setBlanco(1);
		echo $oDesplPosiblesPropietarios->desplegable();
		break;
}