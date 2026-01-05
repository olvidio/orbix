<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use web\Hash;
use function core\urlsafe_b64encode;

/**
 * Esta página muestra un formulario para modificar los datos de un acta.
 *
 *
 * @package    delegacion
 * @subpackage    est
 * @author    Daniel Serrabou
 * @since        14/10/03.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$f_acta = '';
$libro = '';
$pagina = '';
$linea = '';
$lugar = '';
$observ = '';

// Si notas=(nuevo|acta), quiere decir que estoy en un include de actividadestudios/controller/acta_notas
$notas = empty($notas) ? '' : $notas;
$permiso = empty($permiso) ? 3 : $permiso;

// Si soy region del stgr, no puedo modificar actas (que lo hagan las dl).
if (ConfigGlobal::mi_ambito() === 'rstgr') {
    $permiso = 0;
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');

$Qsa_actas = (string)filter_input(INPUT_POST, 'sa_actas');
$Qa_actas = json_decode(core\urlsafe_b64decode($Qsa_actas));
$Qacta = (string)filter_input(INPUT_POST, 'acta');
$Qnotas = (string)filter_input(INPUT_POST, 'notas');

if (empty($notas) && empty($Qnotas)) {
    $oPosicion->recordar();
}

//$acta=urldecode($acta);
//últimos
$any = date('y');
$mi_dele = ConfigGlobal::mi_delef();

/* TODO Aclararse. Ahora pongo crAcse...
// para las regiones no es 'crA', sino 'A'.
$a_reg = explode('-',$_SESSION['session_auth']['esquema']);
$dlEsquema = substr($a_reg[1],0,-1); // quito la v o la f.
$dl = ($dlEsquema=='cr')? ConfigGlobal::mi_region() : $mi_dele;
*/
$dl = $mi_dele;

$ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
$ult_lib = ''; //$GesActas->getUltimoLibro();
$ult_pag = ''; // $GesActas->getUltimaPagina($ult_lib);
$ult_lin =  ''; //$GesActas->getUltimaLinea($ult_lib);
$ult_acta =  $ActaRepository->getUltimaActa($any, $dl);
$acta_new = '';
$pdf = '';

$obj = 'notas\\model\\entity\\ActaDl';

//Distingo la procedencia.
if (empty($notas) && empty($Qnotas)) {
    // No estoy dentro de la pagina de acta_notas
    if (!empty($a_sel)) {
        //vengo de un checkbox y no estoy en la página de acta_notas ($notas).
        $acta_actual = urldecode(strtok($a_sel[0], "#"));
    } else {
        // si vengo por un link en el nombre del acta, sólo tengo el acta encoded
        $acta_actual = urldecode($Qacta);
    }
    $a_actas = array($acta_actual);
} else {
    // Dentro de la página acta_notas.
    if (isset($cActas) && is_array($cActas)) {
        $a_actas = [];
        foreach ($cActas as $oActa) {
            $a_actas[] = $oActa->getActa();
        }
        //por defecto la primera
        $acta_actual = empty($a_actas[0]) ? '' : $a_actas[0];
    } elseif (!empty ($Qa_actas)) {  // Estoy en la pagina notas y cambio el div de actas
        $a_actas = $Qa_actas;
        $acta_actual = $Qacta;
        $notas = $Qnotas;
    }
}

