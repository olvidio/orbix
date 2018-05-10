<?php
use actividades\model\entity as actividades;
use actividadestudios\model\entity as actividadestudios;
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$obj = 'actividadestudios\\model\\entity\\Matricula';

$id_asignatura_real = '';

$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_pau');
$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
$Qid_nivel = (integer) \filter_input(INPUT_POST, 'id_nivel');
$Qid_asignatura = (integer) \filter_input(INPUT_POST, 'id_asignatura');

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$Qid_activ = strtok($a_sel[0],"#");
	$id_asignatura_real=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

$oActividad = new actividades\Actividad($Qid_activ);
$nom_activ = $oActividad->getNom_activ();

$GesAsignaturas = new asignaturas\GestorAsignatura();

$oDesplProfesores = array();
if (!empty($id_asignatura_real)) { //caso de modificar
	$mod="editar";
	$oMatricula = new actividadestudios\Matricula(array('id_nom'=>$Qid_nom,'id_activ'=>$Qid_activ,'id_asignatura'=>$id_asignatura_real));
	$id_situacion=$oMatricula->getId_situacion();
	$preceptor=$oMatricula->getPreceptor();
	$id_preceptor=$oMatricula->getId_preceptor();
	$oAsignatura = new asignaturas\Asignatura($id_asignatura_real);
	$nombre_corto=$oAsignatura->getNombre_corto();
	$id_nivel=$id_asignatura_real;
	$id_asignatura=$id_asignatura_real;
	
	$chk_preceptor = ($preceptor===true)? 'checked' : '';
	$cOpcionales = array();
	$aFaltan=array();
	$oDesplNiveles = array();
	if (!empty($id_preceptor)) {
		$GesProfes = new profesores\model\entity\GestorProfesor();
		$oDesplProfesores= $GesProfes->getListaProfesores();
		$oDesplProfesores->setBlanco(1);
		$oDesplProfesores->setNombre('id_preceptor');
		$oDesplProfesores->setOpcion_sel($id_preceptor);
	}
} else { //caso de nueva asignatura
	$mod="nuevo";	
	$nombre_corto='';
	$preceptor='';
	$chk_preceptor = "";
	$id_preceptor='';
	$detalle='';
	$epoca='';
	$id_activ='';
	// todas las asignaturas
	$aWhere=array();
	$aOperador=array();
	$aWhere['status']='t';
	$aWhere['id_nivel']=3000;
	$aOperador['id_nivel']='<';
	$aWhere['_ordre']='id_nivel';
	$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
	// todas las opcionales 
	$aWhere=array();
	$aOperador=array();
	$aWhere['status']='t';
	$aWhere['id_nivel']='3000,5000';
	$aOperador['id_nivel']='BETWEEN';
	$aWhere['_ordre']='nombre_corto';
	$cOpcionales = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
	// Asignaturas superadas
	$GesNotas = new notas\GestorNota();
	$cSuperadas = $GesNotas->getNotas(array('superada'=>'t'));
	$cond='';
	$c=0;
	foreach ($cSuperadas as $Nota) {
		if ($c >0 ) $cond.='|';
		$c++;
		$cond.=$Nota->getId_situacion();
	}
	$aWhere=array();
	$aOperador=array();
	$aWhere['id_situacion']=$cond;
	$aOperador['id_situacion']='~';
	$aWhere['id_nom']=$Qid_nom;
	$aWhere['id_nivel']=3000;
	$aOperador['id_nivel']='<';
	$aWhere['_ordre']='id_nivel';
	$GesPersonaNotas = new notas\GestorPersonaNota();
	$cAsignaturasSuperadas = $GesPersonaNotas->getPersonaNotas($aWhere,$aOperador);
	$aSuperadas=array();
	foreach($cAsignaturasSuperadas as $oAsignatura) {
		$id_nivel = $oAsignatura->getId_nivel();
		$id_asignatura = $oAsignatura->getId_asignatura();
		$aSuperadas[$id_nivel]=$id_asignatura;
	}
	// También quito las ya matriculadas
	$GesMatriculas = new actividadestudios\GestorMatriculaDl();
	$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom'=>$Qid_nom,'id_activ'=>$Qid_activ));
	$aMatriculadas=array();
	foreach ($cMatriculas as $oMatricula) {
		$id_asignatura=$oMatricula->getId_asignatura();
		$id_nivel = $oMatricula->getId_nivel();
		$aMatriculadas[$id_nivel]=$id_asignatura;
	}
	// asignaturas posibles
	$aFaltan=array();
	foreach ($cAsignaturas as $oAsignatura) {
		$id_nivel = $oAsignatura->getId_nivel();
		$id_asignatura = $oAsignatura->getId_asignatura();
		$nombre_corto = $oAsignatura->getNombre_corto();
		if (array_key_exists($id_nivel,$aSuperadas)) continue;
		if (array_key_exists($id_nivel,$aMatriculadas)) continue;
		$aFaltan[$id_nivel]=$nombre_corto;
	}

	$oDesplNiveles = new web\Desplegable();
	$oDesplNiveles->setNombre('id_nivel');
	$oDesplNiveles->setOpciones($aFaltan);
	$oDesplNiveles->setBlanco(1);
	$oDesplNiveles->setAction('fnjs_cmb_opcional()');
}

