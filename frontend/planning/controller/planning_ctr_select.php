<?php

namespace frontend\planning\controller;

use frontend\planning\support\PlanningRenderer;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

/**
 * Planning (calendario) de las personas de un centro (o grupo de
 * centros), filtrado por periodo y tipo de persona (n, agd, s).
 *
 * Migrado desde `apps/planning/controller/planning_ctr_select.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once __DIR__ . '/../helpers/planning_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$oPeriodo = Periodo::conCalendarioDesdeBackend();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicio_iso = $oPeriodo->getF_ini_iso();
$fin_iso = $oPeriodo->getF_fin_iso();
$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();
$inicio_local = $oIniPlanning->getFromLocal();

$Qdd = 3;
$mod = 0;
$nueva = 0;
$doble = $Qmodelo !== 2 ? 1 : 0;
$interval = (int)$oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) {
    $doble = 0;
}

$Qsacd = (string)filter_input(INPUT_POST, 'sacd');
$Qctr = (string)filter_input(INPUT_POST, 'ctr');
$Qtodos_n = (string)filter_input(INPUT_POST, 'todos_n');
$Qtodos_agd = (string)filter_input(INPUT_POST, 'todos_agd');
$Qtodos_s = (string)filter_input(INPUT_POST, 'todos_s');

$payload = [
    'modelo' => $Qmodelo,
    'tipo' => $Qtipo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'sacd' => $Qsacd,
    'ctr' => $Qctr,
    'todos_n' => $Qtodos_n,
    'todos_agd' => $Qtodos_agd,
    'todos_s' => $Qtodos_s,
];
$apiData = PostRequest::getDataFromUrl('/src/planning/planning_ctr_select_data', $payload);
$ctrSelect = planning_ctr_select_from_payload($apiData);
$msg_txt = $ctrSelect['msg_txt'];
$cabecera_title = $ctrSelect['cabecera_title'];
$a_actividades2 = $ctrSelect['a_actividades2'];

$aGoBack = [
    'modelo' => $Qmodelo,
    'tipo' => $Qtipo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'sacd' => $Qsacd,
    'ctr' => $Qctr,
    'todos_n' => $Qtodos_n,
    'todos_agd' => $Qtodos_agd,
    'todos_s' => $Qtodos_s,
];
$oPosicion->setParametros($aGoBack, 1);

$goLeyenda = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

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

$a_campos = [
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'msg_txt' => $msg_txt,
    'cabecera_title' => $cabecera_title,
    'a_actividades2' => $a_actividades2,
    'goLeyenda' => $goLeyenda,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_ctr_select.phtml', $a_campos);
