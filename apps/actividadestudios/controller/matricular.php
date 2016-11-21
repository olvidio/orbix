<?php
use actividadestudios\model as actividadestudios;
use asistentes\model as asistentes;
use notas\model as notas;
use personas\model as personas;
/**
* Esta página sirve para matricular a todas las personas.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		28/05/03.
*		
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

if (!empty($_POST['sel'])) { //vengo de un checkbox
	//$id_nom=$sel[0];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
} else {
	empty($_POST['id_nom'])? $id_nom="" : $id_nom=$_POST['id_nom'];
	empty($_POST['id_activ'])? $id_activ="" : $id_activ=$_POST['id_activ'];
}

$mes=date('m');
if ($mes>9)  { $any=date('Y')+1; } else { $any = date('Y'); }
$inicurs_ca=core\curso_est("inicio",$any);
$fincurs_ca=core\curso_est("fin",$any);

// no miro los de rapaso:
//   " stgr != 'r' ";
// si no hay id_nom, es para todos los alumnos
if (!empty($id_nom)) {
	$aWhere['id_nom']=$id_nom;
	$aWhere['stgr'] = 'r';
	$aOperador['stgr'] = '!=';
	// miro si es de paso
	if ($id_nom{0} == 5) {
		$GesPersonasDePaso = new personas\GestorPersonaDePaso();
		$cAlumnos = $GesPersonasDePaso->getPersonasDePaso($aWhere,$aOperador);
	} else {
		$GesPersonasDl = new personas\GestorPersonaDl();
		$cAlumnos = $GesPersonasDl->getPersonasDl($aWhere,$aOperador);
	}
	$modo_aviso = 'alert';
} else {
	// solo para los de la dl
	$aWhere['situacion'] = 'A';
	$aWhere['stgr'] = 'r';
	$aOperador['stgr'] = '!=';
	$GesPersonasDl = new personas\GestorPersonaDl();
	$cAlumnos = $GesPersonasDl->getPersonasDl($aWhere,$aOperador);
	$modo_aviso = '';
}
//$GesPersonasDl = new GestorPersonaDl();
//$cAlumnos = $GesPersonasDl->getPersonasDl($aWhere,$aOperador);
// para cada persona:
$m=0;
$msg="";
$aWhere = array();
$aOperadores = array();
// de estudios ca-n, cv-agd
$aWhere['status'] = 2;
$aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
$aOperadores['f_ini'] = 'BETWEEN';
$aWhere['id_tipo_activ'] = '^[12][13][23]';
$aOperadores['id_tipo_activ'] = '~';
foreach($cAlumnos as $oPersonaDl) {
	$id_nom=$oPersonaDl->getId_nom();
	$cAsistencias = array();
	// después me interesa el id_activ, asi que lo busco primero:
	if (empty($id_activ)) { 
		$GesAsistentes  = new asistentes\GestorAsistenteDl();
		$aWhereNom = array('id_nom'=>$id_nom,'propio'=>'t');
		$cAsistencias  = $GesAsistentes->getActividadesDeAsistente($aWhereNom,$aWhere,$aOperadores);
	} else { // puede ser que ya le pase la actividad
		$oAsistente = new asistentes\AsistenteDl(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
		$oAsistente->DBCarregar();
		$cAsistencias[0] = $oAsistente;
	}
	// si no cursa ningún ca, me salto todo
	switch (count($cAsistencias)) {
		case 0:
			$msg .= addslashes(sprintf(_("no se ha hecho nada con %s no tiene asignado ca"),$oPersonaDl->getApellidosNombre()));
			$msg .= "<br>";
		   	continue;
			break;
		case 1:
			$oAsistente = current($cAsistencias); // En le caso de varias, el indice es la f_ini (para poder ordenar en otros casos).
			$id_activ_1=$oAsistente->getId_activ();
			$est_ok=$oAsistente->getEst_ok();
			if ($est_ok != 1 ) {
				//borro el plan de estudios de esta persona.
				$GesMatriculas = new actividadestudios\GestorMatriculaDl();
				$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_nom));
				foreach ($cMatriculas as $oMatricula) {
					if ($oMatricula->DBEliminar() === false) {
						echo _("Hay un error, no se ha eliminado");
					}
				}
			
				//busco las asignaturas que ya están aprobadas y las pongo en un array.
				$GesPersonaNotas = new notas\GestorPersonaNota();
				$cPersonaNotas = $GesPersonaNotas->getPersonaNotasSuperadas($id_nom);
				$a_aprobadas = array();
				foreach ($cPersonaNotas as $oPersonaNota) {
					$a_aprobadas[]=$oPersonaNota->getId_asignatura();
				}
				//busco las asignaturas de su ca
				$GesAsignaturasCa = new actividadestudios\GestorActividadAsignatura();
				// Ojo. Se ha ido cambiando: 1. que también coja las asig con preceptor... 2. Que no coja las asignaturas con preceptor...
				$cAsignaturasCa = $GesAsignaturasCa->getActividadAsignaturas(array('id_activ'=>$id_activ_1,'tipo'=>'x'),array('tipo'=>'IS NULL'));
				foreach ($cAsignaturasCa as $oActividadAsignatura) {
					$id_asignatura = $oActividadAsignatura->getId_asignatura(); 
					$preceptor =  ($oActividadAsignatura->getTipo() == 'p')? 't' : 'f'; 
					// compruebo que no la tenga ya aprobada:
					if (in_array($id_asignatura,$a_aprobadas)) continue;
					// Si es una opcional, compruebo que puede hacerla
					if ($id_asignatura > 3000) {
						switch (substr ($id_asignatura, 1, 1)) {
							case 1: //opcional sólo de bienio
								$aWhereNota['id_nom']=$id_nom;
								$aWhereNota['id_nivel']="'123[012]'";
								$aOperadorNota['id_nivel']='~';
								$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhereNota,$aOperadorNota);
								if (is_array($cPersonaNotas) && count($cPersonaNotas)<3) {
									$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
									$oMatricula->setPreceptor($preceptor);
									if ($oMatricula->DBGuardar() === false) {
										echo _("Error al guardar la matrícula");
									}
								} else {
									continue;
								}
								break;
							case 2: //opcional sólo de cuadrienio
								$aWhereNota['id_nom']=$id_nom;
								$aWhereNota['id_nivel']="'243[01234]'";
								$aOperadorNota['id_nivel']='~';
								$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhereNota,$aOperadorNota);
								if (is_array($cPersonaNotas) && count($cPersonaNotas)<5) {
									$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
									$oMatricula->setPreceptor($preceptor);
									if ($oMatricula->DBGuardar() === false) {
										echo _("Error al guardar la matrícula");
									}
								} else {
									continue;
								}
								break;
							case 3: //opcional de bienio o cuadrienio
								$aWhereNota['id_nom']=$id_nom;
								$aWhereNota['id_nivel']="'123[012]|243[01234]'";
								$aOperadorNota['id_nivel']='~';
								$cPersonaNotas = $GesPersonaNotas->getPersonaNotas($aWhereNota,$aOperadorNota);
								if (is_array($cPersonaNotas) && count($cPersonaNotas)<8) {
									$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
									$oMatricula->setPreceptor($preceptor);
									if ($oMatricula->DBGuardar() === false) {
										echo _("Error al guardar la matrícula");
									}
								} else {
									continue;
								}
							break;				
						}
					} else {
						$oMatricula = new actividadestudios\MatriculaDl(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
						$oMatricula->setPreceptor($preceptor);
						if ($oMatricula->DBGuardar() === false) {
							echo _("Error al guardar la matrícula");
						}
					}
					$m++;
				}
				$msg .= addslashes(sprintf(_("%s se ha matriculado de %s asignaturas"),$oPersonaDl->getApellidosNombre(),$m));
				$msg .= "<br>";
			} else {
				$msg .= addslashes(sprintf(_("no se ha hecho nada com %s. ya tiene el plan de estudios confirmado"),$oPersonaDl->getApellidosNombre()));
				$msg .= "<br>";
			}
			break;
		default:
			$msg .= addslashes(sprintf(_("no se ha hecho nada con %s, tiene asignado más de un ca"),$oPersonaDl->getApellidosNombre()));
			$msg .= "<br>";
	}

}

if (empty($msg)) {
	$msg = addslashes(_("no se ha hecho nada"));
}

if ($modo_aviso == 'alert') {
	echo "<script>alert('$msg')</script>";
	if (!empty($_POST['go_to'])) {
		$oPosicion->ir_a($_POST['go_to']);
	}
} else {
	echo "$msg";
}
?>
