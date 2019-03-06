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

$Qnuevo = (integer) filter_input(INPUT_POST, 'nuevo'); // 0 -> existe, 1->nuevo
$Qobj_pau = (string) filter_input(INPUT_POST, 'obj_pau');

$obj = 'personas\\model\\entity\\'.$Qobj_pau;
	
$oPosicion->recordar();

if (!empty($Qnuevo)) {
    $oF_hoy = new web\DateTimeLocal();
	$Qapellido1 = (string) filter_input(INPUT_POST, 'apellido1');
	// para los acentos
	$Qapellido1 = urldecode($Qapellido1);
	$oPersona = new $obj;
	$cDatosCampo = $oPersona->getDatosCampos();
	$oDbl = $oPersona->getoDbl();
	foreach ($cDatosCampo as $oDatosCampo) {
		$camp = $oDatosCampo->getNom_camp();
		$valor_predeterminado=$oDatosCampo->datos_campo($oDbl,'valor');
		$a_campos[$camp] = $valor_predeterminado;
	}
	$oPersona->setApellido1($Qapellido1);
	$oPersona->setF_situacion($oF_hoy);
	$id_tabla = (string) filter_input(INPUT_POST, 'tabla');
	$stgr = '';
	$dl = '';
	$nom_ctr = '';
	$id_ctr = '';
	$Qid_nom = '';
	$gohome = '';
	$godossiers = '';
	$ir_a_traslado = '';
	$titulo = '';
} else {
	$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	if (!empty($a_sel)) { //vengo de un checkbox
	    $Qid_nom = (integer) strtok($a_sel[0],"#");
		$id_tabla= (string) strtok("#");
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

	$oPersona = new $obj($Qid_nom);
	
	$id_tabla = $oPersona->getId_tabla();
	$dl = $oPersona->getDl();
	$stgr = $oPersona->getStgr();
	// los de paso no tienen ctr
	if (method_exists($oPersona, "getId_ctr")) {
		$id_ctr = $oPersona->getId_ctr();
	} else {
		$id_ctr = '';
	}
//	// para los de paso
//	if (method_exists($oPersona, "getEdad")) {
//		$edad = $oPersona->getEdad();
//	} else {
//		$edad = '';
//	}
//	if (method_exists($oPersona, "getProfesor_stgr")) {
//		$profesor_stgr = $oPersona->getProfesor_stgr();
//	} else {
//		$profesor_stgr = '';
//	}
	// para el ctr hay que buscar el nombre
	if (!empty($id_ctr)) {
		if (core\ConfigGlobal::mi_dele() === core\ConfigGlobal::mi_region()) {
			$oCentroDl = new ubis\Centro($id_ctr);
		} else {
			$oCentroDl = new ubis\CentroDl($id_ctr);
		}
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

if (empty($Qnuevo)) {
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

$GesLocales = new usuarios\model\entity\GestorLocal();
$oDesplLengua = $GesLocales->getListaIdiomas();
$oDesplLengua->setNombre("lengua");
$oDesplLengua->setOpcion_sel($oPersona->getLengua());

//posibles valores de stgr
$tipos= array (  "n"=> _("no cursa est."),
				"b"=> _("bienio"),
				"c1"=>  _("cuadrienio año I"),
				"c2"=> _("cuadrienio año II-IV"),
				"r"=> _("repaso"),
				);

$oDesplStgr = new web\Desplegable();
$oDesplStgr->setNombre('stgr');
$oDesplStgr->setOpciones($tipos);
$oDesplStgr->setOpcion_sel($stgr);
$oDesplStgr->setBlanco(true);

$oHash = new web\Hash();
$campos_chk = 'sacd';
$camposForm = 'que!id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!lengua!nom!nx1!nx2!observ!profesion!situacion!stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin';

//Para la presentacion "de_paso" los campos un poco distintos:
if ($Qobj_pau == 'PersonaEx') {
	$campos_chk = 'sacd!profesor_stgr';
	$camposForm = 'que!id_tabla!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!lugar_nacimiento!edad!f_situacion!inc!lengua!nom!nx1!nx2!observ!profesion!situacion!stgr!trato';
}

$oHash->setcamposForm($camposForm);
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
			'nuevo'=>$Qnuevo,
			'gohome' => $gohome,
			'godossiers' => $godossiers,
			'ir_a_traslado' => $ir_a_traslado,
			'titulo' => $titulo,
			'oHash' => $oHash,
			'id_nom' => $Qid_nom,
			'id_tabla' => $id_tabla,
			'dl' => $dl,
			'id_ctr' => $id_ctr,
			'nom_ctr' => $nom_ctr,
			'oDesplCentro' => $oDesplCentroDl,
			'oDesplSituacion' => $oDesplSituacion,
			'oDesplLengua' => $oDesplLengua,
			'oDesplStgr' => $oDesplStgr,
			'botones' => $botones,
			];

$oView = new core\View('personas\controller');
echo $oView->render($presentacion,$a_campos);