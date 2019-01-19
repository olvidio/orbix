<?php 
use encargossacd\model\entity\Encargo;
use ubis\model\entity\CentroEllas;
use ubis\model\entity\CentroDl;
use encargossacd\model\entity\GestorEncargoTipo;
use encargossacd\model\DesplCentros;
use web\Desplegable;
use web\Hash;
use encargossacd\model\EncargoConstants;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_enc = (integer) strtok($a_sel[0],"#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel',$a_sel,1);
    $scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
    $Qid_enc = (integer)  \filter_input(INPUT_POST, 'id_enc');
}


/**
* Funciones más comunes de la aplicación
include_once(ConfigGlobal::$dir_programas.'/func_web.php');  
include_once("func_tareas.php");
*/


function filtro($id_ubi) {
	$id_ubi_str = (string)$id_ubi; 
	if ($id_ubi_str{0} == 2) {
		$oCentro = new CentroEllas($id_ubi);
	} else {
		$oCentro = new CentroDl($id_ubi);
	}
	$tipo_ubi = $oCentro->getTipo_ubi();
	$tipo_ctr = $oCentro->getTipo_ctr();

	if ($tipo_ubi=="ctrsf") {
		$filtro_ctr=2;
	} else {
		switch ($tipo_ctr) {
			case "aj":
			case "am":
			case "nj":
			case "njce":
			case "nm":
			case "sj":
			case "sm":
			case "sjce":
				$filtro_ctr=1;
				break;
			case "ss":
				$filtro_ctr=3;
				break;
			case "igloc":
			case "igl":
				$filtro_ctr=4;
				break;
			case "cgioc":
			case "oc":
				$filtro_ctr=5;
				break;
			default:
				$filtro_ctr = 0;
				echo "tipo_ctr: $tipo_ctr<br>";
		}
	}
	return $filtro_ctr;
}
// -------------------------------------------------------------

$Qmod = (string) \filter_input(INPUT_POST, 'mod');
$Qid_enc = (integer) \filter_input(INPUT_POST, 'id_enc');
$Qid_tipo_enc = (integer) \filter_input(INPUT_POST, 'id_tipo_enc');

$grupo = (string) \filter_input(INPUT_POST, 'grupo');
$nom_tipo = (string) \filter_input(INPUT_POST, 'nom_tipo');
$Qfiltro_ctr = (string) \filter_input(INPUT_POST, 'filtro_ctr');
$Qdesc_enc = (string) \filter_input(INPUT_POST, 'desc_enc');
$Qdesc_lugar = (string) \filter_input(INPUT_POST, 'desc_lugar');

$idioma_enc = '';
if (empty($Qmod) || $Qmod === 'editar') { //significa que no es nuevo
    if (!empty($_POST['sel'])) { //vengo de un checkbox
		$Qid_enc=strtok($_POST['sel'][0],"#");
	}

	$oEncargo = new Encargo($Qid_enc);
	$Qid_ubi = $oEncargo->getId_ubi();
	$Qid_tipo_enc = $oEncargo->getId_tipo_enc();
	$Qdesc_enc = $oEncargo->getDesc_enc();
	$Qdesc_lugar = $oEncargo->getDesc_lugar();
	$idioma_enc = $oEncargo->getIdioma_enc();
	

	if (!empty($Qid_ubi)) {
		$Qfiltro_ctr = filtro($Qid_ubi);
	}
}

$oGesEncargoTipo = new GestorEncargoTipo();
if (!empty($Qid_tipo_enc))  {
	$tipo=$oGesEncargoTipo->encargo_de_tipo($Qid_tipo_enc);
	$grupo=$tipo['grupo'];
	$nom_tipo=$tipo['nom_tipo'];
} else {
	$Qid_tipo_enc=$oGesEncargoTipo->id_tipo_encargo($grupo,$nom_tipo);
}

