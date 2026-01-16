<?php

use core\ViewTwig;
use encargossacd\model\DesplCentros;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_enc = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
}


/**
 * Funciones más comunes de la aplicación
 * include_once(ConfigGlobal::$dir_programas.'/func_web.php');
 * include_once("func_tareas.php");
 */


function filtro($id_ubi)
{
    $id_ubi_str = (string)$id_ubi;
    if ((int)$id_ubi_str[0] === 2) {
        $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $oCentro = $CentroEllasRepository->findById($id_ubi);
    } else {
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($id_ubi);
    }
    $tipo_ubi = $oCentro->getTipo_ubi();
    $tipo_ctr = $oCentro->getTipo_ctr();

    if ($tipo_ubi === "ctrsf") {
        $filtro_ctr = EncargoGrupo::CENTRO_SF;
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
                $filtro_ctr = EncargoGrupo::CENTRO_SV;
                break;
            case "ss":
                $filtro_ctr = EncargoGrupo::CENTRO_SSSC;
                break;
            case "igloc":
            case "igl":
                $filtro_ctr = EncargoGrupo::IGL;
                break;
            case "cgioc":
            case "oc":
                $filtro_ctr = EncargoGrupo::CGI;
                break;
            default:
                $filtro_ctr = 0;
                echo "tipo_ctr: $tipo_ctr<br>";
        }
    }
    return $filtro_ctr;
}

// -------------------------------------------------------------

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qid_tipo_enc = (integer)filter_input(INPUT_POST, 'id_tipo_enc');

$Qgrupo = (string)filter_input(INPUT_POST, 'grupo');
$Qfiltro_ctr = (string)filter_input(INPUT_POST, 'filtro_ctr');
$Qdesc_enc = (string)filter_input(INPUT_POST, 'desc_enc');
$Qdesc_lugar = (string)filter_input(INPUT_POST, 'desc_lugar');
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

if ($Qgrupo == 8) {
    $Qfiltro_ctr = 8;
}

$idioma_enc = '';

$EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
$EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);

if (empty($Qque) || $Qque === 'editar') { //significa que no es nuevo
    if (!empty($_POST['sel'])) { //vengo de un checkbox
        $Qid_enc = strtok($_POST['sel'][0], "#");
    }

    $oEncargo = $EncargoRepository->findById($Qid_enc);
    $Qid_ubi = $oEncargo->getId_ubi();
    $Qid_zona = $oEncargo->getId_zona();
    $Qid_tipo_enc = $oEncargo->getId_tipo_enc();
    $Qdesc_enc = $oEncargo->getDesc_enc();
    $Qdesc_lugar = $oEncargo->getDesc_lugar();
    $idioma_enc = $oEncargo->getIdioma_enc();

    $Qfiltro_ctr = $oEncargo->getSf_sv();
    if (empty($Qfiltro_ctr) && !empty($Qid_ubi)) {
        $Qfiltro_ctr = filtro($Qid_ubi);
    }
}

if (!empty($Qid_tipo_enc)) {
    $tipo = $EncargoTipoRepository->encargo_de_tipo($Qid_tipo_enc);
    $Qgrupo = $tipo['grupo'];
    //$nom_tipo=$tipo['nom_tipo'];
} else {
    $Qid_tipo_enc = $EncargoTipoRepository->id_tipo_encargo($Qgrupo, '...');
}

$ee = $EncargoTipoRepository->encargo_de_tipo($Qid_tipo_enc);
// desplegable de grupos
if (substr($Qid_tipo_enc, 0, 1) === '.') {
    $grupo_posibles = $ee['grupo'];
} else {
    $Qgrupo = substr($Qid_tipo_enc, 0, 1);
    $aux = '....'; //Que siempre salgan todas las opciones
    $ee_grupo = $EncargoTipoRepository->encargo_de_tipo($aux);
    $grupo_posibles = $ee_grupo['grupo'];
}
$oDesplGrupos = new Desplegable();
$oDesplGrupos->setNombre('grupo');
$oDesplGrupos->setOpciones($grupo_posibles);
$oDesplGrupos->setOpcion_sel($Qgrupo);
$oDesplGrupos->setBlanco(1);
$oDesplGrupos->setAction("fnjs_lst_tipo_enc();");

