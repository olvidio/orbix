<?php

use core\ConfigGlobal;
use usuarios\model\entity\Usuario;
use web\Hash;
use web\Posicion;
use zonassacd\model\entity\GestorZona;

/**
 * Página que presentará los formularios de los distintos plannings
 * Según sea el submenú seleccionado seleccionará el formulario
 * correspondiente
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo de vuelta y le paso la referencia del stack donde está la información.
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qmodelo = (integer)filter_input(INPUT_POST, 'modo');
$Qmodelo = empty($Qmodelo) ? 1 : $Qmodelo;

$Qyear = (integer)filter_input(INPUT_POST, 'year');
$year = empty($Qyear) ? date("Y") : $Qyear;
$Qtrimestre = (integer)filter_input(INPUT_POST, 'trimestre');

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qactividad = (string)filter_input(INPUT_POST, 'actividad');
//$Qpropuesta = (string)filter_input(INPUT_POST, 'propuesta');
$Qpropuesta = TRUE; // Para que también salgan las actividades en proyecto


$chk_trim1 = '';
$chk_trim2 = '';
$chk_trim3 = '';
$chk_trim4 = '';
$chk_trim5 = '';
$chk_trim6 = '';
$chk_trim_101 = '';
$chk_trim_102 = '';
$chk_trim_103 = '';
$chk_trim_104 = '';
$chk_trim_105 = '';
$chk_trim_106 = '';
$chk_trim_107 = '';
$chk_trim_108 = '';
$chk_trim_109 = '';
$chk_trim_110 = '';
$chk_trim_111 = '';
$chk_trim_112 = '';
if (empty($Qtrimestre)) {
    $mes = date("m");
    if ($mes < 4) {
        $chk_trim1 = 'checked';
    }
    if ($mes > 3 && $mes < 7) {
        $chk_trim2 = 'checked';
    }
    if ($mes > 8 && $mes < 10) {
        $chk_trim3 = 'checked';
    }
    if ($mes > 9 && $mes < 13) {
        $chk_trim4 = 'checked';
    }
} else {
    switch ($Qtrimestre) {
        case 1:
            $chk_trim1 = 'checked';
            break;
        case 2:
            $chk_trim2 = 'checked';
            break;
        case 3:
            $chk_trim3 = 'checked';
            break;
        case 4:
            $chk_trim4 = 'checked';
            break;
        case 5:
            $chk_trim5 = 'checked';
            break;
        case 6:
            $chk_trim6 = 'checked';
            break;
        case 101:
            $chk_trim_101 = 'checked';
            break;
        case 102:
            $chk_trim_102 = 'checked';
            break;
        case 103:
            $chk_trim_103 = 'checked';
            break;
        case 104:
            $chk_trim_104 = 'checked';
            break;
        case 105:
            $chk_trim_105 = 'checked';
            break;
        case 106:
            $chk_trim_106 = 'checked';
            break;
        case 107:
            $chk_trim_107 = 'checked';
            break;
        case 108:
            $chk_trim_108 = 'checked';
            break;
        case 109:
            $chk_trim_109 = 'checked';
            break;
        case 110:
            $chk_trim_110 = 'checked';
            break;
        case 111:
            $chk_trim_111 = 'checked';
            break;
        case 112:
            $chk_trim_112 = 'checked';
            break;
    }
}

$id_nom_jefe = '';
$id_usuario = ConfigGlobal::mi_id_usuario();
$oMiUsuario = new Usuario($id_usuario);

if ($oMiUsuario->isRole('p-sacd')) { //sacd
    if ($_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = '';
    } else {
        $id_nom_jefe = $oMiUsuario->getId_pau();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}


$GesZonas = new GestorZona();
$oDesplZonas = $GesZonas->getListaZonas($id_nom_jefe);
$oDesplZonas->setBlanco(0);
// miro si se tiene opción a ver alguna zona. La opción blanco tiene que ser 0, sino la rta es <option></option>.
$algo = $oDesplZonas->options();
if (strlen($algo) < 1) exit(_("No tiene permiso para ver esta página"));
if (!empty($Qid_zona)) {
    $oDesplZonas->setOpcion_sel($Qid_zona);
}

$perm_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
    $perm_des = TRUE;
}

$is_jefeCalendario = $_SESSION['oConfig']->is_jefeCalendario();

$url = 'apps/zonassacd/controller/planning_zones_crida_calendari.php';

$oHash = new Hash();
$oHash->setUrl($url);
$a_camposHidden = [
    'modelo' => $Qmodelo,
    'propuesta' => $Qpropuesta,
];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm('actividad!year!id_zona!trimestre');
$oHash->setCamposNo('modelo');

$oFormAny = new web\PeriodoQue();
$any = (integer)date('Y');
$aOpcionesAnys[$any - 4] = $any - 4;
$aOpcionesAnys[$any - 3] = $any - 3;
$aOpcionesAnys[$any - 2] = $any - 2;
$aOpcionesAnys[$any - 1] = $any - 1;
$aOpcionesAnys[$any] = $any;
$aOpcionesAnys[$any + 1] = $any + 1;
$oFormAny->setPosiblesAnys($aOpcionesAnys);
$oFormAny->setDesplAnysOpcion_sel($year);

$chk_actividad_no = '';
$chk_actividad_si = 'checked';
if (!empty($Qactividad) && $Qactividad === 'no') {
    $chk_actividad_no = 'checked';
    $chk_actividad_si = '';
}

$a_campos = [
    'oHash' => $oHash,
    'url' => $url,
    'is_jefeCalendario' => $is_jefeCalendario,
    'oDesplZonas' => $oDesplZonas,
    'year' => $year,
    'chk_trim1' => $chk_trim1,
    'chk_trim2' => $chk_trim2,
    'chk_trim3' => $chk_trim3,
    'chk_trim4' => $chk_trim4,
    'chk_trim5' => $chk_trim5,
    'chk_trim6' => $chk_trim6,
    'oFormAny' => $oFormAny,
    'chk_actividad_si' => $chk_actividad_si,
    'chk_actividad_no' => $chk_actividad_no,
    'chk_trim_101' => $chk_trim_101,
    'chk_trim_102' => $chk_trim_102,
    'chk_trim_103' => $chk_trim_103,
    'chk_trim_104' => $chk_trim_104,
    'chk_trim_105' => $chk_trim_105,
    'chk_trim_106' => $chk_trim_106,
    'chk_trim_107' => $chk_trim_107,
    'chk_trim_108' => $chk_trim_108,
    'chk_trim_109' => $chk_trim_109,
    'chk_trim_110' => $chk_trim_110,
    'chk_trim_111' => $chk_trim_111,
    'chk_trim_112' => $chk_trim_112,
];

$oView = new core\ViewTwig('zonassacd/controller');
$oView->renderizar('planning_zones.html.twig', $a_campos);