<?php
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use actividadestudios\model as actividadestudios;
use asignaturas\model as asignaturas;
use asistentes\model as asistentes;
use personas\model as personas;
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
	$id_sel=$_POST['sel'];
	$id_activ=strtok($_POST['sel'][0],"#");
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
}
$msg_err = '';

// nombre de la actividad
$oActividad = new actividades\Actividad($id_activ);
$nom_activ=$oActividad->getNom_activ();
	
//director de estudios
$GesCargos = new actividadcargos\GestorCargo();
$cCargos = $GesCargos->getCargos(array('cargo'=>'d.est.'));
$id_cargo = $cCargos[0]->getId_cargo(); // solo hay un cargo de director de estudios.
$GesActividadCargos = new actividadcargos\GestorActividadCargo();
$cActividadCargos = $GesActividadCargos->getActividadCargos(array('id_activ'=>$id_activ,'id_cargo'=>$id_cargo));
if (is_array($cActividadCargos) && count($cActividadCargos) > 0) {
	$id_nom_dtor_est = $cActividadCargos[0]->getId_nom(); // Imagino que sólo hay uno.
} else {
	$id_nom_dtor_est = '';
}

if (empty($id_nom_dtor_est)) {
	$nom_director_est=_("para nombrarlo, ir al dossier de cargos de la actividad");
} else {
	$oPersona = personas\Persona::NewPersona($id_nom_dtor_est);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom_dtor_est";
		$nom_director_est = '';
	} else {
		$nom_director_est = $oPersona->getApellidosNombre();
	}
}

echo $oPosicion->mostrar_left_slide();
?>

<table><tr>
	<td colspan=3><h3><?= $nom_activ ?></h3></td></tr>
	<tr><td></td><td class='contenido'><?= ucfirst(_("director de estudios")) ?>:</td>
	<td><?= $nom_director_est ?></td></tr>
<?php
//asignaturas: profesores y preceptores.
// por cada asignatura
$a=0;
$tipo_old=0;
$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignaturaDl();
$cActividadAsignaturas = $GesActividadAsignaturas->getActividadAsignaturas(array('id_activ'=>$id_activ,'_ordre'=>'tipo'));
foreach ( $cActividadAsignaturas as $oActividadAsignatura) {
	$a++;
	extract($oActividadAsignatura->getTot());

	$oAsignatura = new asignaturas\Asignatura($id_asignatura);	
	$nombre_corto=$oAsignatura->getNombre_corto();
	$creditos=$oAsignatura->getCreditos();

	if (!empty($id_profesor)) {
		$oPersona = personas\Persona::NewPersona($id_profesor);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_profesor";
			continue;
		}
		$nom_profesor=$oPersona->getApellidosNombre();
	} else {
		$nom_profesor='?';
	}
	if (empty($tipo)) { $p=1; }
	if ($tipo=="p") { $p=2; }
	
	if ($tipo_old!=$p) {
		if ($p==1) { echo  "<tr><td></td><td class='contenido'>".ucfirst(_("profesores")).":</td></tr>"; }
		if ($p==2) { echo  "<tr><td></td><td class='contenido'>".ucfirst(_("preceptores")).":</td></tr>"; }
	}
	echo "<tr><td></td>
	   	<td>$nombre_corto ($creditos)</td>
		<td>$nom_profesor</td>
	   	</tr>";
	$tipo_old=$p;
}

//buco los asistentes:
$GesAsistentes = new asistentes\GestorAsistente(); 
$cAsistentes = $GesAsistentes->getAsistentesDeActividad($id_activ,'apellido1,apellido2,nom');
echo  "<tr><td></td><td class='contenido'>".ucfirst(_("asistentes")).":</td></tr><tr></tr>"; 
$a=0;
$a_old=0;
foreach ($cAsistentes as $oActividadAsistente) {
	$a++;
	$id_nom=$oActividadAsistente->getId_nom();
	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom";
		continue;
	}
	$nom_persona=$oPersona->getApellidosNombre();
	$ctr=$oPersona->getCentro_o_dl();
	$stgr=$oPersona->getStgr();
	// busco las asignaturas de esta persona
	$GesMatriculas = new actividadestudios\GestorMatricula();
	$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_nom,'id_activ'=>$id_activ));
	// si no tiene asignaturas, miro si está de repaso
	if (is_array($cMatriculas) && count($cMatriculas)==0) {
		switch ($stgr) {
			case "r":
				echo "<tr><td>$a</td><td>$nom_persona ($ctr)</td><td>".ucfirst(_("repaso"))."</td></tr>";
			break;
			case "n":
				echo "<tr><td>$a</td><td>$nom_persona ($ctr)</td><td>".ucfirst(_("plan de formación"))."</td></tr>";
			break;
			default:
				echo "<tr><td>$a</td><td>$nom_persona ($ctr)</td><td>???</td></tr>";	
		}
	} else {
		foreach($cMatriculas as $oMatricula) {
			$id_asignatura = $oMatricula->getId_asignatura();
			$preceptor = $oMatricula->getPreceptor();

			$oAsignatura = new asignaturas\Asignatura($id_asignatura);	
			$nombre_corto=$oAsignatura->getNombre_corto();
			$creditos=$oAsignatura->getCreditos();

			if ($preceptor=="t") { $preceptor="(preceptor)"; } else { $preceptor=""; }
			if ($a_old!=$a) {
				echo "<tr><td>$a</td><td>$nom_persona ($ctr)</td><td>$nombre_corto ($creditos) $preceptor</td></tr>";
			} else {
				echo "<tr><td></td><td></td><td>$nombre_corto ($creditos) $preceptor</td></tr>";
			}
			$a_old=$a;
		}
	}
}

echo "</table>";

if (!empty($msg_err)) { echo $msg_err; }


?> 
