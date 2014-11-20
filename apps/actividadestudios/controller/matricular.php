<?php
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
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ('classes/actividades/ext_a_actividades_gestor.class');
	require_once ('classes/actividades/d_asignaturas_activ_gestor.class');
	require_once ('classes/activ-personas/d_asistentes_activ_gestor.class');
	require_once ('classes/activ-personas/d_matriculas_activ_gestor.class');
	require_once ('classes/personas/e_notas_gestor.class');
	require_once ('classes/personas/p_n_agd_gestor.class');
	require_once ('classes/personas/p_de_paso_gestor.class');

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

include_once(ConfigGlobal::$dir_programas.'/func_web.php');  


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
$inicurs_ca=curso_est("inicio",$any);
$fincurs_ca=curso_est("fin",$any);
$periodo="BETWEEN '$inicurs_ca' AND '$fincurs_ca'";

// no miro los de rapaso:
//   " stgr != 'r' ";
// si no hay id_nom, es para todos los alumnos
if (!empty($id_nom)) {
	$aWhere['id_nom']=$id_nom;
	$aWhere['stgr'] = 'r';
	$aOperador['stgr'] = '!=';
	// miro si es de paso
	if ($id_nom{0} == 5) {
		$GesPersonasDePaso = new GestorPersonaDePaso();
		$cAlumnos = $GesPersonasDePaso->getPersonasDePaso($aWhere,$aOperador);
	} else {
		$GesPersonasNAgd = new GestorPersonaNAgd();
		$cAlumnos = $GesPersonasNAgd->getPersonasNAgd($aWhere,$aOperador);
	}
} else {
	// solo para los de la dl
	$aWhere['fichero'] = 'A';
	$aWhere['stgr'] = 'r';
	$aOperador['stgr'] = '!=';
	$GesPersonasNAgd = new GestorPersonaNAgd();
	$cAlumnos = $GesPersonasNAgd->getPersonasNAgd($aWhere,$aOperador);
}
//$GesPersonasNAgd = new GestorPersonaNAgd();
//$cAlumnos = $GesPersonasNAgd->getPersonasNAgd($aWhere,$aOperador);
// para cada persona:
$m=0;
$msg="";
foreach($cAlumnos as $oPersonaNAgd) {
	$id_nom=$oPersonaNAgd->getId_nom();
	// después me interesa el id_activ, asi que lo busco primero:
	if (empty($id_activ)) { 
		$GesAsistentes  = new GestorActividadAsistente();
		$aWhereNom = array('id_nom'=>$id_nom,'propio'=>'t');
		// de estudios ca-n, cv-agd
		$aWhere['status'] = 2;
		$aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
		$aOperadores['f_ini'] = 'BETWEEN';
		$aWhere['id_tipo_activ'] = '^[12][13][23]';
		$aOperadores['id_tipo_activ'] = '~';
		$cActividades  = $GesAsistentes->getActividadesDeAsistente($aWhereNom,$aWhere,$aOperadores);
	} else { // puede ser que ya le pase la actividad
		$cActividades[0] = new ActividadAsistente(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
	}
/*
	$sql_activ="select  asis.id_activ,asis.est_ok
			FROM d_asistentes_activ asis, a_actividades a
			WHERE asis.id_nom=$id_nom  AND a.id_activ=asis.id_activ  $cond_act
				AND asis.propio='t'
				AND a.status=2
				AND a.f_ini $periodo 
			";
	//echo "sql_activ: $sql_activ<br>";
	$oDBSt_q1=$oDB->query($sql_activ);
	$n=$oDBSt_q1->rowCount();
	*/

	// si no cursa ningún ca, me salto todo
	switch (count($cActividades)) {
		case 0:
			$msg.="no se ha hecho nada con ".$oPersonaNAgd->getApellidosNombre().". No tiene asignado ca <br>";
		   	continue;
			break;
		case 1:
			$oActividadAsistente = $cActividades[0];
			$id_activ_1=$oActividadAsistente->getId_activ();
			$est_ok=$oActividadAsistente->getEst_ok();
			if ($est_ok === false ) {
				//borro el plan de estudios de esta persona.
				$GesMatriculas = new GestorMatricula();
				$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_nom));
				foreach ($cMatriculas as $oMatricula) {
					if ($oMatricula->DBEliminar() === false) {
						echo _('Hay un error, no se ha eliminado');
					}
				}
			
				//busco las asignaturas que ya están aprobadas y las pongo en un array.
				$GesPersonaNotas = new GestorPersonaNota();
				$cPersonaNotas = $GesPersonaNotas->getPersonaNotasSuperadas($id_nom);
				$a_aprobadas = array();
				foreach ($cPersonaNotas as $oPersonaNota) {
					$a_aprobadas[]=$oPersonaNota->getId_asignatura();
				}
				//busco las asignaturas de su ca
				$GesAsignaturasCa = new GestorActividadAsignatura();
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
								$aWhere['id_nom']=$id_nom;
								//$aWhere['id_nivel']="'123(0|1|2)'";
								$aWhere['id_nivel']="'123[012]'";
								$aOperador['id_nivel']='~';
								$cPersonaNotas = $GesPersonaNotas->getPersonaNotas(array('id_nom'=>$id_nom));
								if (is_array($cPersonaNotas) && count($cPersonaNotas)<3) {
									$oMatricula = new Matricula(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
									$oMatricula->setPreceptor($preceptor);
									if ($oMatricula->DBGuardar() === false) {
										echo "Error al guardar la matrícula";
									}
								} else {
									continue;
								}
								break;
							case 2: //opcional sólo de cuadrienio
								$aWhere['id_nom']=$id_nom;
								//$aWhere['id_nivel']="'243(0|1|2|3|4)'";
								$aWhere['id_nivel']="'243[01234]'";
								$aOperador['id_nivel']='~';
								$cPersonaNotas = $GesPersonaNotas->getPersonaNotas(array('id_nom'=>$id_nom));
								if (is_array($cPersonaNotas) && count($cPersonaNotas)<5) {
									$oMatricula = new Matricula(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
									$oMatricula->setPreceptor($preceptor);
									if ($oMatricula->DBGuardar() === false) {
										echo "Error al guardar la matrícula";
									}
								} else {
									continue;
								}
								break;
							case 3: //opcional de bienio o cuadrienio
								$aWhere['id_nom']=$id_nom;
								$aWhere['id_nivel']="'123[012]|243[01234]'";
								$aOperador['id_nivel']='~';
								$cPersonaNotas = $GesPersonaNotas->getPersonaNotas(array('id_nom'=>$id_nom));
								if (is_array($cPersonaNotas) && count($cPersonaNotas)<8) {
									$oMatricula = new Matricula(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
									$oMatricula->setPreceptor($preceptor);
									if ($oMatricula->DBGuardar() === false) {
										echo "Error al guardar la matrícula";
									}
								} else {
									continue;
								}
							break;				
						}
					} else {
						$oMatricula = new Matricula(array('id_activ'=>$id_activ_1,'id_asignatura'=>$id_asignatura,'id_nom'=>$id_nom));
						$oMatricula->setPreceptor($preceptor);
						if ($oMatricula->DBGuardar() === false) {
							echo "Error al guardar la mtrícula";
						}
					}
					$m++;
				}
			}

			break;
		default:
			$msg.="no se ha hecho nada con ".$oPersonaNAgd->getApellidosNombre().". Tiene asignado más de un ca <br>";
	}

}

echo $msg;

echo sprintf(_("finitoo. Se ha matriculado de %s asignaturas"),$m)."<br>";
if (!empty($_POST['go_to'])) {
	/**
	* Funciones que agilizan la navegación web
	*/
	include_once("./func_web.php");  
	//echo "go_to: ".$_POST['go_to']."<br>";
	$rta=ir_a($_POST['go_to']);
	exit;
}