if ($notas !== 'nuevo' && $Qmod !== 'nueva' && !empty($acta_actual)) { //significa que no es nuevo
    if (!empty($Qacta) && !empty($notas)) { // vengo de actualizar esta pág.
        // estoy actualizando la página
        $id_asignatura_actual = (integer)filter_input(INPUT_POST, 'id_asignatura_actual');
        $id_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $f_acta = (string)filter_input(INPUT_POST, 'f_acta');
        $libro = (string)filter_input(INPUT_POST, 'libro');
        $pagina = (integer)filter_input(INPUT_POST, 'pagina');
        $linea = (integer)filter_input(INPUT_POST, 'linea');
        $lugar = (string)filter_input(INPUT_POST, 'lugar');
        $observ = (string)filter_input(INPUT_POST, 'observ');
        $permiso = (integer)filter_input(INPUT_POST, 'permiso');
    } else {
        $oActa = $ActaRepository->findById($acta_actual);
        $id_asignatura = $oActa->getId_asignatura();
        $id_activ = $oActa->getId_activ();
        $f_acta = $oActa->getF_acta()->getFromLocal();
        $libro = $oActa->getLibro();
        $pagina = $oActa->getPagina();
        $linea = $oActa->getLinea();
        $lugar = $oActa->getLugar();
        $observ = $oActa->getObserv();
        $id_asignatura_actual = $id_asignatura;
        $pdf = $oActa->getpdf();
    }
} else {
    //busco la última acta (para ayudar)
    //
    //echo "aa: $query_acta<br>";
    $num_acta = $ult_acta + 1;
    $ult_acta = "$dl {$ult_acta}/{$any}";
    $acta_new = "$dl {$num_acta}/{$any}";

    if ($notas === "nuevo") { //vengo de un ca
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
        $id_activ = empty($id_activ) ? $Qid_activ : $id_activ;
        $Qid_asignatura = (string)filter_input(INPUT_POST, 'id_asignatura');
        $id_asignatura_actual = empty($id_asignatura) ? $Qid_asignatura : $id_asignatura;
        // Busco al profesor como examinador principal.
        $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($id_activ, $id_asignatura_actual);
        $id_profesor = $oActividadAsignatura->getId_profesor();
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $oPersonaDl = $PersonaDlRepository->findById($id_profesor);
        $ap_nom = $oPersonaDl->getTituloNombre();
        $examinador = $ap_nom;
    } else { // estoy actualizando la página
        if (!empty($a_sel) && !empty($notas)) { //vengo de un checkbox y estoy en la página de acta_notas ($notas).
            $id_activ = (integer)strtok($a_sel[0], '#');
            $id_asignatura = (integer)strtok('#');
            $cActas = $ActaRepository->getActas(array('id_activ' => $id_activ, 'id_asignatura' => $id_asignatura));
            $oActa = $cActas[0];
            $id_asignatura = $oActa->getId_asignatura();
            $id_activ = $oActa->getId_activ();
            $f_acta = $oActa->getF_acta()->getFromLocal();
            $libro = $oActa->getLibro();
            $pagina = $oActa->getPagina();
            $linea = $oActa->getLinea();
            $lugar = $oActa->getLugar();
            $observ = $oActa->getObserv();
            $id_asignatura_actual = $id_asignatura;
        } else {
            $id_asignatura_actual = '';
        }
    }
}

if (!empty($ult_lib)) {
    $ult_lib = sprintf(_("(último= %s)"), $ult_lib);
}
if (!empty($ult_pag)) {
    $ult_pag = sprintf(_("(última= %s)"), $ult_pag);
}
if (!empty($ult_lin)) {
    $ult_lin = sprintf(_("(última= %s)"), $ult_lin);
}
if (!empty($ult_acta)) {
    $ult_acta = sprintf(_("(última= %s)"), $ult_acta);
}

if (!empty($acta_actual)) {
    // Si es cr, se mira en todas:
    if (ConfigGlobal::mi_ambito() === 'rstgr') {
        $repoActaTribunal = $GLOBALS['container']->get(ActaTribunalRepositoryInterface::class);
    } else {
        $repoActaTribunal = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
    }
    $cTribunal = $repoActaTribunal->getActasTribunales(array('acta' => $acta_actual, '_ordre' => 'orden'));
} else {
    $cTribunal = [];
}

$nombre_asignatura = '';
if (!empty($id_asignatura_actual)) {
    $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
    $cAsignatura = $AsignaturaRepository->getAsignaturas(['id_asignatura' => $id_asignatura_actual]);
    if (!empty($cAsignatura)) {
        $oAsignatura = $cAsignatura[0];
        $nombre_asignatura = $oAsignatura->getNombre_asignatura();
    }
}