$ee = $oGesEncargoTipo->encargo_de_tipo($Qid_tipo_enc);
// desplegable de grupos
if (substr($Qid_tipo_enc,0,1)=='.') {
	$grupo_posibles=$ee['grupo'];
} else { 
	$grupo=substr($Qid_tipo_enc,0,1);
	$aux='....'; //Que siempre salgan todas las opciones
	$ee_grupo=$oGesEncargoTipo->encargo_de_tipo($aux);
	$grupo_posibles=$ee_grupo['grupo'];
}
$oDesplGrupos = new Desplegable();
$oDesplGrupos->setNombre('grupo');
$oDesplGrupos->setOpciones($grupo_posibles);
$oDesplGrupos->setOpcion_sel($grupo);
$oDesplGrupos->setBlanco(1);
$oDesplGrupos->setAction("fnjs_generar_id();");

// desplegable de nom_tipo
if (substr($Qid_tipo_enc,1,3)=='...') {
	$nom_tipo_posibles=$ee['nom_tipo'];
} else {
	$aux=substr($Qid_tipo_enc,0,1).'...';
	$ee_nom_tipo=$oGesEncargoTipo->encargo_de_tipo($aux);
	$nom_tipo_posibles=$ee_nom_tipo['nom_tipo'];
}
$oDesplNoms = new Desplegable();
$oDesplNoms->setNombre('nom_tipo');
$oDesplNoms->setOpciones($nom_tipo_posibles);
$oDesplNoms->setOpcion_sel($Qid_tipo_enc);
$oDesplNoms->setBlanco('t');
$oDesplNoms->setAction("fnjs_generar_id();");

$opciones = $oGesEncargoTipo->getArraySeccion();
$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones);
$oDesplGrupoCtrs->setOpcion_sel($Qfiltro_ctr);
$oDesplGrupoCtrs->setBlanco(1);
$oDesplGrupoCtrs->setAction("fnjs_lista_ctrs();");

$oGrupoCtr = new DesplCentros();
$oDesplCtrs = $oGrupoCtr->getDesplPorFiltro($Qfiltro_ctr);
$oDesplCtrs->setNombre('lst_ctrs');
$oDesplCtrs->setAction('fnjs_ver_ficha()');
if (!empty($Qid_ubi)) {
    $oDesplCtrs->setOpcion_sel($Qid_ubi);
}

$idiomas_posibles = EncargoConstants::ARRAY_IDIOMAS;
$oDesplIdiomas = new Desplegable();
$oDesplIdiomas->setNombre('idioma_enc');
$oDesplIdiomas->setOpciones($idiomas_posibles);
$oDesplIdiomas->setOpcion_sel($idioma_enc);
$oDesplIdiomas->setBlanco(1);

$url_actualizar = 'apps/encargossacd/controller/encargo_ver.php';
$oHashAct = new Hash();
$aCamposHidden = [
    'mod' => $Qmod,
    'id_enc' => $Qid_enc,
];
$oHashAct->setUrl($url_actualizar);
if ($Qmod === 'nuevo') {
    $campos_form = 'desc_enc!desc_lugar!filtro_ctr!grupo!id_tipo_enc!idioma_enc!nom_tipo';
} else {
    $campos_form = 'desc_enc!desc_lugar!filtro_ctr!idioma_enc';
}
$oHashAct->setcamposForm($campos_form);
$oHashAct->setcamposNo('lst_ctrs!refresh');
$oHashAct->setArrayCamposHidden($aCamposHidden);

$url_ctr = 'apps/encargossacd/controller/ctr_get_select.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setcamposForm('filtro_ctr!id_ubi');
$h_ctr = $oHashCtr->linkSinVal();

if ($Qmod === 'nuevo') {
    $txt_btn = _("crear encargo");
} else {
    $txt_btn = _("guardar encargo");
}

$a_campos = ['oPosicion' => $oPosicion,
    'url_actualizar' => $url_actualizar,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'oHash' => $oHashAct,
    'id_enc' => $Qid_enc,
    'id_tipo_enc' => $Qid_tipo_enc,
    'oDesplGrupos' => $oDesplGrupos,
    'oDesplNoms' => $oDesplNoms,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
    'oDesplCtrs' => $oDesplCtrs,
    'oDesplIdiomas' => $oDesplIdiomas,
    'desc_enc' => $Qdesc_enc,
    'desc_lugar' => $Qdesc_lugar,
    'mod' > $Qmod,
    'txt_btn' => $txt_btn,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('encargo_ver.html.twig',$a_campos);