if (!empty($Qgrupo)) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_tipo_enc'] = '^' . $Qgrupo;
    $aOperador['id_tipo_enc'] = '~';
    $cEncargoTipos = $EncargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

    // desplegable de nom_tipo
    $posibles_encargo_tipo = [];
    foreach ($cEncargoTipos as $oEncargoTipo) {
        $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
    }
    $oDesplNoms = new Desplegable();
    $oDesplNoms->setNombre('id_tipo_enc');
    $oDesplNoms->setOpciones($posibles_encargo_tipo);
    $oDesplNoms->setOpcion_sel($Qid_tipo_enc);
    $oDesplNoms->setBlanco('t');

} else {
    $oDesplNoms = new Desplegable();
    $oDesplNoms->setOpciones([]);
}

$opciones = $EncargoTipoRepository->getArraySeccion();
$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones);
$oDesplGrupoCtrs->setOpcion_sel($Qfiltro_ctr);
$oDesplGrupoCtrs->setBlanco(1);
$oDesplGrupoCtrs->setAction("fnjs_lista_ctrs();");

$ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
$aOpciones = $ZonaRepository->getArrayZonas();
$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(FALSE);$oDesplZonas->setNombre('id_zona_sel');
$oDesplZonas->setAction('fnjs_lista_ctrs_por_zona()');
if (!empty($Qid_zona)) {
    $oDesplZonas->setOpcion_sel($Qid_zona);
}

$oGrupoCtr = new DesplCentros();
$oGrupoCtr->setIdZona($Qid_zona);
$oDesplCtrs = $oGrupoCtr->getDesplPorFiltro($Qfiltro_ctr);
$oDesplCtrs->setNombre('lst_ctrs');
$oDesplCtrs->setAction('fnjs_ver_ficha()');
if (!empty($Qid_ubi)) {
    $oDesplCtrs->setOpcion_sel($Qid_ubi);
}

$LocaleRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $LocaleRepository->getArrayLocales();
$oDesplIdiomas = new Desplegable("idioma_enc", $a_locales, $idioma_enc, true);

$url_actualizar = 'apps/encargossacd/controller/encargo_ver.php';
$oHashAct = new Hash();
$aCamposHidden = [
    'que' => $Qque,
    'id_enc' => $Qid_enc,
    'id_zona' => $Qid_zona,
];
$oHashAct->setUrl($url_actualizar);
if ($Qque === 'nuevo') {
    $campos_form = 'desc_enc!desc_lugar!filtro_ctr!grupo!id_tipo_enc!id_zona!idioma_enc';
} else {
    $campos_form = 'desc_enc!desc_lugar!filtro_ctr!grupo!id_tipo_enc!id_zona!idioma_enc';
}
$oHashAct->setCamposForm($campos_form);
$oHashAct->setcamposNo('id_zona!id_zona_sel!lst_ctrs!refresh');
$oHashAct->setArrayCamposHidden($aCamposHidden);

$url_zona = 'apps/encargossacd/controller/zonas_get_select.php';
$oHashZona = new Hash();
$oHashZona->setUrl($url_zona);
$oHashZona->setCamposForm('id_zona');
$h_zona = $oHashZona->linkSinVal();

$url_ctr = 'apps/encargossacd/controller/ctr_get_select.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setCamposForm('filtro_ctr');
$h_ctr = $oHashCtr->linkSinVal();
$oHashCtr->setCamposForm('id_zona');
$h_ctr_zona = $oHashCtr->linkSinVal();

$url_lst = 'apps/encargossacd/controller/encargo_ajax.php';
$oHashLst = new Hash();
$oHashLst->setUrl($url_lst);
$oHashLst->setCamposForm('que!grupo');
$h_lst = $oHashLst->linkSinVal();

if ($Qque === 'nuevo') {
    $txt_btn = _("crear encargo");
} else {
    $txt_btn = _("guardar encargo");
}

$a_campos = ['oPosicion' => $oPosicion,
    'url_actualizar' => $url_actualizar,
    'url_zona' => $url_zona,
    'h_zona' => $h_zona,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'h_ctr_zona' => $h_ctr_zona,
    'url_lst' => $url_lst,
    'h_lst' => $h_lst,
    'oHash' => $oHashAct,
    'id_enc' => $Qid_enc,
    'id_tipo_enc' => $Qid_tipo_enc,
    'oDesplGrupos' => $oDesplGrupos,
    'oDesplNoms' => $oDesplNoms,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
    'oDesplZonas' => $oDesplZonas,
    'oDesplCtrs' => $oDesplCtrs,
    'oDesplIdiomas' => $oDesplIdiomas,
    'desc_enc' => $Qdesc_enc,
    'desc_lugar' => $Qdesc_lugar,
    'que' > $Qque,
    'txt_btn' => $txt_btn,
    'grupo' => $Qgrupo,
];

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('encargo_ver.html.twig', $a_campos);