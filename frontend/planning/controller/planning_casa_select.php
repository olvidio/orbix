<?php
namespace frontend\planning\controller;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Pantalla intermedia entre `planning_casa_que` y `planning_casa_ver`.
 * Registra los filtros en `Posicion` y dispara por AJAX la carga del
 * calendario real (`planning_casa_ver`).
 *
 * Migrado desde `apps/planning/controller/planning_casa_select.php`
 * (slice 2 de la migracion del modulo planning). Ya no se serializa el
 * array de actividades en base64: `planning_casa_ver` recalcula a partir
 * de los filtros.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros(($aGoBack ?? \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost()), \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));


$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qcdc_sel = (int)filter_input(INPUT_POST, 'cdc_sel');
$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qsin_activ = (int)filter_input(INPUT_POST, 'sin_activ');
$aIdCdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$aIdCdc = array_filter($aIdCdc, static fn($v) => $v !== false && $v !== '');
$sCdc = $Qcdc_sel === 9 ? implode(',', $aIdCdc) : '';

$aGoBack = [
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'cdc_sel' => $Qcdc_sel,
    'sin_activ' => $Qsin_activ,
    'sSeleccionados' => $sCdc,
];
$oPosicion->setParametros($aGoBack, 1);

$oHashMod = new HashFront();
$oHashMod->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/planning_casa_modificar.php');
$oHashMod->setArraycamposHidden(['que' => 'modificar']);
$oHashMod->setCamposForm('id_activ');
$param_mod = $oHashMod->getParamAjax();

$oHashNew = new HashFront();
$oHashNew->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/planning_casa_nueva.php');
$oHashNew->setArraycamposHidden(['que' => 'nueva']);
$oHashNew->setCamposForm('id_ubi');
$param_new = $oHashNew->getParamAjax();

$aCamposHiddenVer = [
    'modelo' => $Qmodelo,
    'cdc_sel' => $Qcdc_sel,
    'sSeleccionados' => $sCdc,
    'sin_activ' => $Qsin_activ,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'propuesta_calendario' => $Qpropuesta_calendario,
];

$oHashVer = new HashFront();
$oHashVer->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/planning_casa_ver.php');
$oHashVer->setArraycamposHidden($aCamposHiddenVer);
$param_ver = $oHashVer->getParamAjax();

$a_campos = [
    'oPosicion' => $oPosicion,
    'param_ver' => $param_ver,
    'param_mod' => $param_mod,
    'param_new' => $param_new,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_casa_select.phtml', $a_campos);
