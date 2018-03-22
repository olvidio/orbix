<?php
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use asistentes\model as asistentes;
use dossiers\model as dossiers;
use personas\model as personas;

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
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
//$_POST['elim_asis'] = '';
$_POST['elim_asis'] = empty($_POST['elim_asis'])? '' : $_POST['elim_asis'];

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
		$oActividadCargo=new actividadcargos\ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
		if (($oActividadCargo->DBEliminar()) === false) {
			$sClauError = 'Dossiers.cargos_activ.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError('', $sClauError, __LINE__, __FILE__);
			return false;
		}
	 	
		// hay que cerrar el dossier para esta persona, si no tiene más actividades:
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1302));
		$oDossier->cerrar();
		$oDossier->DBGuardar();

		// Borrar también la asistencia, también en el caso de actividades de s y sg
		$oActividad = new actividades\Actividad($id_activ);
		$id_tipo_activ = $oActividad->getId_tipo_activ();

		$oTipoActiv= new web\TiposActividades($id_tipo_activ);
		$ssfsv=$oTipoActiv->getSfsvText();
		$sasistentes=$oTipoActiv->getAsistentesText();
		$sactividad=$oTipoActiv->getActividadText();
		$snom_tipo=$oTipoActiv->getNom_tipoText();

		if ($_POST['elim_asis'] == 2 || $sasistentes == 's' || $sasistentes == 'sg') {
			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$msg_err = "<br>$oPersona con id_nom: $id_pau";
				exit ($msg_err);
			}
			$id_tabla_p = $oPersona->getId_Tabla();
			$id_schema = $oPersona->getId_schema();
			switch ($id_tabla_p) {
				case 'n':
				case 'nax':
				case 'a':
				case 's':
				case 'sssc':
					$id_tabla = 'dl';
					break;
				case 'pn':
				case 'pa':
					if ($id_schema == -1001 || $id_schema == -2001) {
						$id_tabla = 'ex';
					} else {
						$id_tabla = 'out';
					}
					break;
			}
			$oActividadAsistente=new asistentes\Asistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
			$oActividadAsistente->setId_tabla($id_tabla);
			if ($oActividadAsistente->DBEliminar() === false) {
				$msg_err = _('Hay un error, no se ha eliminado');
			}
			$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
			$oDossier->cerrar();
			$oDossier->DBGuardar();
		}	
	break;
	case "nuevo":
		//------------ NUEVO --------
		// Ahora machaca un cargo existente. Quiza podria avisar que ya existe
		$oActividadCargo=new actividadcargos\ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
		$oActividadCargo->setId_nom($id_nom);
		isset($_POST['observ'])? $oActividadCargo->setObserv($_POST['observ']) : $oActividadCargo->setObserv();
		isset($_POST['puede_agd'])? $oActividadCargo->setPuede_agd('t') : $oActividadCargo->setPuede_agd('f');
		
		if (($oActividadCargo->DBGuardar()) === false) {
			$sClauError = 'Dossiers.cargos_activ.nuevo';
			//$_SESSION['oGestorErrores']->addErrorAppLastError('', $sClauError, __LINE__, __FILE__);
			$msg_err = " $sClauError, ". __LINE__ .','. __FILE__ ;
			return false;
		}

		// si no está abierto, hay que abrir el dossier para esta persona
		$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1302));
		$oDossier->abrir();
		$oDossier->DBGuardar();
		// ... y si es la primera persona, hay que abrir el dossier para esta actividad
		$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3102));
		$oDossier->abrir();
		$oDossier->DBGuardar();
		
		// También asiste:
		if (!empty($_POST['asis'])) {
			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$msg_err = "<br>$oPersona con id_nom: $id_pau";
				exit ($msg_err);
			}
			$id_tabla_p = $oPersona->getId_Tabla();
			$id_schema = $oPersona->getId_schema();
			switch ($id_tabla_p) {
				case 'n':
				case 'nax':
				case 'a':
				case 's':
				case 'sssc':
					$id_tabla = 'dl';
					break;
				case 'pn':
				case 'pa':
					if ($id_schema == -1001 || $id_schema == -2001) {
						$id_tabla = 'ex';
					} else {
						$id_tabla = 'out';
					}
					break;
			}
			$oActividadAsistente=new asistentes\Asistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
			$oActividadAsistente->setId_tabla($id_tabla);
			$oActividadAsistente->setPropio('t'); // por defecto lo pongo como propio
			$oActividadAsistente->setFalta('f');
			if ($oActividadAsistente->DBGuardar() === false) {
				$msg_err = _('Hay un error, no se ha guardado');
			}
			// si no está abierto, hay que abrir el dossier para esta persona
			$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
			$oDossier->abrir();
			$oDossier->DBGuardar();
			// ... y si es la primera persona, hay que abrir el dossier para esta actividad
			$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3101));
			$oDossier->abrir();
			$oDossier->DBGuardar();
		}
		break;
	case "editar":
	//------------ EDITAR --------
		$oActividadCargo=new actividadcargos\ActividadCargo(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
		$oActividadCargo->setId_nom($id_nom);
		isset($_POST['observ'])? $oActividadCargo->setObserv($_POST['observ']) : $oActividadCargo->setObserv();
		isset($_POST['puede_agd'])? $oActividadCargo->setPuede_agd('t') : $oActividadCargo->setPuede_agd('f');
		if ($oActividadCargo->DBGuardar() === false) {
			$msg_err = _('Hay un error, no se ha guardado');
		}
		// Modifico la asistencia:
		$oActividadAsistente=new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		if ($oActividadAsistente->DBCarregar('guardar') === false) { //no existe
			if (!empty($_POST['asis'])) { // lo añado
				$oActividadAsistente->setPropio('t'); // por defecto lo pongo como propio
				$oActividadAsistente->setFalta('f');
				if ($oActividadAsistente->DBGuardar() === false) {
					$msg_err = _('Hay un error, no se ha guardado');
				}
				// si no está abierto, hay que abrir el dossier para esta persona
				$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
				$oDossier->abrir();
				$oDossier->DBGuardar();
				// ... y si es la primera persona, hay que abrir el dossier para esta actividad
				$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3101));
				$oDossier->abrir();
				$oDossier->DBGuardar();
			}
		} else {
			if (isset($_POST['asis']) && empty($_POST['asis'])) { // lo borro
				if ($oActividadAsistente->DBEliminar() === false) {
					$msg_err = _('Hay un error, no se ha eliminado');
				}
				// si no está abierto, hay que abrir el dossier para esta persona
				$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1301));
				$oDossier->abrir();
				$oDossier->DBGuardar();
				// ... y si es la primera persona, hay que abrir el dossier para esta actividad
				$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3101));
				$oDossier->abrir();
				$oDossier->DBGuardar();
			}
		}
		break;
}

if (empty($msg_err)) { 
	if (!empty($_POST['go_to'])) {
		echo $oPosicion->ir_a($_POST['go_to']);
	} else {
		$oPosicion->setId_div('ir_a');
		echo $oPosicion->mostrar_left_slide();
	}
} else {
	echo $msg_err;
}	

?>
