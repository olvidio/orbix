<?php
/**
 * Incorpora la primera petición como asistencia con plaza:
 *  'asignada' en el caso de actividades de la dl.
 *  'pedida' en actividades de otras dl.
 * No debe actualizar a las personas que ya tienen una aistencia a una actividad 
 * marcada como propia en el curso.
 * 
 * @param string $sactividad
 * @param string $sasistentes
 */

use actividades\model\entity as actividades;
use asistentes\model\entity as asistentes;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsactividad = (string)  filter_input(INPUT_POST, 'sactividad');
$Qsasistentes = (string)  filter_input(INPUT_POST, 'sasistentes');

$mi_sfsv = core\ConfigGlobal::mi_sfsv();
if ($mi_sfsv == 1) $ssfsv = 'sv';
if ($mi_sfsv == 2) $ssfsv = 'sf';
$snom_tipo = '...';
$oTipoActiv= new web\TiposActividades();
$oTipoActiv->setSfsvText($ssfsv);
$oTipoActiv->setAsistentesText($Qsasistentes);
$oTipoActiv->setActividadText($Qsactividad);
$Qid_tipo_activ=$oTipoActiv->getId_tipo_activ();
$Qid_tipo_activ =  '^'.$Qid_tipo_activ;

/* Pongo en la variable $curso el periodo del curso */
switch ($Qsactividad) {
	case 'ca':
	case 'cv':
		$any=  core\ConfigGlobal::any_final_curs('est');
		$inicurs=core\curso_est("inicio",$any,"est")->format('Y-m-d');
		$fincurs=core\curso_est("fin",$any,"est")->format('Y-m-d');
		break;
	case 'crt':
		$any=  core\ConfigGlobal::any_final_curs('crt');
		$inicurs=core\curso_est("inicio",$any,"crt")->format('Y-m-d');
		$fincurs=core\curso_est("fin",$any,"crt")->format('Y-m-d');
		break;
}

$mi_dele = core\ConfigGlobal::mi_dele();
//Actividades a las que afecta
$cActividades = array();
$aWhereA['status'] = actividades\ActividadAll::STATUS_ACTUAL;
$aWhereA['f_ini'] = "'$inicurs','$fincurs'";
$aOperadorA['f_ini'] = 'BETWEEN';
switch ($Qsasistentes) {
	case "agd":
	case "a":
		//caso de agd
		$id_tabla_persona='a'; //el id_tabla entra en conflicto con el de actividad
		$aWhereA['id_tipo_activ'] = $Qid_tipo_activ;
		$aOperadorA['id_tipo_activ'] = '~';

		//inicialmente estaba sólo con las activiades publicadas. 
		//Ahora añado las no publicadas de midl.
		$GesActividadesDl = new actividades\GestorActividadDl();
		$cActividadesDl = $GesActividadesDl->getActividades($aWhereA,$aOperadorA);
		// Añado la condición para que no duplique las de midele:
		$aWhereA['dl_org'] = $mi_dele;
		$aOperadorA['dl_org'] = '!=';
		$GesActividadesPub = new actividades\GestorActividadPub();
		$cActividadesPub = $GesActividadesPub->getActividades($aWhereA,$aOperadorA);
		
		$cActividades = array_merge($cActividadesDl,$cActividadesPub);
		break;
	case "n":
		// caso de n
		$id_tabla_persona='n';
		$aWhereA['id_tipo_activ'] = $Qid_tipo_activ;
		$aOperadorA['id_tipo_activ'] = '~';
		//inicialmente estaba sólo con las activiades publicadas. 
		//Ahora añado las no publicadas de midl.
		$GesActividadesDl = new actividades\GestorActividadDl();
		$cActividadesDl = $GesActividadesDl->getActividades($aWhereA,$aOperadorA);
		// Añado la condición para que no duplique las de midele:
		$aWhereA['dl_org'] = $mi_dele;
		$aOperadorA['dl_org'] = '!=';
		$GesActividadesPub = new actividades\GestorActividadPub();
		$cActividadesPub = $GesActividadesPub->getActividades($aWhereA,$aOperadorA);
		
		$cActividades = array_merge($cActividadesDl,$cActividadesPub);
	break;
}
$a_id_activ = array();
foreach ($cActividades as $oActividad) {
	$a_id_activ[] = $oActividad->getId_activ();
}