$oHashActa = new Hash();
$sCamposForm = 'libro!linea!pagina!lugar!observ!id_asignatura!f_acta!acta!name_asignatura';
if ($Qmod === 'nueva' || $notas === "nuevo") {
    $sCamposForm .= '!acta';
    $sCamposForm .= '!f_acta';
}
if (!empty($cTribunal)) {
    //$sCamposForm .= '!item';
    $sCamposForm .= '!examinadores';
}
$oHashActa->setCamposForm($sCamposForm);
$oHashActa->setCamposNo('go_to!examinadores!notas!refresh');
$a_camposHidden = [];
if ($Qmod === 'nueva' || $notas === "nuevo") {
    $a_camposHidden['mod'] = 'nueva';
    if (empty($id_activ)) {
        echo _("no se guardará el ca/cv donde se cursó la asignatura");
    } else {
        $a_camposHidden['id_activ'] = $id_activ;
    }
} else {
//	$a_camposHidden['acta'] = $acta;
    $a_camposHidden['mod'] = '';
    $a_camposHidden['id_activ'] = $id_activ;
    $a_camposHidden['sa_actas'] = urlsafe_b64encode(json_encode($a_actas), JSON_THROW_ON_ERROR);
    $a_camposHidden['notas'] = $notas;
}
$oHashActa->setArraycamposHidden($a_camposHidden);

$oHashActaPdf = new Hash();
$oHashActaPdf->setCamposForm('acta_pdf');
$oHashActaPdf->setCamposNo('acta_pdf');
//cambio el nombre, porque tiene el mismo id en el otro formnulario
$oHashActaPdf->setArrayCamposHidden(['acta_num' => $acta_actual]);

$titulo = strtoupper(_("datos del acta"));

$examinadores = [];
if (!empty($cTribunal)) {
    foreach ($cTribunal as $oActaTribunal) {
        $examinador = $oActaTribunal->getExaminador();
        $examinadores[] = $examinador;
    }
}

$url_ajax = ConfigGlobal::getWeb() . '/apps/notas/controller/acta_ajax.php';
$oHashLink = new Hash();
$oHashLink->setUrl($url_ajax);
$oHashLink->setCamposForm('que!search');
$h_ajax = $oHashLink->getParamAjaxEnArray();

if (empty($pdf)) {
    $readonly = '';
    $url_download = '';
    $url_delete = '';
} else {
    $readonly = 'readonly';
    $url_download = Hash::link('apps/notas/controller/acta_pdf_download.php?' . http_build_query(['key' => $acta_actual]));
    $url_delete = 'apps/notas/controller/acta_pdf_delete.php';
}
$oHashActaDelete = new Hash();
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashActaDelete->setArrayCamposHidden(['acta_num' => $acta_actual]);
$h_delete = $oHashActaDelete->getParamAjax();

// Solo cr, puede eliminar un acta firmada:
if (ConfigGlobal::mi_ambito() === 'rstgr' || ConfigGlobal::mi_ambito() === 'r') {
    $soy_rstgr = TRUE;
} else {
    $soy_rstgr = FALSE;
}

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'notas' => $notas,
    'mod' => $Qmod,
    'oHashActa' => $oHashActa,
    'oHashActaPdf' => $oHashActaPdf,
    'titulo' => $titulo,
    'acta_actual' => $acta_actual,
    'acta_new' => $acta_new,
    'ult_acta' => $ult_acta,
    'f_acta' => $f_acta,
    'libro' => $libro,
    'ult_lib' => $ult_lib,
    'pagina' => $pagina,
    'ult_pag' => $ult_pag,
    'linea' => $linea,
    'ult_lin' => $ult_lin,
    'lugar' => $lugar,
    'observ' => $observ,
    'url_ajax' => $url_ajax,
    'h_ajax' => $h_ajax,
    'id_asignatura' => $id_asignatura_actual,
    'nombre_asignatura' => $nombre_asignatura,
    'examinadores' => $examinadores,
    'a_actas' => $a_actas,
    'permiso' => $permiso,
    'readonly' => $readonly,
    'url_download' => $url_download,
    'url_delete' => $url_delete,
    'h_delete' => $h_delete,
    'soy_rstgr' => $soy_rstgr,
];

$oView = new ViewPhtml('notas\controller');
$oView->renderizar('acta_ver.phtml', $a_campos);