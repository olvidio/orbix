<?php
namespace frontend\planning\controller;

use frontend\planning\helpers\PlanningPostInput;
use frontend\planning\helpers\PlanningPayload;
use frontend\shared\config\AppUrlConfig;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Planning (calendario) de las actividades asignadas a un conjunto
 * de personas seleccionadas en `planning_persona_select`.
 *
 * Migrado desde `apps/planning/controller/planning_persona_ver.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = PlanningPostInput::postString('na');
$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$a_sel = PlanningPostInput::collectSelFromPost();
$sSeleccionados = implode(',', $a_sel);

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$navState = ListNavSupport::mergeSelectionIntoReturnParametros([
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'sSeleccionados' => $sSeleccionados,
], $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['sSeleccionados' => $sSeleccionados],
    $navState,
);

ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::mergeSelectionForRecordar([
        'sSeleccionados' => $sSeleccionados,
    ], $Qid_sel, $Qscroll_id),
);

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

$payload = [
    'obj_pau' => $Qobj_pau,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'sSeleccionados' => $sSeleccionados,
];
$apiData = PostRequest::getDataFromUrl('/src/planning/planning_persona_ver_data', $payload);
$a_actividades = PlanningPayload::actividadesMap($apiData['a_actividades'] ?? null);

$estilos = PlanningPayload::calendarioEstilos();
$css = $estilos['css'];

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
    'css' => $css,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_persona_ver.phtml', $a_campos);
