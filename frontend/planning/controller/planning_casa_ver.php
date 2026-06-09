<?php

namespace frontend\planning\controller;

use frontend\planning\support\PlanningRenderer;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use function frontend\shared\helpers\is_true;
use frontend\shared\FrontBootstrap;

/**
 * Planning (calendario) de actividades de un grupo de casas en un
 * periodo dado. Se invoca por AJAX desde `planning_casa_select.phtml`.
 *
 * Migrado desde `apps/planning/controller/planning_casa_ver.php`
 * (slice 2 de la migracion del modulo planning).
 *
 * Actividades y periodos por casa vía `PostRequest` → `/src/planning/planning_casa_ver_data`
 * (`PlanningCasaVerData`: `ActividadesPorCasasService` + `CasaPeriodosForPlanning`).
 * Las fechas del periodo se envían como `f_ini_iso` / `f_fin_iso` junto al POST del formulario.
 */
require_once __DIR__ . '/../helpers/planning_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
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

$payloadVer = $_POST;
$payloadVer['f_ini_iso'] = (string)$oPeriodo->getF_ini_iso();
$payloadVer['f_fin_iso'] = (string)$oPeriodo->getF_fin_iso();

$d = PostRequest::getDataFromUrl('/src/planning/planning_casa_ver_data', $payloadVer);
$a_actividades = planning_actividades_map($d['a_actividades'] ?? null);
$casa_periodos_por_ubi = planning_casa_periodos_por_ubi($d['casa_periodos_por_ubi'] ?? null);

$goLeyenda = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

$estilos = planning_calendario_estilos();
$css = $estilos['css'];

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($estilos['colorColumnaUno']);
$oPlanning->setColorColumnaDos($estilos['colorColumnaDos']);
$oPlanning->setColorColumnaDomingo($estilos['colorColumnaDomingo']);
$oPlanning->setTable_border($estilos['table_border']);
$oPlanning->setDd($Qdd);
$oPlanning->setCabecera($cabecera);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setActividades($a_actividades);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);
$oPlanning->setCasaPeriodosPorUbi($casa_periodos_por_ubi);

$a_campos = [
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'cabecera_title' => $cabecera_title,
    'css' => $css,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_casa_ver.phtml', $a_campos);
