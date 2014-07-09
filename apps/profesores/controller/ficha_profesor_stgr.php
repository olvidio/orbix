<?php
use asignaturas\model as asignaturas;
use personas\model as personas;
use profesores\model as profesores;
use ubis\model as ubis;
/**
* Esta página sirve para la ficha de profesor del stgr.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		21/3/2007.
*		
*/

/**
* Funciones más comunes de la aplicación
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
	empty($_POST['id_tabla'])? $id_tabla="" : $id_tabla=$_POST['id_tabla'];
}
$_POST['permiso']= empty($_POST['permiso'])? '':$_POST['permiso'];
$_POST['depende']= empty($_POST['depende'])? '':$_POST['depende'];

$num_txt='';
$agd_txt='';
$sacd_txt='';
$latin_txt='';
switch ($id_tabla) {
	case "n":
		$tabla_pau="p_numerarios";
		$num_txt="si";
		break;
	case "a":
		$tabla_pau="p_agregados";
		$agd_txt="si";
		break;
	case "pn":
	case "pa":
		$tabla_pau="p_de_paso";
		break;
}

$oPersona = personas\Persona::NewPersona($id_nom);
$nom_ap = $oPersona->getNombreApellidosCrSin();
$sacd = $oPersona->getSacd();
$id_ctr = $oPersona->getid_ctr();

$oCentroDl = new ubis\CentroDl($id_ctr);
$nombre_ubi = $oCentroDl->getNombre_ubi();

$go_to=urlencode("../../est/ficha_profesor_stgr.php?id_nom=$id_nom&id_tabla=$id_tabla&permiso=${_POST['permiso']}&depende=${_POST['depende']}");

/*
$query="SELECT ".na_cr_sin()." as nom_ap, p.sacd, u.nombre_ubi, d.latin
		FROM $tabla_pau p LEFT JOIN d_profesor_latin d USING (id_nom), u_centros_dl u
		WHERE p.id_nom=$id_nom AND p.id_ctr=u.id_ubi";
//echo "r: $query<br>";
$oDBSt_q=$oDB->query($query);
$row_nom=$oDBSt_q->fetch(PDO::FETCH_ASSOC);
extract($row_nom);
*/

$oProfesorLatin = new profesores\ProfesorLatin($id_nom);
$latin = $oProfesorLatin->getLatin();

$cosas['latin']="bloque=main&id_dossier=1022&tabla_dossier=d_profesor_latin&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to}";

if ($sacd=="t") { $sacd_txt="si"; }
if ($latin=="t") { $latin_txt="si"; }

/*
$formato_fecha="'DD.RM.YYYY'";

$sql_dep="SELECT  d.id_departamento,d.escrito_nombramiento,to_char(d.f_nombramiento,$formato_fecha) as ff_nombramiento,d.id_tipo_profesor,
					dep.departamento, t.tipo_profesor,d.f_nombramiento,d.f_cese,d.escrito_cese
					FROM d_profesor_stgr d JOIN xe_departamentos dep USING (id_departamento), xe_tipo_profesor_stgr t
					WHERE id_nom=$id_nom AND d.id_tipo_profesor=t.id_tipo_profesor AND f_cese is null
					ORDER BY f_nombramiento";
//echo "sql: $sql_nombramientos<br>";
$oDBSt_q_dep=$oDB->query($sql_dep);
$row_dep=$oDBSt_q_dep->fetch(PDO::FETCH_ASSOC);
$dep=$row_dep['departamento'];
*/

$gesProfesor = new profesores\GestorProfesor();
$cProfesores = $gesProfesor->getProfesores(array('id_nom'=>$id_nom,'_ordre'=>'f_nombramiento'),array('f_cese'=>'IS NULL'));
$a_nombramientos = array();
foreach ($cProfesores as $oProfesor) {
	$id_departamento = $oProfesor->getId_departamento();
	$escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
	$f_nombramiento = $oProfesor->getF_nombramiento();
	//$f_cese = $oProfesor->getF_cese();
	$escrito_cese = $oProfesor->getEscrito_cese();
	$id_tipo_profesor = $oProfesor->getId_tipo_profesor();

	$oDepartamento = new asignaturas\Departamento($id_departamento);
	$departamento = $oDepartamento->getDepartamento();

	$oProfesroTipo = new profesores\ProfesorTipo($id_tipo_profesor);
	$tipo_profesor = $oProfesorTipo->getTipo_profesor();

	$a_nombramientos[] = array('tipo_profesor'=>$tipo_profesor,'ff_nombramiento'=>$f_nombramiento,'escrito_nombramiento'=>$escrito_nombramiento);
}


