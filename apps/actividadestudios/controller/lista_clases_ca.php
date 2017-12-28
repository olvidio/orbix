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
	$nom_director_est="<span class=no_print>". _("para nombrarlo, ir al dossier de cargos de la actividad"). "</span>";
} else {
	$oPersona = personas\Persona::NewPersona($id_nom_dtor_est);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom_dtor_est";
		$nom_director_est='';
	} else {
		$nom_director_est=$oPersona->getApellidosNombre();
	}
}

echo $oPosicion->atras();

$cabecera="<table><thead><tr>
		<td colspan=4><h3>$nom_activ</h3></td></tr>
		<tr><td></td><td class='contenido' colspan=2>".ucfirst(_("director de estudios")).":</td>
		<td>$nom_director_est</td></tr><tr></tr></thead>";

//asignaturas del ca. (profesores y preceptores).
//asignaturas: profesores y preceptores.
// por cada asignatura
$a=0;
$tipo_old=0;
$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignatura();
$cActividadAsignaturas = $GesActividadAsignaturas->getActividadAsignaturas(array('id_activ'=>$id_activ));
foreach ( $cActividadAsignaturas as $oActividadAsignatura) {
	$a++;
	//extract($oActividadAsignatura->getTot());
	$id_asignatura = $oActividadAsignatura->getId_asignatura();
	$tipo = $oActividadAsignatura->getTipo();

	$oAsignatura = new asignaturas\Asignatura($id_asignatura);	
	$nombre_corto=$oAsignatura->getNombre_corto();
	$creditos=$oAsignatura->getCreditos();
	if (!empty($id_profesor)) {
		$oPersona = personas\Persona::NewPersona($id_profesor);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_profesor (profesor)";
			$nom_profesor = '';
		} else {
			$nom_profesor=$oPersona->getApellidosNombre();
		}
	} else {
		$nom_profesor = '';
	}
	if (empty($tipo)) { $p=1; }
	if ($tipo=="p") { $p=2; }
	
	if ($p==1) { 
		$profe=ucfirst(_("profesor")); 
		$tipo_old=$p;
		if ($a>1) echo "<div class=salta_pag></div>"; //si no es la primera salto de página
		echo $cabecera;	
	}
	if ($p==2) { 
		$profe=ucfirst(_("preceptor"));  
		if ($tipo_old!=$p) { echo "<div class=salta_pag></div>".$cabecera; } else { echo "<table><tr><td valign=top width=40px class='no_print'></td></tr>"; }
		$tipo_old=$p;
	}
	echo "<tbody><tr><td><br></td></tr><tr><td></td><td colspan=2 class='contenido'><h2>$nombre_corto</h2></td></tr>";
	echo "<tr><td></td><td></td><td>$profe:</td><td>$nom_profesor</td></tr><tr></tr>";
	// busco los alumnos
	$GesAsistentes = new asistentes\GestorAsistente(); 
	$cAsistentes = $GesAsistentes->getAsistentesDeActividad($id_activ,'apellido1,apellido2,nom');
	$i=0;
	foreach ($cAsistentes as $oActividadAsistente) {
		$i++;
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
		$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$id_nom,'id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
		// si no tiene asignaturas, miro si está de repaso
		if (is_array($cMatriculas) && count($cMatriculas)!=0) {
			echo "<tr><td></td><td align=right>$i.-</td><td colspan=2>$nom_persona ($ctr)</td></tr>";
		} else {
			$i--;
		}
	}
	echo "</tbody>";
	echo "</table>";
}
if (!empty($msg_err)) { echo $msg_err; }
?>
