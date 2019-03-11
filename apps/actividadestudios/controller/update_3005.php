<?php
use actividadestudios\model\entity as actividadestudios;
use dossiers\model\entity as dossiers;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
$Qmod = (string) \filter_input(INPUT_POST,'mod');
$Qpau = (string) \filter_input(INPUT_POST,'pau');

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	if ($Qpau=="a") { 
	    $Qid_activ = (integer) strtok($a_sel[0],"#");
	    $Qid_asignatura= (integer) strtok("#");
	}
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$Qid_activ = (integer) \filter_input(INPUT_POST,'id_activ');
	$Qid_asignatura = (integer) \filter_input(INPUT_POST,'id_asignatura');
}

$msg_err = '';
switch ($Qmod) {
	case 'eliminar':  //------------ BORRAR --------
		if ($Qpau=="a") { 
			$oActividadAsignatura = new actividadestudios\ActividadAsignaturaDl();
			$oActividadAsignatura->setId_activ($Qid_activ);
			$oActividadAsignatura->setId_asignatura($Qid_asignatura);
			if ($oActividadAsignatura->DBEliminar() === false) {
				$msg_err = _("hay un error, no se ha borrado");
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
					$msg_err = _("hay un error, no se ha borrado");
				}
			}
			*/
		}
		break;
	case 'nuevo': //------------ NUEVO --------
		$oActividadAsignatura = new actividadestudios\ActividadAsignaturaDl();
		$oActividadAsignatura->setId_activ($Qid_activ);
		$oActividadAsignatura->setId_asignatura($Qid_asignatura);
		
		$Qid_profesor = (integer) \filter_input(INPUT_POST,'id_profesor');
		$Qavis_profesor = (string) \filter_input(INPUT_POST,'avis_profesor');
		$Qtipo = (string) \filter_input(INPUT_POST,'tipo');
		$Qf_ini = (string) \filter_input(INPUT_POST,'f_ini');
		$Qf_fin = (string) \filter_input(INPUT_POST,'f_fin');
		
		$oActividadAsignatura->setId_profesor($Qid_profesor); 
		$oActividadAsignatura->setAvis_profesor($Qavis_profesor); 
		$oActividadAsignatura->setTipo($Qtipo);
		$oActividadAsignatura->setF_ini($Qf_ini);
		$oActividadAsignatura->setF_fin($Qf_fin);
		if ($oActividadAsignatura->DBGuardar() === false) {
			$msg_err = _("hay un error, no se ha creado");
		}
		// si es la primera asignatura, hay que abrir el dossier para esta actividad
		$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$Qid_activ,'id_tipo_dossier'=>3005));
		$oDossier->abrir();
		$oDossier->DBGuardar();
		break;
	case 'editar': //------------ EDITAR --------
		$Qid_profesor = (integer) \filter_input(INPUT_POST,'id_profesor');
		$Qavis_profesor = (string) \filter_input(INPUT_POST,'avis_profesor');
		$Qtipo = (string) \filter_input(INPUT_POST,'tipo');
		$Qf_ini = (string) \filter_input(INPUT_POST,'f_ini');
		$Qf_fin = (string) \filter_input(INPUT_POST,'f_fin');

		$oActividadAsignatura = new actividadestudios\ActividadAsignaturaDl();
		$oActividadAsignatura->setId_activ($Qid_activ);
		$oActividadAsignatura->setId_asignatura($Qid_asignatura);
		$oActividadAsignatura->DBCarregar();
		$oActividadAsignatura->setId_profesor($Qid_profesor); 
		$oActividadAsignatura->setAvis_profesor($Qavis_profesor); 
		$oActividadAsignatura->setTipo($Qtipo);
		$oActividadAsignatura->setF_ini($Qf_ini);
		$oActividadAsignatura->setF_fin($Qf_fin);
		if ($oActividadAsignatura->DBGuardar() === false) {
			$msg_err = _("hay un error, no se ha guardado");
		}
		break;
}

if (empty($msg_err)) { 
	echo $msg_err;
}	
