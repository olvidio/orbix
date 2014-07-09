<?php
use actividades\model as actividades;
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
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/activ-personas/d_matriculas_activ_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//include_once ("./func_dossiers.php");

if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=="p") { $id_activ=strtok($_POST['sel'][0],"#"); $id_nom=$_POST['id_pau']; }
	if ($_POST['pau']=="a") { $id_nom=strtok($_POST['sel'][0],"#"); $id_activ=$_POST['id_pau']; }
} else {
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
	empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
}

switch ($_POST['mod']) {
	//------------ BORRAR --------
	case "eliminar":
		// hay que averiguar si la actividad es de la dl o de fuera.
		$oActividad  = new actividades\Actividad($id_activ);
		$id_tabla = $oActividad->getId_tabla();
		switch ($id_tabla) {
			case 'dl':
				$oAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			case 'ex':
				$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			default:
				$oAsistente=new asistentes\Asistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		}
		$oAsistente->DBCarregar();
		if ($oAsistente->DBEliminar() === false) {
			echo _('Hay un error, no se ha eliminado');
		}
		// hay que cerrar el dossier para esta persona/actividad/ubi, si no tiene más:
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
		$oDossier->cerrar();
		$oDossier->DBGuardar();

		// también borro las matriculas que pueda tener
		/*
		$oGestorMatricula=new GestorMatricula();
		foreach ($oGestorMatricula->getMatriculas(array('id_activ'=>$id_activ,'id_nom'=>$id_nom)) as $oMatricula) {
			if ($oMatricula->DBEliminar() === false) {
				echo _('Hay un error, no se ha eliminado');
			}
		}
		*/
		break;
	//------------ NUEVO --------
	//------------ EDITAR --------
	case "nuevo":
		// hay que abrir el dossier para esta persona/actividad/ubi:
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
		$oDossier->abrir();
		$oDossier->DBGuardar();
		$oPersona = new personas\PersonaPub($id_nom);
		$id_tabla_p = $oPersona->getId_tabla();
	case "editar":
		$id_tabla_p = empty($id_tabla_p)? '' : $id_tabla_p;
		echo "ttt: $id_tabla_p<br>";
		// hay que averiguar si la actividad es de la dl o de fuera.
		$oActividad  = new actividades\Actividad($id_activ);
		$id_tabla = $oActividad->getId_tabla();
		switch ($id_tabla) {
			case 'dl':
				if ($id_tabla_p == 'pa' || $id_tabla_p == 'pn') {
					$oAsistente=new asistentes\AsistenteEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				} else {
					$oAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				}
				break;
			case 'ex':
				$oAsistente=new asistentes\AsistenteOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
				break;
			default:
				$oAsistente=new asistentes\Asistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		}
		$oAsistente->DBCarregar();
		isset($_POST['encargo'])? $oAsistente->setEncargo($_POST['encargo']) : $oAsistente->setEncargo();
		isset($_POST['cama'])? $oAsistente->setCama($_POST['cama']) : $oAsistente->setCama();
		isset($_POST['observ'])? $oAsistente->setObserv($_POST['observ']) : $oAsistente->setObserv();
		isset($_POST['propio'])? $oAsistente->setPropio('t') : $oAsistente->setPropio('f');
		isset($_POST['est_ok'])? $oAsistente->setEst_ok('t') : $oAsistente->setEst_ok('f');
		isset($_POST['cfi'])? $oAsistente->setCfi('t') : $oAsistente->setCfi('f');
		isset($_POST['falta'])? $oAsistente->setFalta('t') : $oAsistente->setFalta('f');
		isset($_POST['cfi_con'])? $oAsistente->setCfi_con($_POST['cfi_con']) : $oAsistente->setCfi_con();
		if ($oAsistente->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
}

$oPosicion->setId_div('ir_a');
echo $oPosicion->atras();
?>
