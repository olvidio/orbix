<?php
use actividades\model as actividades;
use asistentes\model as asistentes;
use personas\model as personas;

/**
 * Incorpora la primer peticion como asistencia con plaza 'pedida'.
 * No debe actualizar a las personas que ya tienen una aistencia a una actividad 
 * marcada como propia en el curso.
 */
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/* Validate inputs $b = (string)filter_input(INPUT_GET, 'b'); */
	
$tipo_activ = (string)  filter_input(INPUT_POST, 'tipo_activ');
$na = (string)  filter_input(INPUT_POST, 'na');

/* Pongo en la variable $curso el periodo del curso */
$mes=date('m');
$any=date('Y');
if ($mes>9) { $any=$any+1; } 
$inicurs_ca=core\curso_est("inicio",$any);
$fincurs_ca=core\curso_est("fin",$any);

//Actividades a las que afecta
$cActividades = array();
$sfsv = core\ConfigGlobal::mi_sfsv();
$aWhereA['status'] = 2;
$aWhereA['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
$aOperadorA['f_ini'] = 'BETWEEN';
switch ($na) {
	case "agd":
	case "a":
		//caso de agd
		$id_tabla_persona='a'; //el id_tabla entra en conflicto con el de actividad
		$tabla_pau='p_agregados';
		switch ($tipo_activ) {
			case 'ca': //133
				$id_tipo_activ = '^'.$sfsv.'33';
				break;
			case 'crt':
				$id_tipo_activ = '^'.$sfsv.'31';
				break;
		}
		$aWhereA['id_tipo_activ'] = $id_tipo_activ;
		$aOperadorA['id_tipo_activ'] = '~';
		$GesActividades = new actividades\GestorActividadPub();
		$cActividades = $GesActividades->getActividades($aWhereA,$aOperadorA);
		break;
	case "n":
		// caso de n
		$id_tabla_persona='n';
		$tabla_pau='p_numerarios';
		switch ($tipo_activ) {
			case 'ca': //112
				$id_tipo_activ = '^'.$sfsv.'12';
				break;
			case 'crt':
				$id_tipo_activ = '^'.$sfsv.'11';
				break;
		}
		$aWhereA['id_tipo_activ'] = $id_tipo_activ;
		$aOperadorA['id_tipo_activ'] = '~';
		$GesActividades = new actividades\GestorActividadPub();
		$cActividades = $GesActividades->getActividades($aWhereA,$aOperadorA);
	break;
}
$a_id_activ = array();
foreach ($cActividades as $oActividad) {
	$a_id_activ[] = $oActividad->getId_activ();
}

//Miro las peticiones actuales
$gesPlazasPeticion = new \actividadplazas\model\GestorPlazaPeticion();
$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('tipo'=>$tipo_activ,'_ordre'=>'orden'));
$id_nom_old = 0;
foreach ($cPlazasPeticion as $oPlazaPeticion) {
	// solo apunto la primera (segun orden)
	$id_nom = $oPlazaPeticion->getId_nom();
	$id_activ_new = $oPlazaPeticion->getId_activ();
	if ($id_nom_old == $id_nom) { continue; }
	$id_nom_old = $id_nom;

	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\",'',$obj_persona);
	//Con el tiempo habrá menos actividades (curso) que asistencias (todas). Miro por actividades:
	$ya_tiene = 0;
	foreach ($cActividades as $oActividad) {
		// hay que averiguar si la actividad es de la dl o de fuera.
		$id_activ = $oActividad->getId_activ();
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
					$oAsistenteNew = new asistentes\AsistenteDl();
					$GesAsistentes = new asistentes\GestorAsistenteDl();
					$cAsistentes = $GesAsistentes->getAsistentesDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
					break;
				case 'PersonaIn':
				case 'PersonaEx':
					$oAsistenteNew = new asistentes\AsistenteEx();
					$GesAsistentes = new asistentes\GestorAsistenteEx();
					$cAsistentes = $GesAsistentes->getAsistentesEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
					break;
			}
		} else {
			if ($id_tabla == 'dl') {
				$oAsistenteNew = new asistentes\AsistenteOut();
				$GesAsistentes = new asistentes\GestorAsistenteOut();
				$cAsistentes = $GesAsistentes->getAsistentesOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
			} else {
				$oAsistenteNew = new asistentes\AsistenteEx();
				$GesAsistentes = new asistentes\GestorAsistenteEx();
				$cAsistentes = $GesAsistentes->getAsistentesEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
			}
		}
		//Comprobar que no tiene alguno asignado como propio
		if (is_array($cAsistentes) && count($cAsistentes) > 0) {
			$ya_tiene++;
			break;
		}
	}
	if ($ya_tiene == 0) {
		//asignar uno nuevo.
		$oAsistenteNew->setId_activ($id_activ_new);
		$oAsistenteNew->setId_nom($id_nom);
		$oAsistenteNew->DBCarregar();
		$oAsistenteNew->setPropio('t');
		$oAsistenteNew->setPlaza(1);
		if ($oAsistenteNew->DBGuardar() === false) {
			$msg_err = _('Hay un error, no se ha guardado');
			echo $msg_err;
		}
	}
}
