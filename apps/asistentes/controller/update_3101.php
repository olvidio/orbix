<?php
use actividades\model as actividades;
use actividadestudios\model as actividadestudios;
use dossiers\model as dossiers;
use asistentes\model as asistentes;
use personas\model as personas;
/**
 * Actualiza los datos de un objeto Asistente.
 * Al eliminar también elimina  las matrículas.
 *
 * @package	delegacion
 * @subpackage	actividades
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @ajax		23/8/2007.
 * @version 1.0
 * @created 24/09/2010
 *
 * @param array $_POST['sel'] con id_nom# o id_activ# si vengo de un select de una lista
 * @param integer $_POST['id_activ']
 * @param integer $_POST['id_nom']
 * @param string $_POST['mod']
 * @param boolean $_POST['propio'] optional
 * @param boolean $_POST['falta'] optional
 * @param boolean $_POST['est_ok'] optional
 * @param string $_POST['observ'] optional
 * @param integer $_POST['plaza'] optional
 * @param string $_POST['propietario'] optional
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/activ-personas/d_matriculas_activ_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=="p") { $id_activ=strtok($_POST['sel'][0],"#"); $id_nom=$_POST['id_pau']; }
	if ($_POST['pau']=="a") { $id_nom=strtok($_POST['sel'][0],"#"); $id_activ=$_POST['id_pau']; }
} else {
	empty($_POST['id_activ_old'])? $id_activ_old="" : $id_activ_old=$_POST['id_activ_old'];
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
	empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
}

function eliminar ($id_activ,$id_nom) {
	$msg_err = '';
	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom";
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\",'',$obj_persona);
	// hay que averiguar si la actividad es de la dl o de fuera.
	$oActividad  = new actividades\Actividad($id_activ);
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	if ($dl == core\ConfigGlobal::mi_dele()) {
		Switch($obj_persona) {
			case 'PersonaN':
			case 'PersonaNax':
			case 'PersonaAgd':
			case 'PersonaS':
			case 'PersonaSSSC':				
			case 'PersonaDl':
				$oAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			case 'PersonaIn':
				// Supongo que sólo debería modificar la dl origen.
				// $oAsistente=new asistentes\AsistenteIn(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				exit (_("Los datos de asistencia los modifica la dl del asistente"));
				break;
			case 'PersonaEx':
				$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
		}
	} else {
		if ($id_tabla == 'dl') {
			$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		} else {
			$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		}
	}
	if (empty($msg_err)) { 
		$oAsistente->DBCarregar();
		if ($oAsistente->DBEliminar() === false) {
			$msg_err = _('Hay un error, no se ha eliminado');
		}
	}

	// hay que cerrar el dossier para esta persona/actividad/ubi, si no tiene más:
	$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
	$oDossier->cerrar();
	$oDossier->DBGuardar();

	// también borro las matriculas que pueda tener
	$oGestorMatricula=new actividadestudios\GestorMatricula();
	foreach ($oGestorMatricula->getMatriculas(array('id_activ'=>$id_activ,'id_nom'=>$id_nom)) as $oMatricula) {
		if ($oMatricula->DBEliminar() === false) {
			$msg_err = _('Hay un error, no se ha eliminado');
		}
	}
	return $msg_err;
}
function plaza($id_nom){
	$msg_err = '';
	global $_POST;
	$id_activ = (string)  filter_input(INPUT_POST, 'id_activ');
	$plaza = (string)  filter_input(INPUT_POST, 'plaza');
	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom";
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\",'',$obj_persona);
	// hay que averiguar si la actividad es de la dl o de fuera.
	$oActividad  = new actividades\Actividad($id_activ);
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	if ($dl == core\ConfigGlobal::mi_dele()) {
		Switch($obj_persona) {
			case 'PersonaN':
			case 'PersonaNax':
			case 'PersonaAgd':
			case 'PersonaS':
			case 'PersonaSSSC':
			case 'PersonaDl':
				$oAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			case 'PersonaIn':
			case 'PersonaEx':
				$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
		}
	} else {
		if ($id_tabla == 'dl') {
			$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		} else {
			$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		}
	}
	$oAsistente->DBCarregar();
	isset($plaza)? $oAsistente->setPlaza($plaza) : $oAsistente->setPlaza();
	if ($oAsistente->DBGuardar() === false) {
		$msg_err = _('Hay un error, no se ha guardado');
	}
	return $msg_err;
}

function editar($id_activ,$id_nom){
	$msg_err = '';
	global $_POST;
	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom";
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\",'',$obj_persona);
	// hay que averiguar si la actividad es de la dl o de fuera.
	$oActividad  = new actividades\Actividad($id_activ);
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	if ($dl == core\ConfigGlobal::mi_dele()) {
		Switch($obj_persona) {
			case 'PersonaN':
			case 'PersonaNax':
			case 'PersonaAgd':
			case 'PersonaS':
			case 'PersonaSSSC':
			case 'PersonaDl':
				$oAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			case 'PersonaIn':
			case 'PersonaEx':
				$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
		}
	} else {
		if ($id_tabla == 'dl') {
			$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		} else {
			$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		}
	}
	$oAsistente->DBCarregar();
	isset($_POST['encargo'])? $oAsistente->setEncargo($_POST['encargo']) : $oAsistente->setEncargo();
	isset($_POST['cama'])? $oAsistente->setCama($_POST['cama']) : $oAsistente->setCama();
	isset($_POST['observ'])? $oAsistente->setObserv($_POST['observ']) : $oAsistente->setObserv();
	isset($_POST['plaza'])? $oAsistente->setPlaza($_POST['plaza']) : $oAsistente->setPlaza();
	isset($_POST['propio'])? $oAsistente->setPropio('t') : $oAsistente->setPropio('f');
	isset($_POST['est_ok'])? $oAsistente->setEst_ok('t') : $oAsistente->setEst_ok('f');
	isset($_POST['cfi'])? $oAsistente->setCfi('t') : $oAsistente->setCfi('f');
	isset($_POST['falta'])? $oAsistente->setFalta('t') : $oAsistente->setFalta('f');
	isset($_POST['cfi_con'])? $oAsistente->setCfi_con($_POST['cfi_con']) : $oAsistente->setCfi_con();
	isset($_POST['propietario'])? $oAsistente->setPropietario($_POST['propietario']) : $oAsistente->setPropietario();
	if ($oAsistente->DBGuardar() === false) {
		$msg_err = _('Hay un error, no se ha guardado');
	}
	return $msg_err;

}
$msg_err = '';
switch ($_POST['mod']) {
	//------------ cambiar PLAZA --------
	case "plaza":
		$msg_err = '';
		$arr = json_decode($_POST['lista_json']);
		foreach ($arr as $obj) {
			$id_nom = $obj->value;
			$id_nom = strtok($id_nom,'#'); // los cargos tienen más datos
			$msg_err .= plaza($id_nom);
		}
		$_POST['go_to'] = 'no';
		break;
	//------------ MOVER --------
	case "mover":
		$msg_err = eliminar ($id_activ_old,$id_nom);
		$msg_err .= editar($id_activ,$id_nom);
		break;
	//------------ BORRAR --------
	case "eliminar":
	$msg_err = eliminar ($id_activ,$id_nom);
		break;
	//------------ NUEVO --------
	//------------ EDITAR --------
	case "nuevo":
		// hay que abrir el dossier para esta persona/actividad/ubi:
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
		$oDossier->abrir();
		$oDossier->DBGuardar();
	case "editar":
		$msg_err = editar($id_activ,$id_nom);
		break;
}

if (empty($msg_err)) { 
	if (!empty($_POST['go_to']) && $_POST['go_to'] != 'no') {
		echo $oPosicion->ir_a($_POST['go_to']);
	} else {
		if ($_POST['go_to'] != 'no') {
			$oPosicion->setId_div('ir_a');
			echo $oPosicion->atras();
		}
	}
} else {
	echo $msg_err;
}	
?>
