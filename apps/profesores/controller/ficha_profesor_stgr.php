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

$dele = core\ConfigGlobal::mi_dele(); 
$dele .= (core\ConfigGlobal::mi_sfsv()==2)? 'f' : ''; 

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	$id_nom=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
} else {
	$id_pau = empty($_POST['id_pau'])? '' : $_POST['id_pau'];
	$id_nom = empty($_POST['id_nom'])? '' : $_POST['id_nom'];
	$id_nom = empty($id_nom)? $id_pau : $id_nom;
	$id_tabla = empty($_POST['id_tabla'])? '' : $_POST['id_tabla'];
}
$_POST['permiso']= empty($_POST['permiso'])? '':$_POST['permiso'];
$_POST['depende']= empty($_POST['depende'])? '':$_POST['depende'];
$obj_pau = empty($_POST['obj_pau'])? '':$_POST['obj_pau'];

$aWhere = array('id_nom'=>$id_nom,'_ordre'=>'f_nombramiento');
$aOperador = array();
if (!empty($_POST['print'])) { $aWhere['f_cese'] = 'NULL'; $aOperador['f_cese'] = 'IS NULL'; }

$num_txt='';
$agd_txt='';
$sacd_txt='';
$latin_txt='';
switch ($id_tabla) {
	case "n":
		$num_txt="si";
		break;
	case "a":
		$agd_txt="si";
		break;
	case "pn":
	case "pa":
		break;
}

$oPersona = personas\Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
	$msg_err = "<br>$oPersona con id_nom: $id_nom";
	exit($msg_err);
}
$nom_ap = $oPersona->getNombreApellidosCrSin();
$sacd = $oPersona->getSacd();
$id_ctr = $oPersona->getid_ctr();

$oCentroDl = new ubis\CentroDl($id_ctr);
$nombre_ubi = $oCentroDl->getNombre_ubi();

$go_to=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/profesores/controller/ficha_profesor_stgr.php?'.http_build_query(array('id_nom'=>$id_nom,'id_tabla'=>$id_tabla,'permiso'=>$_POST['permiso'],'depende'=>$_POST['depende'])));

$oProfesorLatin = new profesores\ProfesorLatin($id_nom);
$latin = $oProfesorLatin->getLatin();

$go_cosas['print']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/profesores/controller/ficha_profesor_stgr.php?'.http_build_query(array('id_nom'=>$id_nom,'id_tabla'=>$id_tabla,'print'=>'1')));

$cosas=array('bloque'=>'main',
			'id_dossier'=>1022, //latin
			'pau'=>'p',
			'id_pau'=>$id_nom,
			'obj_pau'=>$obj_pau,
			'permiso'=>$_POST['permiso'],
			'depende'=>$_POST['depende'],
			'go_to'=>$go_to);
$go_cosas['latin']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

if ($sacd=="t") { $sacd_txt="si"; }
if ($latin=="t") { $latin_txt="si"; }

$gesProfesor = new profesores\GestorProfesor();
$cProfesores = $gesProfesor->getProfesores($aWhere,$aOperador);
$a_nombramientos = array();
foreach ($cProfesores as $oProfesor) {
	$id_departamento = $oProfesor->getId_departamento();
	$escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
	$f_nombramiento = $oProfesor->getF_nombramiento();
	//$f_cese = $oProfesor->getF_cese();
	$escrito_cese = $oProfesor->getEscrito_cese();
	$id_tipo_profesor = $oProfesor->getId_tipo_profesor();

	$oDepartamento = new asignaturas\Departamento(array('id_departamento'=>$id_departamento));
	$departamento = $oDepartamento->getDepartamento();

	$oProfesorTipo = new profesores\ProfesorTipo($id_tipo_profesor);
	$tipo_profesor = $oProfesorTipo->getTipo_profesor();

	$a_nombramientos[] = array('departamento'=>$departamento,'tipo_profesor'=>$tipo_profesor,'f_nombramiento'=>$f_nombramiento,'escrito_nombramiento'=>$escrito_nombramiento);
}
$dep = !empty($departamento)? $departamento : '';

