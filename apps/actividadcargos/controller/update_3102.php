<?php
/**
 * Actualiza los datos de un objeto ActividadCargo.
 * Si asiste ($_POST['asis']), se crea el objeto ActividadAsistente y se pone como propio
 *
 * @package	delegacion
 * @subpackage	actividades
 * @author	Daniel Serrabou
 * @since		15/5/02.
 * @ajax		23/8/2007.
 * @version 1.0
 * @created 24/09/2010
 *
 * @param array $_POST['sel'] con id_nom#id_cargo si vengo de un select de una lista
 * @param integer $_POST['id_activ']
 * @param integer $_POST['id_cargo']
 * @param integer $_POST['id_nom']
 * @param string $_POST['observ'] optional
 * @param boolean $_POST['puede_agd'] optional
 * @param boolean $_POST['asis'] optional
 * @param integer $_POST['elim_asis'] optional Si ==2 elimino también la asistencia.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("classes/actividades/ext_a_actividades.class");
	require_once ("classes/activ-personas/d_asistentes_activ.class");
	require_once ("classes/activ-personas/d_cargos_activ.class");
	include_once('classes/web/tipo_actividad.class');

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once ("./func_dossiers.php");
		
$_POST['elim_asis'] = '';

if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=="p") {
		$id_activ=strtok($_POST['sel'][0],"#");
		$id_cargo=strtok("#");
		empty($_POST['id_pau'])? $id_nom="" : $id_nom=$_POST['id_pau'];
	}
	if ($_POST['pau']=="a") {
		$id_nom=strtok($_POST['sel'][0],"#");
		$id_cargo=strtok("#");
		$_POST['elim_asis']=strtok("#");
		empty($_POST['id_pau'])? $id_activ="" : $id_activ=$_POST['id_pau'];
	}
} else {
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
	empty($_POST['id_cargo'])? $id_cargo="" : $id_cargo=$_POST['id_cargo'];
	empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
}

switch ($_POST['mod']) {
	//------------ BORRAR --------
	case "eliminar":
		$oActividadCargo=new ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
		if (($oActividadCargo->DBEliminar()) === false) {
			$sClauError = 'Dossiers.cargos_activ.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError('', $sClauError, __LINE__, __FILE__);
			return false;
		}
	 	
		// hay que cerrar el dossier para esta persona, si no tiene más actividades:
		cerrar_dossier('p',$id_nom,'1302');
		// Borrar también la asistencia, también en el caso de actividades de s y sg
		$oActividad = new Actividad($id_activ);
		$id_tipo_activ = $oActividad->getId_tipo_activ();

		$oTipoActiv= new TiposActividades($id_tipo_activ);
		$ssfsv=$oTipoActiv->getSfsvText();
		$sasistentes=$oTipoActiv->getAsistentesText();
		$sactividad=$oTipoActiv->getActividadText();
		$snom_tipo=$oTipoActiv->getNom_tipoText();

		if ($_POST['elim_asis']==2 || $sasistentes == 's' || $sasistentes == 'sg') {
			$oActividadAsistente=new ActividadAsistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
			if ($oActividadAsistente->DBEliminar() === false) {
				echo _('Hay un error, no se ha eliminado');
			}
			cerrar_dossier('p',$id_nom,'1301'); 
		}	
	break;
	case "nuevo":
		//------------ NUEVO --------
		// Ahora machaca un cargo existente. Quiza podria avisar que ya existe
		$oActividadCargo=new ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
		$oActividadCargo->setId_nom($id_nom);
		isset($_POST['observ'])? $oActividadCargo->setObserv($_POST['observ']) : $oActividadCargo->setObserv();
		isset($_POST['puede_agd'])? $oActividadCargo->setPuede_agd('t') : $oActividadCargo->setPuede_agd('f');
		

		if (($oActividadCargo->DBGuardar()) === false) {
			$sClauError = 'Dossiers.cargos_activ.nuevo';
			$_SESSION['oGestorErrores']->addErrorAppLastError('', $sClauError, __LINE__, __FILE__);
			return false;
		}

		// si no está abierto, hay que abrir el dossier para esta persona
		abrir_dossier('p',$id_nom,'1302');
		// ... y si es la primera persona, hay que abrir el dossier para esta actividad
		abrir_dossier('a',$id_activ,'3102');
		
		// También asiste:
		if (!empty($_POST['asis'])) {
			$oActividadAsistente=new ActividadAsistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
			$oActividadAsistente->setPropio('t'); // por defecto lo pongo como propio
			$oActividadAsistente->setFalta('f');
			if ($oActividadAsistente->DBGuardar() === false) {
				echo _('Hay un error, no se ha guardado');
			}
			// si no está abierto, hay que abrir el dossier para esta persona
			abrir_dossier('p',$id_nom,'1301');
			// ... y si es la primera persona, hay que abrir el dossier para esta actividad
			abrir_dossier('a',$id_activ,'3101');
		}
		break;
	case "editar":
	//------------ EDITAR --------
		$oActividadCargo=new ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
		$oActividadCargo->setId_nom($id_nom);
		isset($_POST['observ'])? $oActividadCargo->setObserv($_POST['observ']) : $oActividadCargo->setObserv();
		isset($_POST['puede_agd'])? $oActividadCargo->setPuede_agd('t') : $oActividadCargo->setPuede_agd('f');
		if ($oActividadCargo->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		// Modifico la asistencia:
		$oActividadAsistente=new ActividadAsistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		if ($oActividadAsistente->DBCarregar('guardar') === false) { //no existe
			if (!empty($_POST['asis'])) { // lo añado
				$oActividadAsistente->setPropio('t'); // por defecto lo pongo como propio
				$oActividadAsistente->setFalta('f');
				if ($oActividadAsistente->DBGuardar() === false) {
					echo _('Hay un error, no se ha guardado');
				}
				// si no está abierto, hay que abrir el dossier para esta persona
				abrir_dossier('p',$id_nom,'1301');
				// ... y si es la primera persona, hay que abrir el dossier para esta actividad
				abrir_dossier('a',$id_activ,'3101');
			}
		} else {
			if (isset($_POST['asis']) && empty($_POST['asis'])) { // lo borro
				if ($oActividadAsistente->DBEliminar() === false) {
					echo _('Hay un error, no se ha eliminado');
				}
				// si no está abierto, hay que abrir el dossier para esta persona
				abrir_dossier('p',$id_nom,'1301');
				// ... y si es la primera persona, hay que abrir el dossier para esta actividad
				abrir_dossier('a',$id_activ,'3101');
			}
		}
		break;
}
?>
