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
require_once __DIR__ . '/../helpers/planning_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = (string)filter_input(INPUT_POST, 'na');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qnombre = planning_post_string('nombre');
$Qapellido1 = planning_post_string('apellido1');
$Qapellido2 = planning_post_string('apellido2');
$Qcentro = planning_post_string('centro');

if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack((int)$stack)) {
            $Qobj_pau = planning_posicion_string($oPosicion2->getParametro('obj_pau'), $Qobj_pau);
            $Qna = planning_posicion_string($oPosicion2->getParametro('na'), $Qna);
            $Qperiodo = planning_posicion_string($oPosicion2->getParametro('periodo'), $Qperiodo);
            $Qyear = (int)planning_posicion_string($oPosicion2->getParametro('year'), (string)$Qyear);
            $Qempiezamin = planning_posicion_string($oPosicion2->getParametro('empiezamin'), $Qempiezamin);
            $Qempiezamax = planning_posicion_string($oPosicion2->getParametro('empiezamax'), $Qempiezamax);
            $Qnombre = planning_posicion_string($oPosicion2->getParametro('nombre'), $Qnombre);
            $Qapellido1 = planning_posicion_string($oPosicion2->getParametro('apellido1'), $Qapellido1);
            $Qapellido2 = planning_posicion_string($oPosicion2->getParametro('apellido2'), $Qapellido2);
            $Qcentro = planning_posicion_string($oPosicion2->getParametro('centro'), $Qcentro);
            $filtros = planning_filtros_persona_desde_sa_where_encoded(
                planning_posicion_string($oPosicion2->getParametro('saWhere')),
                planning_posicion_string($oPosicion2->getParametro('saWhereCtr')),
                $Qnombre,
                $Qapellido1,
                $Qapellido2,
                $Qcentro,
                $Qna,
            );
            $Qnombre = $filtros['nombre'];
            $Qapellido1 = $filtros['apellido1'];
            $Qapellido2 = $filtros['apellido2'];
            $Qcentro = $filtros['centro'];
            $Qna = $filtros['na'];
            $oPosicion2->olvidar((int)$stack);
        }
    }
} elseif (planning_post_string('saWhere') !== '' || planning_post_string('saWhereCtr') !== '') {
    $filtros = planning_filtros_persona_desde_sa_where_encoded(
        planning_post_string('saWhere'),
        planning_post_string('saWhereCtr'),
        $Qnombre,
        $Qapellido1,
        $Qapellido2,
        $Qcentro,
        $Qna,
    );
    $Qnombre = $filtros['nombre'];
    $Qapellido1 = $filtros['apellido1'];
    $Qapellido2 = $filtros['apellido2'];
    $Qcentro = $filtros['centro'];
    $Qna = $filtros['na'];
}

$periodo_txt = PeriodoPlanningHelper::textoPeriodoPorDefecto(planning_mes_fin_stgr());
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
    'Qnombre' => $Qnombre,
    'Qapellido1' => $Qapellido1,
    'Qapellido2' => $Qapellido2,
    'Qcentro' => $Qcentro,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_persona_que.phtml', $a_campos);
