<?php
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

use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use dossiers\model\entity as dossiers;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
$Qmod = (string) \filter_input(INPUT_POST,'mod');
$Qpau = (string) \filter_input(INPUT_POST,'pau');

//En el caso de eliminar desde la lista de cargos
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	if ($Qpau=="p") {
		$Qid_activ = (integer) strtok($a_sel[0],"#");
		$Qid_asignatura = (integer) strtok("#");
		$Qid_nom = (integer) \filter_input(INPUT_POST,'id_pau');
	}
	if ($Qpau=="a") {
		$Qid_nom = (integer) strtok($a_sel[0],"#");
		$Qid_asignatura = (integer) strtok("#");
		$Qid_activ = (integer) \filter_input(INPUT_POST,'id_pau');
	}
} else { // desde el formulario
	$Qid_activ = (integer) \filter_input(INPUT_POST,'id_activ');
	$Qid_activ_old = (integer) \filter_input(INPUT_POST,'id_activ_old');
	$Qid_nom = (integer) \filter_input(INPUT_POST,'id_nom');
}
	
// -------------- funciones -----------------------

function eliminar ($id_activ,$id_nom) {
	$msg_err = '';
	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\entity\\",'',$obj_persona);
	// hay que averiguar si la actividad es de la dl o de fuera.
	$oActividad  = new actividades\Actividad($id_activ);
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	$oAsistente = asistentes\Asistente::getClaseAsistente($obj_persona,$dl,$id_tabla);
	$oAsistente->setPrimary_key(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
	$oAsistente->DBCarregar();
	if ($oAsistente->DBEliminar() === false) {
		$msg_err = _("hay un error, no se ha eliminado");
	}

	// hay que cerrar el dossier para esta persona/actividad/ubi, si no tiene más:
	$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
	$oDossier->cerrar();
	$oDossier->DBGuardar();

	// también borro las matriculas que pueda tener
	$oGestorMatricula=new actividadestudios\GestorMatricula();
	foreach ($oGestorMatricula->getMatriculas(array('id_activ'=>$id_activ,'id_nom'=>$id_nom)) as $oMatricula) {
		if ($oMatricula->DBEliminar() === false) {
			$msg_err = _("hay un error, no se ha eliminado");
		}
	}
	return $msg_err;
}
function plaza($id_nom){
	$msg_err = '';
	$id_activ = (string)  filter_input(INPUT_POST, 'id_activ');
	$plaza = (string)  filter_input(INPUT_POST, 'plaza');
	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\entity\\",'',$obj_persona);
	// hay que averiguar si la actividad es de la dl o de fuera.
	$oActividad  = new actividades\Actividad($id_activ);
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	$oAsistente = asistentes\Asistente::getClaseAsistente($obj_persona,$dl,$id_tabla);
	$oAsistente->setPrimary_key(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
	$oAsistente->DBCarregar();
	isset($plaza)? $oAsistente->setPlaza($plaza) : $oAsistente->setPlaza();
	if ($oAsistente->DBGuardar() === false) {
		$msg_err = _("hay un error, no se ha guardado");
	}
	return $msg_err;
}

function editar($id_activ,$id_nom){
	$msg_err = '';
	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err = "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\entity\\",'',$obj_persona);
	// hay que averiguar si la actividad es de la dl o de fuera.
	$oActividad  = new actividades\Actividad($id_activ);
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$id_tabla = $oActividad->getId_tabla();
	$oAsistente = asistentes\Asistente::getClaseAsistente($obj_persona,$dl,$id_tabla);
	$oAsistente->setPrimary_key(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
	$oAsistente->DBCarregar();
	
	$Qencargo = (string) \filter_input(INPUT_POST, 'encargo');
	$Qcama = (string) \filter_input(INPUT_POST, 'cama');
	$Qobserv = (string) \filter_input(INPUT_POST, 'observ');
	$Qobserv_est = (string) \filter_input(INPUT_POST, 'observ_est');
	$Qplaza = (integer) \filter_input(INPUT_POST, 'plaza');
	$Qpropio = (string) \filter_input(INPUT_POST, 'propio');
	$Qest_ok = (string) \filter_input(INPUT_POST, 'est_ok');
	$Qcfi = (string) \filter_input(INPUT_POST, 'cfi');
	$Qfalta = (string) \filter_input(INPUT_POST, 'falta');
	$Qcfi_con = (string) \filter_input(INPUT_POST, 'cfi_con');
	$Qpropietario = (string) \filter_input(INPUT_POST, 'propietario');
	if ($Qpropietario === 'xxx') { $Qpropietario = ''; }

	isset($Qencargo)? $oAsistente->setEncargo($Qencargo) : $oAsistente->setEncargo();
	isset($Qcama)? $oAsistente->setCama($Qcama) : $oAsistente->setCama();
	isset($Qobserv)? $oAsistente->setObserv($Qobserv) : $oAsistente->setObserv();
	isset($Qobserv_est)? $oAsistente->setObserv_est($Qobserv_est) : $oAsistente->setObserv_est();
	isset($Qplaza)? $oAsistente->setPlaza($Qplaza) : $oAsistente->setPlaza();
	empty($Qpropio)? $oAsistente->setPropio('f') : $oAsistente->setPropio('t');
	empty($Qest_ok)? $oAsistente->setEst_ok('f') : $oAsistente->setEst_ok('t');
	empty($Qcfi)? $oAsistente->setCfi('f') : $oAsistente->setCfi('t');
	empty($Qfalta)? $oAsistente->setFalta('f') : $oAsistente->setFalta('t');
	isset($Qcfi_con)? $oAsistente->setCfi_con($Qcfi_con) : $oAsistente->setCfi_con();
	// Si no es epecificado, al poner la plaza ya se pone al propietario
	!empty($Qpropietario)? $oAsistente->setPropietario($Qpropietario) : FALSE;
	if ($oAsistente->DBGuardar() === false) {
		$msg_err = _("hay un error, no se ha guardado");
	}
	return $msg_err;

}

switch ($Qmod) {
	//------------ cambiar PLAZA --------
	case "plaza":
		$msg_err = '';
		$Qlista_json = (string) \filter_input(INPUT_POST, 'lista_json');
		$arr = json_decode($Qlista_json);
		foreach ($arr as $obj) {
			$id_nom = $obj->value;
			$id_nom = (integer) strtok($id_nom,'#'); // los cargos tienen más datos
			$msg_err .= plaza($id_nom);
		}
		break;
	//------------ MOVER --------
	case "mover":
		$msg_err = eliminar($Qid_activ_old,$Qid_nom);
		$msg_err .= editar($Qid_activ,$Qid_nom);
		break;
	//------------ BORRAR --------
	case "eliminar":
	$msg_err = eliminar($Qid_activ,$Qid_nom);
		break;
	//------------ NUEVO --------
	//------------ EDITAR --------
	case "nuevo":
		// hay que abrir el dossier para esta persona/actividad/ubi:
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$Qid_nom,'id_tipo_dossier'=>1301));
		$oDossier->abrir();
		$oDossier->DBGuardar();
	case "editar":
		$msg_err = editar($Qid_activ,$Qid_nom);
		break;
}


if (empty($msg_err)) { 
	echo $msg_err;
}	