<?php

namespace frontend\planning\controller;

use frontend\planning\support\PeriodoPlanningHelper;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

/**
 * Formulario de filtros para el planning por centros (personas de un
 * centro determinado).
 *
 * Migrado desde `apps/planning/controller/planning_ctr_que.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once __DIR__ . '/../helpers/planning_support.php';
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

$periodo_txt = PeriodoPlanningHelper::textoPeriodoPorDefecto(planning_mes_fin_stgr());

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

$Qsacd = (string)filter_input(INPUT_POST, 'sacd');
$Qctr = (string)filter_input(INPUT_POST, 'ctr');
$Qtodos_n = (string)filter_input(INPUT_POST, 'todos_n');
$Qtodos_agd = (string)filter_input(INPUT_POST, 'todos_agd');
$Qtodos_s = (string)filter_input(INPUT_POST, 'todos_s');

$locale_us = OrbixRuntime::isLocaleUs();

$oHash1 = new HashFront();
$oHash1->setCamposForm('sacd!ctr!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash1->setcamposNo('todos_n!todos_agd!todos_s!modelo');
$oHash1->setArraycamposHidden([
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
]);

$oFormP = PeriodoPlanningHelper::formPeriodo($Qperiodo, $Qyear, $Qempiezamin, $Qempiezamax);

$chk_sacd_no = empty($Qsacd) ? 'checked' : '';
$chk_sacd_si = empty($Qsacd) ? '' : 'checked';
$chk_todos_n = empty($Qtodos_n) ? '' : 'checked';
$chk_todos_agd = empty($Qtodos_agd) ? '' : 'checked';
$chk_todos_s = empty($Qtodos_s) ? '' : 'checked';

$urlSelect = 'frontend/planning/controller/planning_ctr_select.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash1' => $oHash1,
    'oFormP' => $oFormP,
    'locale_us' => $locale_us,
    'chk_sacd_no' => $chk_sacd_no,
    'chk_sacd_si' => $chk_sacd_si,
    'ctr' => $Qctr,
    'chk_todos_n' => $chk_todos_n,
    'chk_todos_agd' => $chk_todos_agd,
    'chk_todos_s' => $chk_todos_s,
    'urlSelect' => $urlSelect,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_ctr_que.phtml', $a_campos);
