<?php

namespace frontend\planning\controller;

use frontend\shared\config\AppUrlConfig;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use frontend\shared\web\Posicion;

/**
 * Planning (calendario) de las actividades asignadas a un conjunto
 * de personas seleccionadas en `planning_persona_select`.
 *
 * Migrado desde `apps/planning/controller/planning_persona_ver.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once("frontend/shared/global_header_front.inc");


/** @var Posicion $oPosicion */

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$goLeyenda = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

$oPeriodo = Periodo::conCalendarioDesdeBackend();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();

$Qdd = 3;
$mod = 0;
$nueva = 0;

$print = 0;
if ($Qmodelo === 2) {
    $print = 1;
}
$doble = $print === 1 ? 0 : 1;
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ((int)$interval < 2) {
    $doble = 0;
}

$cabecera_title = ucfirst(_("persona seleccionada"));

$payload = $_POST;
$apiData = PostRequest::getDataFromUrl('/src/planning/planning_persona_ver_data', $payload);
$a_actividades = (array)($apiData['a_actividades'] ?? []);

$aGoBack = [
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'sacd' => '',
    'ctr' => '',
    'todos_n' => '',
    'todos_agd' => '',
    'todos_s' => '',
    'id_ubi' => '',
];
$oPosicion->setParametros($aGoBack, 1);

include_once(OrbixRuntime::dirEstilos() . '/calendario_color_cols.css.php');
include_once(OrbixRuntime::dirEstilos() . '/calendario.css.php');

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setColorColumnaDomingo($colorColumnaDomingo);
$oPlanning->setTable_border($table_border);
$oPlanning->setDd($Qdd);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);
$oPlanning->setActividades($a_actividades);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'cabecera_title' => $cabecera_title,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_persona_ver.phtml', $a_campos);
