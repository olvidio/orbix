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
use frontend\shared\FrontBootstrap;

/**
 * Planning (calendario) de las actividades asignadas a un conjunto
 * de personas seleccionadas en `planning_persona_select`.
 *
 * Migrado desde `apps/planning/controller/planning_persona_ver.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once __DIR__ . '/../helpers/planning_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(($aGoBack ?? list_nav_build_return_parametros_from_post()), list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));


$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = planning_post_string('na');
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

$a_sel = planning_collect_sel_from_post();
$payload = [
    'obj_pau' => $Qobj_pau,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'sSeleccionados' => implode(',', $a_sel),
];
$apiData = PostRequest::getDataFromUrl('/src/planning/planning_persona_ver_data', $payload);
$a_actividades = planning_actividades_map($apiData['a_actividades'] ?? null);

$aGoBack = [
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'id_sel' => planning_post_string('id_sel'),
    'scroll_id' => planning_post_string('scroll_id'),
    'sSeleccionados' => implode(',', $a_sel),
];
$oPosicion->setParametros($aGoBack, 1);

$estilos = planning_calendario_estilos();

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($estilos['colorColumnaUno']);
$oPlanning->setColorColumnaDos($estilos['colorColumnaDos']);
$oPlanning->setColorColumnaDomingo($estilos['colorColumnaDomingo']);
$oPlanning->setTable_border($estilos['table_border']);
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
