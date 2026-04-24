<?php

namespace frontend\planning\controller;

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Desplegable;
use web\Hash;
use web\PeriodoQue;
use web\Posicion;

/**
 * Formulario de filtros para el planning por zonas (sacd). Calcula el
 * subconjunto de zonas visible segun el rol y prepara el desplegable.
 *
 * Migrado desde `apps/planning/controller/planning_zones_que.php`
 * (slice 3 de la migracion del modulo planning). La plantilla se ha
 * reescrito como PHTML; ya no se usa Twig.
 */
require_once("frontend/shared/global_header_front.inc");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */
$oPosicion->recordar();

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '' && $stack !== null) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack((int)$stack)) {
            $oPosicion2->olvidar((int)$stack);
        }
    }
}

$Qmodelo = (int)filter_input(INPUT_POST, 'modo');
$Qmodelo = empty($Qmodelo) ? 1 : $Qmodelo;

$Qyear = (int)filter_input(INPUT_POST, 'year');
$year = empty($Qyear) ? (int)date('Y') : $Qyear;
$Qtrimestre = (int)filter_input(INPUT_POST, 'trimestre');

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qactividad = (string)filter_input(INPUT_POST, 'actividad');
$Qpropuesta = true;

$checksTrim = [
    1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '',
    101 => '', 102 => '', 103 => '', 104 => '', 105 => '', 106 => '',
    107 => '', 108 => '', 109 => '', 110 => '', 111 => '', 112 => '',
];
if (empty($Qtrimestre)) {
    $mes = (int)date('m');
    if ($mes < 4) {
        $checksTrim[1] = 'checked';
    } elseif ($mes < 7) {
        $checksTrim[2] = 'checked';
    } elseif ($mes > 8 && $mes < 10) {
        $checksTrim[3] = 'checked';
    } elseif ($mes > 9) {
        $checksTrim[4] = 'checked';
    }
} elseif (array_key_exists($Qtrimestre, $checksTrim)) {
    $checksTrim[$Qtrimestre] = 'checked';
}

$id_nom_jefe = null;
$UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$id_role = $oMiUsuario->getId_role();

$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$aRoles = $RoleRepository->getArrayRoles();
if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
    if (!$_SESSION['oConfig']->is_jefeCalendario()) {
        $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
        if (empty($id_nom_jefe)) {
            exit(_("No tiene permiso para ver esta página"));
        }
    }
}

$ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
$aOpciones = $ZonaRepository->getArrayZonas($id_nom_jefe);
$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(false);
$oDesplZonas->setBlanco(0);
$algo = $oDesplZonas->options();
if (strlen($algo) < 1) {
    exit(_("No tiene permiso para ver esta página"));
}
if (!empty($Qid_zona)) {
    $oDesplZonas->setOpcion_sel($Qid_zona);
}

$is_jefeCalendario = $_SESSION['oConfig']->is_jefeCalendario();
$url = 'frontend/planning/controller/planning_zones_select.php';

$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setArraycamposHidden([
    'modelo' => $Qmodelo,
    'propuesta' => $Qpropuesta,
]);
$oHash->setCamposForm('actividad!year!id_zona!trimestre');
$oHash->setCamposNo('modelo');

$oFormAny = new PeriodoQue();
$any = (int)date('Y');
$aOpcionesAnys = [
    $any - 4 => $any - 4,
    $any - 3 => $any - 3,
    $any - 2 => $any - 2,
    $any - 1 => $any - 1,
    $any => $any,
    $any + 1 => $any + 1,
];
$oFormAny->setPosiblesAnys($aOpcionesAnys);
$oFormAny->setDesplAnysOpcion_sel($year);

$chk_actividad_si = ($Qactividad !== '' && $Qactividad === 'no') ? '' : 'checked';
$chk_actividad_no = ($Qactividad === 'no') ? 'checked' : '';

$a_campos = [
    'oHash' => $oHash,
    'url' => $url,
    'is_jefeCalendario' => $is_jefeCalendario,
    'oDesplZonas' => $oDesplZonas,
    'year' => $year,
    'oFormAny' => $oFormAny,
    'chk_actividad_si' => $chk_actividad_si,
    'chk_actividad_no' => $chk_actividad_no,
    'checksTrim' => $checksTrim,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_zones_que.phtml', $a_campos);
