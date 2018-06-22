<?php
use actividades\model\entity as actividades;
use asistentes\model\entity as asistentes;
use actividadestudios\model\entity as actividadestudios;
use dossiers\model\entity as dossiers;
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
$Qest_ok = (string) \filter_input(INPUT_POST,'est_ok');
$Qobserv = (string) \filter_input(INPUT_POST,'observ');

//En el caso de eliminar desde la lista de cargos
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	if ($Qpau=="p") {
		$Qid_activ=strtok($a_sel[0],"#");
		$Qid_asignatura=strtok("#");
		$Qid_nom = (integer) \filter_input(INPUT_POST,'id_pau');
	}
	if ($Qpau=="a") {
		$Qid_nom=strtok($a_sel[0],"#");
		$Qid_asignatura=strtok("#");
		$Qid_activ = (integer) \filter_input(INPUT_POST,'id_pau');
	}
} else { // desde el formulario
	$Qid_activ = (integer) \filter_input(INPUT_POST,'id_activ');
	$Qid_nom = (integer) \filter_input(INPUT_POST,'id_nom');
	$Qid_nom = (integer) \filter_input(INPUT_POST,'id_pau');
	$Qid_asignatura = (integer) \filter_input(INPUT_POST,'id_asignatura');
	$Qid_nivel = (integer) \filter_input(INPUT_POST,'id_nivel');
	$Qid_situacion = (integer) \filter_input(INPUT_POST,'id_situacion');
	$Qpreceptor = (string) \filter_input(INPUT_POST,'preceptor');
	$Qid_preceptor = (integer) \filter_input(INPUT_POST,'id_preceptor');
}

