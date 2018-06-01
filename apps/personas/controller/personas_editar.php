<?php
use ubis\model\entity as ubis;
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ("apps/web/func_web.php");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$nuevo = (integer)  filter_input(INPUT_POST, 'nuevo');
$Qobj_pau = (string)  filter_input(INPUT_POST, 'obj_pau');
	
$oPosicion->recordar();


if (!empty($nuevo)) {
	$obj = 'personas\\model\\entity\\'.$Qobj_pau;
	$oPersona = new $obj;
	$cDatosCampo = $oPersona->getDatosCampos();
	$oDbl = $oPersona->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;
	}
	$a_campos['f_situacion'] = date('j/m/Y');
	$a_campos['id_nom'] = '';
	$a_campos['obj'] = $oPersona;
	$a_campos['id_tabla'] = empty($_POST['id_tabla'])? '' : $_POST['id_tabla'];
	$nom_ctr = '';
} else {
	$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if (!empty($a_sel)) { //vengo de un checkbox
		$Qid_nom = strtok($a_sel[0],"#");
		$id_tabla=strtok("#");
		// el scroll id es de la página anterior, hay que guardarlo allí
		$oPosicion->addParametro('id_sel',$a_sel,1);
		$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
		$oPosicion->addParametro('scroll_id',$scroll_id,1);
	} else {
		$Qid_nom = (integer)  filter_input(INPUT_POST, 'id_nom');
		$id_tabla = (string)  filter_input(INPUT_POST, 'tabla');
	}
	// Sobre-escribe el scroll_id que se pueda tener
	if (isset($_POST['stack'])) {
		$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	} else { 
		$stack = '';
	}
	//Si vengo por medio de Posicion, borro la última
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}

	$obj = 'personas\\model\\entity\\'.$Qobj_pau;
	$oPersona = new $obj($Qid_nom);
	
	$id_tabla = $oPersona->getId_tabla();
	$dl = $oPersona->getDl();
	$sacd = $oPersona->getSacd();
	$trato = $oPersona->getTrato();
	$nom = $oPersona->getNom();
	$nx1 = $oPersona->getNx1();
	$apellido1 = $oPersona->getApellido1();
	$nx2 = $oPersona->getNx2();
	$apellido2 = $oPersona->getApellido2();
	$f_nacimiento = $oPersona->getF_nacimiento();
	$lengua = $oPersona->getLengua();
	$situacion = $oPersona->getSituacion();
	$f_situacion = $oPersona->getF_situacion();
	$apel_fam = $oPersona->getApel_fam();
	$inc = $oPersona->getInc();
	$f_inc = $oPersona->getF_inc();
	$stgr = $oPersona->getStgr();
	$profesion = $oPersona->getProfesion();
	$eap = $oPersona->getEap();
	$observ = $oPersona->getObserv();
	$lugar_nacimiento = $oPersona->getLugar_nacimiento();
	// los de paso no tienen ctr
	if (method_exists($oPersona, "getId_ctr")) {
		$id_ctr = $oPersona->getId_ctr();
	} else {
		$id_ctr = '';
	}
	// para los de paso
	if (method_exists($oPersona, "getEdad")) {
		$edad = $oPersona->getEdad();
	} else {
		$edad = '';
	}
	if (method_exists($oPersona, "getProfesor_stgr")) {
		$profesor_stgr = $oPersona->getProfesor_stgr();
	} else {
		$profesor_stgr = '';
	}
	// para el ctr hay que buscar el nombre
	if (!empty($id_ctr)) {
		$oCentroDl = new ubis\CentroDl($id_ctr);
		$nom_ctr = $oCentroDl->getNombre_ubi();
		$oDesplCentroDl = array();
	} else {
		$nom_ctr = '';
	}
}

// para el ctr, si es nuevo o está vacio
if (empty($nom_ctr)) {
	$GesCentroDl = new ubis\GestorCentroDl();
	$oDesplCentroDl = $GesCentroDl->getListaCentros();
	$oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
	$oDesplCentroDl->setNombre("id_ctr");
}

