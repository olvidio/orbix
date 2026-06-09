<?php

namespace frontend\planning\controller;

use frontend\planning\support\PeriodoPlanningHelper;
use frontend\shared\PostRequest;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\CasasQue;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\FrontBootstrap;

/**
 * Formulario de filtros para el planning por casas (se selecciona el
 * grupo de casas y el periodo).
 *
 * Filtro/modo de `CasasQue` vía `PostRequest` → `/src/planning/planning_casa_que_data`
 * (`PlanningCasaQueFormData`), sin `Role`/`PauType` en este controlador.
 *
 * Migrado desde `apps/planning/controller/planning_casa_que.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
$oPosicion->recordar();

if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack((int)$stack)) {
            $oPosicion2->olvidar((int)$stack);
        }
    }
}

$queCasasPayload = PostRequest::getDataFromUrl('/src/planning/planning_casa_que_data', []);
$queCasasPayload = is_array($queCasasPayload) ? $queCasasPayload : [];
$filtroCasasQue = (array)($queCasasPayload['filtro'] ?? ['active' => true]);
$modoCasasQue = (string)($queCasasPayload['modo_casas'] ?? 'all');
if ($modoCasasQue === '') {
    $modoCasasQue = 'all';
}

$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
$Qsin_activ = (int)filter_input(INPUT_POST, 'sin_activ');
$chk_actividad_no = $Qsin_activ === 1 ? '' : 'checked';
$chk_actividad_si = $Qsin_activ === 1 ? 'checked' : '';

$Qcdc_sel = (int)filter_input(INPUT_POST, 'cdc_sel');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$QsSeleccionados = (string)filter_input(INPUT_POST, 'sSeleccionados');

$oHash = new HashFront();
$oHash->setCamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!modelo!periodo!sin_activ!year');
$oHash->setcamposNo('id_cdc');
$oHash->setArraycamposHidden([
    'propuesta_calendario' => $Qpropuesta_calendario,
]);

$oFormP = PeriodoPlanningHelper::formPeriodo($Qperiodo, $Qyear, $Qempiezamin, $Qempiezamax);

$oForm = new CasasQue();
$oForm->setTitulo(strtoupper_dlb(_("búsqueda de casas cuyo planning interesa")));

$oForm->setFiltroCasas($filtroCasasQue);
$oForm->setCasas($modoCasasQue);
$oForm->setCdcSel($Qcdc_sel);
$oForm->setSeleccionados($QsSeleccionados);

$urlSelect = 'frontend/planning/controller/planning_casa_select.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'propuesta_calendario' => $Qpropuesta_calendario,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'chk_actividad_no' => $chk_actividad_no,
    'chk_actividad_si' => $chk_actividad_si,
    'urlSelect' => $urlSelect,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_casa_que.phtml', $a_campos);