switch ($Qmod) {
	case 'observ':  //------------ observaciones --------
		// hay que averiguar si la persona es de la dl o de fuera.
		$oPersona = personas\Persona::NewPersona($Qid_nom);
		if (!is_object($oPersona)) {
			$msg_err = "<br>$oPersona con id_nom: $Qid_nom en  ".__FILE__.": line ". __LINE__;
			exit($msg_err);
		}
		$obj_persona = get_class($oPersona);
		$obj_persona = str_replace("personas\\model\\entity\\",'',$obj_persona);
		// hay que averiguar si la actividad es de la dl o de fuera.
		$oActividad  = new actividades\Actividad($Qid_activ);
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
		$id_tabla = $oActividad->getId_tabla();
		$oAsistente = asistentes\Asistente::getClaseAsistente($obj_persona,$dl,$id_tabla);
		$oAsistente->setPrimary_key(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom));
		$oAsistente->DBCarregar();
		$oAsistente->setObserv($Qobserv);
		$oAsistente->DBGuardar();
		break;
	case 'plan':  //------------ confirmar estudios --------
		$est_ok = (isset($Qest_ok) && $Qest_ok=='t') ? 't': 'f';
		// hay que averiguar si la persona es de la dl o de fuera.
		$oPersona = personas\Persona::NewPersona($Qid_nom);
		if (!is_object($oPersona)) {
			$msg_err = "<br>$oPersona con id_nom: $Qid_nom en  ".__FILE__.": line ". __LINE__;
			exit($msg_err);
		}
		$obj_persona = get_class($oPersona);
		$obj_persona = str_replace("personas\\model\\entity\\",'',$obj_persona);
		// hay que averiguar si la actividad es de la dl o de fuera.
		$oActividad  = new actividades\Actividad($Qid_activ);
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
		$id_tabla = $oActividad->getId_tabla();
		$oAsistente = asistentes\Asistente::getClaseAsistente($obj_persona,$dl,$id_tabla);
		$oAsistente->setPrimary_key(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom));
		$oAsistente->DBCarregar();
		$oAsistente->setEst_ok($est_ok);
		$oAsistente->DBGuardar();
		break;
	case 'eliminar': //------------ BORRAR --------
		if ($Qpau=="p") { 
			// Para borrar varios
			foreach ($a_sel as $sel) {
				$id_activ = strtok($sel,'#'); 
				$id_asignatura = strtok('#'); 
				if (!empty($Qid_activ)) {
					$id_activ = $Qid_activ;
				}
				if (!empty($Qid_nom)) {
					$id_nom = $Qid_nom;
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
				// Si la puse yo, hay que eliminar esta asignatura a las asignaturas que se dan en el ca
				// si no hay nadie más matriculado:
				$oGesActividadAsignatura = new actividadestudios\GestorActividadAsignaturaDl();
				$cActividadAsignaturas = $oGesActividadAsignatura->getActividadAsignaturas(array('id_activ'=>$Qid_activ,'id_asignatura'=>$Qid_asignatura));
				if (count($cActividadAsignaturas) == 1) {
					$gesMatriculas = new actividadestudios\GestorMatricula();
					$cMatriculas = $gesMatriculas->getMatriculas(['id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura]);
					if (count($cMatriculas) == 0) {
						$oActividadAsignatura = $cActividadAsignaturas[0];
						$oActividadAsignatura->DBEliminar();
					}
				}
			}
		}
		if ($Qpau=="a") { 
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
		// Si no es opcional, calculo el id_asignatura a partir del id_nivel
		if ($Qid_asignatura=='1') {
			$oGesAsignaturas=new asignaturas\model\entity\GestorAsignatura();
			$cAsignaturas=$oGesAsignaturas->getAsignaturas(array('id_nivel'=>$Qid_nivel));
			$oAsignatura = $cAsignaturas[0]; // sólo deberia haber una
			$Qid_asignatura=$oAsignatura->getId_asignatura();
		}
		
		$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom,'id_asignatura'=>$Qid_asignatura));
		$oMatricula->setId_nivel($Qid_nivel);
		$oMatricula->setId_situacion($Qid_situacion);
		empty($Qpreceptor)? $oMatricula->setPreceptor('f') : $oMatricula->setPreceptor('t');
		$oMatricula->setId_preceptor($Qid_preceptor);
		if ($oMatricula->DBGuardar() === false) {
			$msg_err = _("Hay un error, no se ha guardado.");
		} else {
			// si no está abierto, hay que abrir el dossier para esta persona
			$oDossier = new dossiers\Dossier(array('tabla'=>'p','id_pau'=>$Qid_nom,'id_tipo_dossier'=>1303));
			$oDossier->abrir();
			$oDossier->DBGuardar();
			// ... y si es la primera persona, hay que abrir el dossier para esta actividad
			$oDossier = new dossiers\Dossier(array('tabla'=>'a','id_pau'=>$Qid_activ,'id_tipo_dossier'=>3103));
			$oDossier->abrir();
			$oDossier->DBGuardar();

			// hay que añadir esta asignatura a las asignaturas que se dan en el ca
			// compruebo que no existe:
			$oGesActividadAsignatura = new actividadestudios\GestorActividadAsignatura();
			$cActividadAsignaturas = $oGesActividadAsignatura->getActividadAsignaturas(array('id_activ'=>$Qid_activ,'id_asignatura'=>$Qid_asignatura));
			if (count($cActividadAsignaturas) == 0) {
				$oActividadAsignatura = new actividadestudios\ActividadAsignaturaDl();
				$oActividadAsignatura->setId_activ($Qid_activ);
				$oActividadAsignatura->setId_asignatura($Qid_asignatura);
				if ($Qpreceptor==true) {
					$oActividadAsignatura->setId_profesor($Qid_preceptor);
					$tipo = 'p';
				} else {
					$tipo = '';
				}
				$oActividadAsignatura->setTipo($tipo);
				$oActividadAsignatura->DBGuardar();
			}
		}
		break;
	case 'editar':  //------------ EDITAR --------
		$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom,'id_asignatura'=>$Qid_asignatura));
		isset($Qid_asignatura)? $oMatricula->setId_asignatura($Qid_asignatura) : $oMatricula->setId_asignatura();
		isset($Qid_nivel)? $oMatricula->setId_nivel($Qid_nivel) : $oMatricula->setId_nivel();
		isset($Qid_situacion)? $oMatricula->setId_situacion($Qid_situacion) : $oMatricula->setId_situacion();
		empty($Qpreceptor)? $oMatricula->setPreceptor('f') : $oMatricula->setPreceptor('t');
		isset($Qid_preceptor)? $oMatricula->setId_preceptor($Qid_preceptor) : $oMatricula->setId_preceptor();
		
		if ($oMatricula->DBGuardar() === false) {
			$msg_err = _("Hay un error, no se ha guardado.");
		}
}

if (!empty($msg_err)) { 
	echo $msg_err;
}