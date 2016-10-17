<?php
use asistentes\model as asistentes;
use actividadestudios\model as actividadestudios;
use dossiers\model as dossiers;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	if ($_POST['pau']=='p') { 
		$id_activ = strtok($_POST['sel'][0],'#'); 
		$id_activ = !empty($_POST['id_activ'])? $_POST['id_activ'] : $id_activ;
		$id_asignatura = strtok('#'); 
		$id_nom = strtok('#'); 
		$id_nom = !empty($_POST['id_pau'])? $_POST['id_pau'] : $id_nom;
	}
	if ($_POST['pau']=='a') {
		$id_nom=strtok($_POST['sel'][0],'#'); 
		$id_asignatura=strtok('#'); 
		$id_activ=empty($_POST['id_pau'])? '' : $_POST['id_pau'];
	}
} else {
	$id_activ=empty($_POST['id_activ'])? '' : $_POST['id_activ'];
	$id_nom=empty($_POST['id_pau'])? '' : $_POST['id_pau'];
	$id_asignatura=empty($_POST['id_asignatura'])? '' : $_POST['id_asignatura'];
}

switch ($_POST['mod']) {
	case 'plan':  //------------ confirmar estudios --------
		$est_ok = (isset($_POST['est_ok']) && $_POST['est_ok']=='t') ? 't': 'f';
		$oAsistente = new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		$oAsistente->DBCarregar();
		$oAsistente->setEst_ok($est_ok);
		$oAsistente->DBGuardar();
		break;
	case 'eliminar': //------------ BORRAR --------
		if ($_POST['pau']=="p") { 
			// Para borrar varios
			foreach ($_POST['sel'] as $sel) {
				$id_activ = strtok($sel,'#'); 
				$id_asignatura = strtok('#'); 
				if (!empty($_POST['id_activ'])) {
					$id_activ = $_POST['id_activ'];
				}
				if (!empty($_POST['id_pau'])) {
					$id_nom = $_POST['id_pau'];
				} else {
					$id_nom = strtok('#'); 
				}
			
				$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'id_asignatura'=>$id_asignatura));
				if ($oMatricula->DBEliminar() === false) {
					$msg_err = _("Hay un error, no se ha borrado.");
				}
				// hay que cerrar el dossier para esta persona, si no tiene más actividades:
				$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1303));
				$oDossier->abrir();
				$oDossier->DBGuardar();
			}
		}
		if ($_POST['pau']=="a") { 
			$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'id_asignatura'=>$id_asignatura));
			if ($oMatricula->DBEliminar() === false) {
				$msg_err = _("Hay un error, no se ha borrado.");
			}
			// hay que cerrar el dossier para esta actividad, si no tiene más personas:
			$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3103));
			$oDossier->abrir();
			$oDossier->DBGuardar();
		}
		break;
	case 'nuevo': //------------ NUEVO --------
		$_POST['id_nivel'] = empty($_POST['id_nivel'])? '' : $_POST['id_nivel'];
		$_POST['id_situacion'] = empty($_POST['id_situacion'])? '' : $_POST['id_situacion'];
		$_POST['preceptor'] = (isset($_POST['preceptor']) && $_POST['preceptor']==true)? 't' : 'f';

		$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'id_asignatura'=>$id_asignatura));
		$oMatricula->setId_nivel($_POST['id_nivel']);
		$oMatricula->setId_situacion($_POST['id_situacion']);
		$oMatricula->setPreceptor($_POST['preceptor']);
		if ($oMatricula->DBGuardar() === false) {
			$msg_err = _("Hay un error, no se ha guardado.");
		} else {
			// si no está abierto, hay que abrir el dossier para esta persona
			$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$id_nom,'id_tipo_dossier'=>1303));
			$oDossier->abrir();
			$oDossier->DBGuardar();
			// ... y si es la primera persona, hay que abrir el dossier para esta actividad
			$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$id_activ,'id_tipo_dossier'=>3103));
			$oDossier->abrir();
			$oDossier->DBGuardar();

			// hay que añadir esta asignatura a las asignaturas que se dan en el ca
			// compruebo que no existe:
			$oGesActividadAsignatura = new actividadestudios\GestorActividadAsignatura();
			$cActividadAsignaturas = $oGesActividadAsignatura->getActividadAsignaturas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			if (count($cActividadAsignaturas) > 0) {
				$tipo = ($_POST['preceptor']=='t')? 'p' : '';
				$oActividadAsignatura = new actividadestudios\ActividadAsignatura();
				$oActividadAsignatura->setId_activ($id_activ);
				$oActividadAsignatura->setId_asignatura($id_asignatura);
				$oActividadAsignatura->setTipo($tipo);
				$oActividadAsignatura->DBGuardar();
			}
		}
		break;
	case 'editar':  //------------ EDITAR --------
		//$aPrimaryKey = unserialize($_POST['primary_key_s']);
		$_POST['id_nivel'] = empty($_POST['id_nivel'])? '' : $_POST['id_nivel'];
		$_POST['id_situacion'] = empty($_POST['id_situacion'])? '' : $_POST['id_situacion'];
		$_POST['preceptor'] = (isset($_POST['preceptor']) && $_POST['preceptor']==true)? 't' : 'f';

		$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'id_asignatura'=>$id_asignatura));
		//$oMatricula = new actividadestudios\MatriculaDl($aPrimaryKey);
		$oMatricula->setId_asignatura($id_asignatura);
		$oMatricula->setId_nivel($_POST['id_nivel']);
		$oMatricula->setId_situacion($_POST['id_situacion']);
		$oMatricula->setPreceptor($_POST['preceptor']);
		if ($oMatricula->DBGuardar() === false) {
			$msg_err = _("Hay un error, no se ha guardado.");
		}
}

$go_to = urldecode($_POST['go_to']);
if (empty($msg_err)) { 
	$oPosicion = new web\Posicion();
	echo $oPosicion->ir_a($go_to);
} else {
	echo $msg_err;
}	
?>