// miro cuales son las opcionales genéricas, para la funcion
//  fnjs_cmb_opcional de javascript.
// la condicion es que tengan id_sector=1
$aWhere=array();
$aOperador=array();
$aWhere['status']='t';
$aWhere['id_sector']=1;
$aWhere['id_nivel']=3000;
$aOperador['id_nivel']='<';
$aWhere['_ordre']='nombre_corto';
$cOpcionalesGenericas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
$condicion='';
$lista_nivel_op='';
foreach ($cOpcionalesGenericas as $oOpcional) {
	$id_nivel_j = $oOpcional->getId_nivel();
	$condicion.="id==".$id_nivel_j." || ";
	$lista_nivel_op.=$id_nivel_j.",";
}
$condicion_js=substr($condicion,0,-4);

$oHash = new web\Hash();
$camposForm = '';
$oHash->setCamposNo('preceptor!id_preceptor');
$a_camposHidden = array(
		'id_pau' => $Qid_nom,
		'id_activ' => $Qid_activ,
		'mod' => $mod,
		);
if (!empty($id_asignatura_real)) {
	$a_camposHidden['id_asignatura'] = $id_asignatura;
	$a_camposHidden['id_nivel'] = $id_nivel;
} else {
	$camposForm .= 'id_asignatura!id_nivel';
}
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$url_ajax = core\ConfigGlobal::getWeb().'/apps/notas/controller/notas_ajax.php';
$oHash1 = new web\Hash();
$oHash1->setUrl($url_ajax);
$oHash1->setCamposForm('que!id_nom'); 
//$oHash1->setCamposNo('id_nom'); 
$h1 = $oHash1->linkSinVal();
$oHash2 = new web\Hash();
$oHash2->setUrl($url_ajax);
$oHash2->setCamposForm('que'); 
$h2 = $oHash2->linkSinVal();

$a_campos = ['obj' => $obj,
			'oPosicion' => $oPosicion,
			'oHash' => $oHash,
			'url_ajax' => $url_ajax,
			'h1' => $h1,
			'h2' => $h2,
			'condicion_js' => $condicion_js,
			'nom_activ' => $nom_activ,
			'id_asignatura_real' => $id_asignatura_real,
			'nombre_corto' => $nombre_corto,
			'oDesplNiveles' => $oDesplNiveles,
			'chk_preceptor' => $chk_preceptor,
			'id_preceptor' => $id_preceptor,
			'oDesplProfesores' => $oDesplProfesores,
			];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('form_1303.phtml',$a_campos);