//Miro las peticiones actuales
$gesPlazasPeticion = new \actividadplazas\model\entity\GestorPlazaPeticion();
$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('tipo'=>$Qsactividad,'_ordre'=>'id_nom,orden'));
$id_nom_old = 0;
$msg_err = '';
foreach ($cPlazasPeticion as $oPlazaPeticion) {
	// solo apunto la primera (segun orden)
	$id_nom = $oPlazaPeticion->getId_nom();
	$id_activ_new = $oPlazaPeticion->getId_activ();
	if ($id_nom_old == $id_nom) { continue; }
	$id_nom_old = $id_nom;

	// hay que averiguar si la persona es de la dl o de fuera.
	$oPersona = personas\Persona::NewPersona($id_nom);
	// Debo saltarme las asistencias de personas que no son del grupo (n, agd)
	$id_tabla_p = $oPersona->getId_tabla();
	if ($id_tabla_p != $id_tabla_persona) { continue; }
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
		exit($msg_err);
	}
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\entity\\",'',$obj_persona);

	//Comprobar que no tienen alguna actividad ya asignada como propia
	//Con el tiempo habrá menos actividades (curso) que asistencias (todas). Miro por actividades:
	$ya_tiene = 0;
	foreach ($cActividades as $oActividad) {
		// hay que averiguar si la actividad es de la dl o de fuera.
		$id_activ = $oActividad->getId_activ();
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
		$id_tabla = $oActividad->getId_tabla();
		if ($dl == $mi_dele) {
			Switch($obj_persona) {
				case 'PersonaN':
				case 'PersonaNax':
				case 'PersonaAgd':
				case 'PersonaS':
				case 'PersonaSSSC':
				case 'PersonaDl':
					$GesAsistentes = new asistentes\GestorAsistenteDl();
					$cAsistentes = $GesAsistentes->getAsistentesDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
					break;
				case 'PersonaIn':
				case 'PersonaEx':
					$GesAsistentes = new asistentes\GestorAsistenteEx();
					$cAsistentes = $GesAsistentes->getAsistentesEx(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
					break;
			}
		} else {
			if ($id_tabla == 'dl') {
				$GesAsistentes = new asistentes\GestorAsistenteOut();
				$cAsistentes = $GesAsistentes->getAsistentesOut(array('id_activ'=>$id_activ,'id_nom'=>$id_nom,'propio'=>'t'));
			} else {
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
		$oActividad = new actividades\ActividadAll(array('id_activ'=>$id_activ_new));
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
		$id_tabla = $oActividad->getId_tabla();
		if ($dl == $mi_dele) {
			Switch($obj_persona) {
				case 'PersonaN':
				case 'PersonaNax':
				case 'PersonaAgd':
				case 'PersonaS':
				case 'PersonaSSSC':
				case 'PersonaDl':
					$oAsistenteNew = new asistentes\AsistenteDl();
					break;
				case 'PersonaIn':
				case 'PersonaEx':
					$oAsistenteNew = new asistentes\AsistenteEx();
					break;
			}
		} else {
			if ($id_tabla == 'dl') {
				$oAsistenteNew = new asistentes\AsistenteOut();
			} else {
				$oAsistenteNew = new asistentes\AsistenteEx();
			}
		}
		//asignar uno nuevo.
		$oAsistenteNew->setId_activ($id_activ_new);
		$oAsistenteNew->setId_nom($id_nom);
		$oAsistenteNew->DBCarregar();
		$oAsistenteNew->setPropio('t');
		//1:pedida, 2:en espera, 3: denegada, 4:asignada, 5:confirmada
		$oAsistenteNew->setPlaza(asistentes\Asistente::PLAZA_ASIGNADA);
		// IMPORTANT: Propietario del a plaza
		$oAsistenteNew->setPropietario("$dl>$mi_dele");
		if ($oAsistenteNew->DBGuardar() === false) {
			$msg_err = _("hay un error, no se ha guardado");
			echo $msg_err;
		}
	}
}

$txt = sprintf(_("no se incorporán las peticiones si la persona ya tiene una actividad como propia en el periodo: %s - %s."),$inicurs,$fincurs);
if (!empty($msg_err)) { echo $msg_err; }
?>
<script>
	fnjs_left_side_hide(); 
</script>
<?= $txt; ?>