$ok=0;
$ok_txt=0;
$presentacion="persona_form.phtml";
switch ($Qobj_pau){
	case "PersonaAgd":
		$id_tabla = 'a';
		if ($_SESSION['oPerm']->have_perm("agd")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("agd") or $_SESSION['oPerm']->have_perm("dtor"))) {
			//$presentacion="p_agregados.phtml";
			$presentacion="persona_form.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaN":
		$id_tabla = 'n';
		if ($_SESSION['oPerm']->have_perm("sm")) { $ok=1; } 
		if (($_SESSION['oPerm']->have_perm("sm") or $_SESSION['oPerm']->have_perm("dtor"))) { 
			//$presentacion="p_numerarios.phtml";
			$presentacion="persona_form.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaNax":
		$id_tabla = 'x';
		if ($_SESSION['oPerm']->have_perm("sm")) { $ok=1; } 
		if ($_SESSION['oPerm']->have_perm("sm") or $_SESSION['oPerm']->have_perm("dtor")) { 
			//$presentacion="p_numerarios.phtml";
			$presentacion="persona_form.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaS":
		$id_tabla = 's';
		if ($_SESSION['oPerm']->have_perm("sg")) { $ok=1; } 
		if ($_SESSION['oPerm']->have_perm("sg") or $_SESSION['oPerm']->have_perm("dtor")) { 
			//$presentacion="p_supernumerarios.phtml";
			$presentacion="persona_form.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaSSSC":
		$id_tabla = 'sssc';
		if ($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd")) { $ok=1; } 
		if ($_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("dtor")) { 
			//$presentacion="p_sssc.phtml";
			$presentacion="persona_form.phtml";
			$ok_txt=1;
		} else {
			$presentacion="p_public_personas.phtml";
		}
		break;
	case "PersonaEx":
		if (empty($id_tabla)) $id_tabla = 'pn';
		$presentacion="persona_de_paso.phtml";
		if ($_SESSION['oPerm']->have_perm("agd") or $_SESSION['oPerm']->have_perm("sm") or $_SESSION['oPerm']->have_perm("des") or $_SESSION['oPerm']->have_perm("est")) { $ok=1; } 
		$ok_txt=1;
		break;
}

if (empty($nuevo)) {
	$ir_a_traslado=web\hash::link('apps/personas/controller/traslado_form.php?'.http_build_query(array('pau'=>'p','id_pau'=>$Qid_nom,'obj_pau'=>$Qobj_pau)));
}

$botones = 0;
/*
1: guardar cambios
2: eliminar
3: formato texto
*/
if ($ok==1) {	
	$botones = '1';
	// de momento se lo permito a los de paso i cp
	if ($Qobj_pau == 'PersonaEx') {
		$botones .= ',2';
	}
}
if 	($ok_txt==1) {
	//$botones .= ',3'; // de momento no lo pongo
}

//------------------------------------------------------------------------

$GesSituacion = new personas\model\entity\GestorSituacion();
$oDesplSituacion = $GesSituacion->getListaSituaciones();
$oDesplSituacion->setNombre("situacion");
$oDesplSituacion->setOpcion_sel($oPersona->getSituacion());


$campos_chk = 'sacd';

$oHash = new web\Hash();
$oHash->setcamposForm('que!id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!lengua!nom!nx1!nx2!observ!profesion!situacion!stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin');
$oHash->setcamposNo($campos_chk);
$a_camposHidden = array(
		'campos_chk'=>$campos_chk,
		'obj_pau'=>$Qobj_pau,
		'id_nom'=>$Qid_nom
		);
$oHash->setArraycamposHidden($a_camposHidden);


$a_parametros = array('pau'=>'p','id_nom'=>$Qid_nom,'obj_pau'=>$Qobj_pau); 
$gohome=web\Hash::link('apps/personas/controller/home_persona.php?'.http_build_query($a_parametros));
$a_parametros = array('pau'=>'p','id_pau'=>$Qid_nom,'obj_pau'=>$Qobj_pau); 
$godossiers=web\Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query($a_parametros));

$titulo = $oPersona->getNombreApellidos();
	

$a_campos = ['obj_txt' => $obj,
			'obj' => $oPersona,
			'oPosicion' => $oPosicion,
			'pau'=>'p',
			'id_pau'=>$Qid_nom,
			'Qobj_pau'=>$Qobj_pau,
			'gohome' => $gohome,
			'godossiers' => $godossiers,
			'ir_a_traslado' => $ir_a_traslado,
			'titulo' => $titulo,
			'oHash' => $oHash,
			'id_nom' => $Qid_nom,
			'id_tabla' => $id_tabla,
			'dl' => $dl,
			'sacd' => $sacd,
			'trato' => $trato,
			'nom' => $nom,
			'nx1' => $nx1,
			'apellido1' => $apellido1,
			'nx2' => $nx2,
			'apellido2' => $apellido2,
			'f_nacimiento' => $f_nacimiento,
			'lengua' => $lengua,
			'situacion ' => $situacion ,
			'f_situacion' => $f_situacion,
			'apel_fam' => $apel_fam,
			'inc' => $inc,
			'f_inc' => $f_inc,
			'stgr' => $stgr,
			'profesion' => $profesion,
			'eap' => $eap,
			'observ' => $observ,
			'lugar_nacimiento' => $lugar_nacimiento,
			'id_ctr' => $id_ctr,
			'nom_ctr' => $nom_ctr,
			'edad' => $edad,
			'profesor_stgr' => $profesor_stgr,
			'oDesplCentro' => $oDesplCentroDl,
			'oDesplSituacion' => $oDesplSituacion,
			'botones' => $botones,
			];

$oView = new core\View('personas\controller');
echo $oView->render($presentacion,$a_campos);