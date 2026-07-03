<?php
namespace frontend\planning\controller;

use frontend\notas\helpers\NotasFormSupport;
use frontend\planning\helpers\PlanningPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Formulario de filtros para el planning por zonas (sacd). Calcula el
 * subconjunto de zonas visible segun el rol y prepara el desplegable.
 *
 * Migrado desde `apps/planning/controller/planning_zones_que.php`
 * (slice 3 de la migracion del modulo planning). La plantilla se ha
 * reescrito como PHTML; ya no se usa Twig.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack((int)$stack)) {
            $oPosicion2->olvidar((int)$stack);
        }
    }
}
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionIntoReturnParametros(ListNavSupport::buildReturnParametrosFromPost(), ListNavSupport::idSelFromPost(), ListNavSupport::scrollIdFromPost()));



$Qmodelo = (int)filter_input(INPUT_POST, 'modo');
$Qmodelo = empty($Qmodelo) ? 1 : $Qmodelo;

$Qyear = (int)filter_input(INPUT_POST, 'year');
$year = empty($Qyear) ? (int)date('Y') : $Qyear;
$Qtrimestre = (int)filter_input(INPUT_POST, 'trimestre');

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qactividad = (string)filter_input(INPUT_POST, 'actividad');
$Qpropuesta = (bool)filter_input(INPUT_POST, 'propuesta');

$checksTrim = [
    1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '',
    101 => '', 102 => '', 103 => '', 104 => '', 105 => '', 106 => '',
    107 => '', 108 => '', 109 => '', 110 => '', 111 => '', 112 => '',
];
if (empty($Qtrimestre)) {
    $mes = (int)date('m');
    if ($mes < 4) {
        $checksTrim[1] = 'checked';
    } elseif ($mes < 7) {
        $checksTrim[2] = 'checked';
    } elseif ($mes > 8 && $mes < 10) {
        $checksTrim[3] = 'checked';
    } elseif ($mes > 9) {
        $checksTrim[4] = 'checked';
    }
} elseif (array_key_exists($Qtrimestre, $checksTrim)) {
    $checksTrim[$Qtrimestre] = 'checked';
}

$zonesData = PostRequest::getDataFromUrl('/src/planning/planning_zones_que_data', []);
$aOpciones = NotasFormSupport::desplegableOpciones($zonesData['opciones_zonas'] ?? []);
$oDesplZonas = new Desplegable();
$oDesplZonas->setOpciones($aOpciones);
$oDesplZonas->setBlanco(false);
if ($Qid_zona !== 0) {
    $oDesplZonas->setOpcion_sel(PlanningPayload::desplegableOpcionSel($Qid_zona));
}

$is_jefeCalendario = PlanningPayload::isJefeCalendario();
$url = 'frontend/planning/controller/planning_zones_select.php';

$oHash = new HashFront();
$oHash->setUrl($url);
$oHash->setArraycamposHidden([
    'modelo' => $Qmodelo,
    'propuesta' => $Qpropuesta,
]);
$oHash->setCamposForm('actividad!year!id_zona!trimestre');
$oHash->setCamposNo('modelo!propuesta');

$oFormAny = new PeriodoQue();
$aOpcionesAnys = PlanningPayload::periodoAnysOpciones();
$oFormAny->setPosiblesAnys($aOpcionesAnys);
$oFormAny->setDesplAnysOpcion_sel(PlanningPayload::desplegableOpcionSel($year));

$chk_actividad_si = ($Qactividad !== '' && $Qactividad === 'no') ? '' : 'checked';
$chk_actividad_no = ($Qactividad === 'no') ? 'checked' : '';

$a_campos = [
    'oHash' => $oHash,
    'url' => $url,
    'is_jefeCalendario' => $is_jefeCalendario,
    'oDesplZonas' => $oDesplZonas,
    'year' => $year,
    'oFormAny' => $oFormAny,
    'chk_actividad_si' => $chk_actividad_si,
    'chk_actividad_no' => $chk_actividad_no,
    'checksTrim' => $checksTrim,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_zones_que.phtml', $a_campos);
