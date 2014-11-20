<?php
use actividadestudios\model as actividadestudios;
use dossiers\model as dossiers;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=="a") { 
			$id_asignatura=strtok($_POST['sel'][0],"#"); 
			$id_activ=strtok("#");
	}
} else {
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
	empty($_POST['id_asignatura'])? $id_asignatura="" : $id_asignatura=$_POST['id_asignatura'];
}

$msg_err = '';
switch ($_POST['mod']) {
	case 'eliminar':  //------------ BORRAR --------
		if ($_POST['pau']=="a") { 
			$oActividadAsignatura = new actividadestudios\ActividadAsignatura();
			$oActividadAsignatura->setId_activ($id_activ);
			$oActividadAsignatura->setId_asignatura($id_asignatura);
			if ($oActividadAsignatura->DBEliminar() === false) {
				$msg_err = _("Hay un error, no se ha borrado.");
			}
			/*
			// hay que cerrar el dossier para esta actividad, si no tiene más personas:
			$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3005));
			$oDossier->cerrar();
			$oDossier->DBGuardar();
			//también debería borrar las matriculas
			*/
			/*
			$GesMatriculas = new GestorMatricula();
			$cMatriculas = $GesMatriculas ->getMatriculas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			foreach ($cMatriculas as $oMatricula) {
				if ($oMatricula->DBElliminar() === false) {
					$msg_err = _("Hay un error, no se ha borrado.");
				}
			}
			*/
		}
		break;
	case 'nuevo': //------------ NUEVO --------
		$oActividadAsignatura = new actividadestudios\ActividadAsignatura();
		$oActividadAsignatura->setId_activ($id_activ);
		$oActividadAsignatura->setId_asignatura($id_asignatura);
		if (!empty($_POST['interes'])) $oActividadAsignatura->setInteres($_POST['interes']); 
		if (!empty($_POST['id_profesor'])) $oActividadAsignatura->setId_profesor($_POST['id_profesor']); 
		if (!empty($_POST['avis_profesor'])) $oActividadAsignatura->setAvis_profesor($_POST['avis_profesor']); 
		if (!empty($_POST['tipo'])) $oActividadAsignatura->setTipo($_POST['tipo']);
		if (!empty($_POST['f_ini'])) $oActividadAsignatura->setF_ini($_POST['f_ini']);
		if (!empty($_POST['f_fin'])) $oActividadAsignatura->setF_fin($_POST['f_fin']);
		if ($oActividadAsignatura->DBGuardar() === false) {
			$msg_err = _("Hay un error, no se ha creado.");
		}
		// si es la primera asignatura, hay que abrir el dossier para esta actividad
		$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3005));
		$oDossier->abrir();
		$oDossier->DBGuardar();
		break;
	case 'editar': //------------ EDITAR --------
		$oActividadAsignatura = new actividadestudios\ActividadAsignatura();
		$oActividadAsignatura->setId_activ($id_activ);
		$oActividadAsignatura->setId_asignatura($id_asignatura);
		$oActividadAsignatura->DBCarregar();
		$_POST['interes'] = empty($_POST['interes'])? 'f' : 't'; 
		$oActividadAsignatura->setInteres($_POST['interes']); 
		$oActividadAsignatura->setId_profesor($_POST['id_profesor']); 
		$oActividadAsignatura->setAvis_profesor($_POST['avis_profesor']); 
		$oActividadAsignatura->setTipo($_POST['tipo']);
		$oActividadAsignatura->setF_ini($_POST['f_ini']);
		$oActividadAsignatura->setF_fin($_POST['f_fin']);
		if ($oActividadAsignatura->DBGuardar() === false) {
			$msg_err = _("Hay un error, no se ha guardado.");
		}
		break;
}

$go_to = urldecode($_POST['go_to']);
//echo "ir_a ".$go_to;
if (empty($msg_err)) { 
	$oPosicion = new web\Posicion();
	echo $oPosicion->ir_a($go_to);
} else {
	echo $msg_err;
}	
?>