if (empty($_POST['print'])) { // si no es para imprimir muestro todos los datos
	// director departamento (id_dossier=1020)  //////////////////////////////////
	$gesProfesorDirector = new profesores\GestorProfesorDirector();
	$cDirectores = $gesProfesorDirector->getProfesoresDirectores($aWhere,$aOperador);
	$a_director = array();
	foreach ($cDirectores as $oProfesorDirector) {
		$id_departamento = $oProfesorDirector->getId_departamento();
		$escrito_nombramiento = $oProfesorDirector->getEscrito_nombramiento();
		$f_nombramiento = $oProfesorDirector->getF_nombramiento();
		$f_cese = $oProfesorDirector->getF_cese();
		$escrito_cese = $oProfesorDirector->getEscrito_cese();

		$oDepartamento = new asignaturas\Departamento(array('id_departamento'=>$id_departamento));
		$departamento = $oDepartamento->getDepartamento();

		$a_director[] = array('departamento'=>$departamento,'f_nombramiento'=>$f_nombramiento,'escrito_nombramiento'=>$escrito_nombramiento,'f_cese'=>$f_cese,'escrito_cese'=>$escrito_cese);
	}
	$cosas['id_dossier']=1020;
	$go_cosas['director']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

	// juramento //////////////////////////
	$oJuramento = new profesores\ProfesorJuramento(array('id_nom'=>$id_nom));
	$oJuramento->DBCarregar();
	$f_juramento = $oJuramento->getF_juramento();
	$cosas['id_dossier']=1021;
	$go_cosas['juramento']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

	//publicaciones (id_dossier=1012)  ///////////////////////////////////
	$gesProfesorPublicaciones = new profesores\GestorProfesorPublicacion();
	$cProfesorPublicaciones = $gesProfesorPublicaciones->getProfesorPublicaciones(array('id_nom'=>$id_nom,'_ordre'=>'f_publicacion'));
	$a_publicaciones = array();
	foreach ($cProfesorPublicaciones as $oProfesorPublicacion) {
		$pendiente = $oProfesorPublicacion->getPendiente();
		$tipo_publicacion = $oProfesorPublicacion->getTipo_publicacion();
		$titulo = $oProfesorPublicacion->getTitulo();
		$editorial = $oProfesorPublicacion->getEditorial();
		$coleccion = $oProfesorPublicacion->getColeccion();
		$f_publicacion = $oProfesorPublicacion->getF_publicacion();
		$referencia = $oProfesorPublicacion->getReferencia();
		$lugar = $oProfesorPublicacion->getLugar();
		$observ = $oProfesorPublicacion->getObserv();

		$a_publicaciones[] = array( 'pendiente'=>$pendiente, 'tipo_publicacion'=>$tipo_publicacion, 'titulo'=>$titulo, 'editorial'=>$editorial, 'coleccion'=>$coleccion, 'f_publicacion'=>$f_publicacion, 'referencia'=>$referencia, 'lugar'=>$lugar, 'observ'=>$observ);

	}
	$cosas['id_dossier']=1012;
	$go_cosas['publicaciones']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));
}


// Curriculum (id_dossier=1017) ///////////////////
$gesProfesorTituloEst = new profesores\GestorProfesorTituloEst();
$cTitulosEst = $gesProfesorTituloEst->getTitulosEst(array('id_nom'=>$id_nom,'_ordre'=>'year'));
$a_curriculum = array();
foreach ($cTitulosEst as $oProfesorTituloEst) {
	$eclesiastico = $oProfesorTituloEst->getEclesiastico();
	$titulo = $oProfesorTituloEst->getTitulo();
	$centro_dnt = $oProfesorTituloEst->getCentro_dnt();
	$year = $oProfesorTituloEst->getYear();

	$a_curriculum[] = array( 'eclesiastico'=>$eclesiastico, 'titulo'=>$titulo, 'centro_dnt'=>$centro_dnt, 'year'=>$year);
}
$cosas['id_dossier']=1017;
$go_cosas['curriculum']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

