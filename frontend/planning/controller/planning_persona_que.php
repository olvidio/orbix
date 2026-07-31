<?php
namespace frontend\planning\controller;

use frontend\planning\helpers\PlanningPostInput;
use frontend\planning\helpers\PlanningPayload;
use frontend\planning\support\PeriodoPlanningHelper;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

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

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = (string)filter_input(INPUT_POST, 'na');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qnombre = PlanningPostInput::postString('nombre');
$Qapellido1 = PlanningPostInput::postString('apellido1');
$Qapellido2 = PlanningPostInput::postString('apellido2');
$Qcentro = PlanningPostInput::postString('centro');

if (PlanningPostInput::postString('saWhere') !== '' || PlanningPostInput::postString('saWhereCtr') !== '') {
    $filtros = PlanningPayload::filtrosPersonaDesdeSaWhereEncoded(
        PlanningPostInput::postString('saWhere'),
        PlanningPostInput::postString('saWhereCtr'),
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

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::mergeSelectionIntoReturnParametros([
        'obj_pau' => $Qobj_pau,
        'na' => $Qna,
        'year' => $Qyear,
        'periodo' => $Qperiodo,
        'empiezamax' => $Qempiezamax,
        'empiezamin' => $Qempiezamin,
        'nombre' => $Qnombre,
        'apellido1' => $Qapellido1,
        'apellido2' => $Qapellido2,
        'centro' => $Qcentro,
    ], $Qid_sel, $Qscroll_id),
);

$periodo_txt = PeriodoPlanningHelper::textoPeriodoPorDefecto(PlanningPayload::mesFinStgr());
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
