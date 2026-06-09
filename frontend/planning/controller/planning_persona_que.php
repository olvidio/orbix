<?php

namespace frontend\planning\controller;

use frontend\planning\support\PeriodoPlanningHelper;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

/**
 * Formulario de filtros para el planning por persona (numerarios, agd,
 * supernumerarios, sacd, de paso...).
 *
 * Migrado desde `apps/planning/controller/planning_persona_que.php`
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

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = (string)filter_input(INPUT_POST, 'na');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

$periodo_txt = PeriodoPlanningHelper::textoPeriodoPorDefecto((int)$_SESSION['oConfig']->getMesFinStgr());
$locale_us = OrbixRuntime::isLocaleUs();

$oHash = new HashFront();
$oHash->setCamposForm('nombre!apellido1!apellido2!centro!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('modelo');
$oHash->setArraycamposHidden([
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
]);

$oFormP = PeriodoPlanningHelper::formPeriodo($Qperiodo, $Qyear, $Qempiezamin, $Qempiezamax);

$personas_txt = match ($Qobj_pau) {
    'PersonaN' => _("numerarios"),
    'PersonaNax' => _("nax"),
    'PersonaAgd' => _("agregados"),
    'PersonaS' => _("supernumerarios"),
    'PersonaSSSC' => _("de la sss+"),
    'PersonaDl' => _("de la dl"),
    'PersonaEx' => _("de paso"),
    'PersonaSacd' => _("sacd"),
    default => exit(sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__)),
};

$urlSelect = 'frontend/planning/controller/planning_persona_select.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'personas_txt' => $personas_txt,
    'locale_us' => $locale_us,
    'urlSelect' => $urlSelect,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_persona_que.phtml', $a_campos);