// Nombramientos (id_dossier=1018) ///////////////////////////
$gesProfesor = new profesores\GestorProfesor();
$cProfesores = $gesProfesor->getProfesores($aWhere,$aOperador);
$a_nombramientos = array();
$id_departamento = '';
foreach ($cProfesores as $oProfesor) {
	$id_departamento = $oProfesor->getId_departamento();
	$escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
	$f_nombramiento = $oProfesor->getF_nombramiento();
	$escrito_nombramiento = $oProfesor->getEscrito_nombramiento();
	$f_cese = $oProfesor->getF_cese();
	$escrito_cese = $oProfesor->getEscrito_cese();
	$id_tipo_profesor = $oProfesor->getId_tipo_profesor();

	$oDepartamento = new asignaturas\Departamento(array('id_departamento'=>$id_departamento));
	$departamento = $oDepartamento->getDepartamento();

	$oProfesorTipo = new profesores\ProfesorTipo($id_tipo_profesor);
	$tipo_profesor = $oProfesorTipo->getTipo_profesor();

	$a_nombramientos[] = array('departamento'=>$departamento,'tipo_profesor'=>$tipo_profesor,'f_nombramiento'=>$f_nombramiento,'escrito_nombramiento'=>$escrito_nombramiento,'f_cese'=>$f_cese,'escrito_cese'=>$escrito_cese);
}
$cosas['id_dossier']=1018;
$go_cosas['nombramientos']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

// Ampliación docencia (id_dossier=1019) ///////////////////
$gesProfesorAmpliacion = new profesores\GestorProfesorAmpliacion();
$cProfesorAmpliaciones = $gesProfesorAmpliacion->getProfesorAmpliaciones($aWhere,$aOperador);
$a_ampliacion = array();
$id_departamento = '';
foreach ($cProfesorAmpliaciones as $oProfesorAmpliacion) {
	$id_asignatura = $oProfesorAmpliacion->getId_asignatura();
	$f_nombramiento = $oProfesorAmpliacion->getF_nombramiento();
	$escrito_nombramiento = $oProfesorAmpliacion->getEscrito_nombramiento();
	$f_cese = $oProfesorAmpliacion->getF_cese();
	$escrito_cese = $oProfesorAmpliacion->getEscrito_cese();

	$oAsignatura = new asignaturas\Asignatura(array('id_asignatura'=>$id_asignatura));
	$nombre_corto = $oAsignatura->getNombre_corto();


	$a_ampliacion[] = array('nombre_corto'=>$nombre_corto,'f_nombramiento'=>$f_nombramiento,'escrito_nombramiento'=>$escrito_nombramiento,'f_cese'=>$f_cese,'escrito_cese'=>$escrito_cese);
}
$cosas['id_dossier']=1019;
$go_cosas['ampliacion']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

// Convivencias y congresos (id_dossier=1024) //////////////////////////////
$GesProfesorCongresos = new profesores\GestorProfesorCongreso();
$cProfesorCongresos = $GesProfesorCongresos->getProfesorCongresos(array('id_nom'=>$id_nom,'_ordre'=>'f_ini'));
$cosas['id_dossier']=1024;
$go_cosas['congresos']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

// Actividad docente (id_dossier=1025) ////////////////////////////////////
$GesDocencias = new profesores\GestorProfesorDocenciaStgr();
$cDocencias = $GesDocencias->getProfesorDocenciasStgr(array('id_nom'=>$id_nom,'_ordre'=>'curso,id_asignatura'));
$cosas['id_dossier']=1025;
$go_cosas['docencia']=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/datos_sql.php?'.http_build_query($cosas));

echo $oPosicion->atras();

if (!empty($_POST['print'])) {
	include("../view/ficha_profesor_stgr.print.phtml");
} else {
	include("../view/ficha_profesor_stgr.phtml");
}
?>
