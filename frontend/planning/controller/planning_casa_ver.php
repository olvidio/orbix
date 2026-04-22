<?php

namespace frontend\planning\controller;

use core\ConfigGlobal;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\model\ViewNewPhtml;
use src\planning\application\ActividadesPorCasasService;
use web\Hash;
use web\Periodo;
use function core\is_true;

/**
 * Planning (calendario) de actividades de un grupo de casas en un
 * periodo dado. Se invoca por AJAX desde `planning_casa_select.phtml`.
 *
 * Migrado desde `apps/planning/controller/planning_casa_ver.php`
 * (slice 2 de la migracion del modulo planning).
 *
 * El controlador recalcula `$a_actividades` a partir de los filtros
 * en `$_POST` (antes venian en base64 desde `planning_casa_select`).
 */
require_once("frontend/shared/global_header_front.inc");
require_once("apps/core/global_object.inc");

$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qcdc_sel = (int)filter_input(INPUT_POST, 'cdc_sel');
$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qsin_activ = (int)filter_input(INPUT_POST, 'sin_activ');
$QsSeleccionados = (string)filter_input(INPUT_POST, 'sSeleccionados');

$aIdCdc = null;
if ($Qcdc_sel === 9 && $QsSeleccionados !== '') {
    $aIdCdc = array_map('trim', explode(',', $QsSeleccionados));
}

$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$oInicio_iso = $oPeriodo->getF_ini();
$oFin_iso = $oPeriodo->getF_fin();
$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();

$Qdd = 3;
$mod = 0;
$nueva = 0;
if (is_true($Qpropuesta_calendario)) {
    $mod = 1;
    $nueva = 1;
}

$doble = $Qmodelo !== 2 ? 1 : 0;
$interval = (int)$oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) {
    $doble = 0;
}

$cabecera_title = ucfirst(_("casas"));
$cabecera = ucfirst(_("calendario de casas"));

[, $a_actividades] = ActividadesPorCasasService::actividadesPorCasas(
    $Qcdc_sel,
    $oIniPlanning,
    $oFinPlanning,
    $Qsin_activ,
    $oFin_iso,
    $oInicio_iso,
    $aIdCdc
);

$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

$css = '';
switch ($Qmodelo) {
    case 2:
    case 1:
        $css = file_get_contents(ConfigGlobal::$dir_estilos . '/calendario.css.php');
        break;
    case 3:
        $css = file_get_contents(ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        break;
}
include_once(ConfigGlobal::$dir_estilos . '/calendario_color_cols.css.php');

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);
$oPlanning->setDd($Qdd);
$oPlanning->setCabecera($cabecera);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setActividades($a_actividades);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);

$a_campos = [
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'cabecera_title' => $cabecera_title,
    'css' => $css,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_casa_ver.phtml', $a_campos);