$condicion_print='';
if (!empty($_POST['print'])) {
	$condicion_print=" AND f_cese is null";
	$aOperador['f_cese'] = 'IS NULL';
} else { // si no es para imprimir muestro todos los datos
	// director departamento (id_dossier=1020)
	$sql_director="SELECT  d.id_departamento,d.escrito_nombramiento,to_char(d.f_nombramiento,$formato_fecha) as ff_nombramiento,
						dep.departamento, d.f_nombramiento,d.f_cese,d.escrito_cese
						FROM d_profesor_director d JOIN xe_departamentos dep USING (id_departamento)
						WHERE id_nom=$id_nom
						ORDER BY f_nombramiento";
	//echo "sql: $sql_nombramientos<br>";
	$oDBSt_q_director=$oDB->query($sql_director);
	$director = $oDBSt_q_director->fetchAll();


	$gesProfesorDirector = new profesores\GestorProfesorDirector();
	$cDirectores = $gesProfesorDirector->getProfesorDirectores(array('id_nom'=>$id_nom,'_ordre'=>'f_nombramiento'));
	$a_directores = array();
	foreach ($cDirectores as $oProfesorDirector) {
		$id_departamento = $oProfesor->getId_departamento();
		$escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
		$f_nombramiento = $oProfesor->getF_nombramiento();
		//$f_cese = $oProfesor->getF_cese();
		$escrito_cese = $oProfesor->getEscrito_cese();
		$id_tipo_profesor = $oProfesor->getId_tipo_profesor();

		$oDepartamento = new asignaturas\Departamento($id_departamento);
		$departamento = $oDepartamento->getDepartamento();

		$oProfesroTipo = new profesores\ProfesorTipo($id_tipo_profesor);
		$tipo_profesor = $oProfesorTipo->getTipo_profesor();

		$a_nombramientos[] = array('tipo_profesor'=>$tipo_profesor,'ff_nombramiento'=>$f_nombramiento,'escrito_nombramiento'=>$escrito_nombramiento);
	}




	$cosas['director']="bloque=main&id_dossier=1020&tabla_dossier=d_profesor_director&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";
	// juramento
	$query="SELECT f_juramento
			FROM d_profesor_juramento
			WHERE id_nom=$id_nom ";
	//echo "r: $query<br>";
	$oDBSt_q=$oDB->query($query);
	$f_juramento=$oDBSt_q->fetchColumn();
	$cosas['juramento']="bloque=main&id_dossier=1021&tabla_dossier=d_profesor_juramento&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";
	//publicaciones (id_dossier=1012)
	$sql_publicaciones="SELECT *
			FROM d_publicaciones p
			WHERE id_nom=$id_nom
			ORDER BY f_publicacion ";
	$oDBSt_q_publicaciones=$oDB->query($sql_publicaciones);
	$publicaciones=$oDBSt_q_publicaciones->fetchAll();
	$cosas['publicaciones']="bloque=main&id_dossier=1012&tabla_dossier=d_publicaciones&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";

}


// Curriculum (id_dossier=1017)
$sql_curriculum="SELECT * FROM d_titulo_est WHERE id_nom=$id_nom ORDER BY year";
$oDBSt_q_curriculum=$oDB->query($sql_curriculum);
$curriculum=$oDBSt_q_curriculum->fetchAll();

$cosas['curriculum']="bloque=main&id_dossier=1017&tabla_dossier=d_titulo_est&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";


// Nombramientos (id_dossier=1018)
$sql_nombramientos="SELECT  d.id_departamento,d.escrito_nombramiento,to_char(d.f_nombramiento,$formato_fecha) as ff_nombramiento,d.id_tipo_profesor,
					dep.departamento, t.tipo_profesor,d.f_nombramiento,d.f_cese,d.escrito_cese
					FROM d_profesor_stgr d JOIN xe_departamentos dep USING (id_departamento), xe_tipo_profesor_stgr t
					WHERE id_nom=$id_nom AND d.id_tipo_profesor=t.id_tipo_profesor $condicion_print
					ORDER BY f_nombramiento";
//echo "sql: $sql_nombramientos<br>";
$oDBSt_q_nombramientos=$oDB->query($sql_nombramientos);
$nombramientos=$oDBSt_q_nombramientos->fetchAll();
$cosas['nombramientos']="bloque=main&id_dossier=1018&tabla_dossier=d_profesor_stgr&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";
// Ampliación docencia (id_dossier=1019)
$sql_ampliacion="SELECT  d.id_asignatura,d.escrito_nombramiento,to_char(d.f_nombramiento,$formato_fecha) as ff_nombramiento,nombre_corto,d.f_cese,d.escrito_cese
					FROM d_profesor_ampliacion d JOIN xa_asignaturas a USING (id_asignatura)
					WHERE id_nom=$id_nom $condicion_print
					ORDER BY f_nombramiento";
$oDBSt_q_ampliacion=$oDB->query($sql_ampliacion);
$ampliacion=$oDBSt_q_ampliacion->fetchAll();
$cosas['ampliacion']="bloque=main&id_dossier=1019&tabla_dossier=d_profesor_ampliacion&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";
// Convivencias y congresos (id_dossier=1024)
$GesCongresos = new profesores\GestorCongreso();
$cCongresos = $GesCongresos->getCongresos(array('id_nom'=>$id_nom,'_ordre'=>'f_ini'));
$cosas['congresos']="bloque=main&id_dossier=1024&tabla_dossier=d_congresos&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";
// Actividad docente (id_dossier=1025)
$GesDocencias = new profesores\GestorDocenciaStgr();
$cDocencias = $GesDocencias->getDocenciasStgr(array('id_nom'=>$id_nom,'_ordre'=>'curso,id_asignatura'));
$cosas['docencia']="bloque=main&id_dossier=1025&tabla_dossier=d_docencia_stgr&pau=p&id_pau=$id_nom&tabla_pau=$tabla_pau&permiso=".$_POST['permiso']."&depende=${_POST['depende']}&go_to=$go_to";

/*
// de moment no ho faig
if (!empty($_POST['print'])) {
	include("ficha_profesor_stgr.print.html");
} else {
	include("ficha_profesor_stgr.html");
}
*/
	include("../view/ficha_profesor_stgr.print.phtml");
?>
