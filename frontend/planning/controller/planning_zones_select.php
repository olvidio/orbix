<?php
namespace frontend\planning\controller;

use frontend\planning\helpers\PlanningPayload;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Planning (calendario) por zonas sacd. Datos vía `PostRequest` → `/src/planning/planning_zones_select_data`
 * (`PlanningZonesSelectData` / `ActividadesPorZonasService` en backend); fechas de periodo como `DateTimeImmutable` para `PlanningRenderer`.
 *
 * Migrado desde `apps/planning/controller/planning_zones_select.php`
 * (slice 3 de la migracion del modulo planning). La version legacy
 * hacia `echo` de HTML e incluia directamente los CSS y el calendario;
 * ahora la presentacion vive en la vista PHTML.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qtrimestre = (int)filter_input(INPUT_POST, 'trimestre');
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qactividad = (string)filter_input(INPUT_POST, 'actividad');
$Qpropuesta = (bool)filter_input(INPUT_POST, 'propuesta');

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros([
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'trimestre' => $Qtrimestre,
    'id_zona' => $Qid_zona,
    'actividad' => $Qactividad,
    'propuesta' => $Qpropuesta,
], \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));


$oPosicion->setParametros([
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'trimestre' => $Qtrimestre,
    'id_zona' => $Qid_zona,
    'actividad' => $Qactividad,
    'propuesta' => $Qpropuesta,
], 1);

$data = PostRequest::getDataFromUrl('/src/planning/planning_zones_select_data', $_POST);
$zonesSelect = PlanningPayload::zonesSelectFromPayload($data);
$isoIni = $zonesSelect['planning_ini_iso'];
$isoFin = $zonesSelect['planning_fin_iso'];
$oIniPlanning = \DateTimeImmutable::createFromFormat('Y-m-d', $isoIni) ?: new \DateTimeImmutable($isoIni);
$oFinPlanning = \DateTimeImmutable::createFromFormat('Y-m-d', $isoFin) ?: new \DateTimeImmutable($isoFin);

$goLeyenda = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

$estilos = PlanningPayload::calendarioEstilos();

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($estilos['colorColumnaUno']);
$oPlanning->setColorColumnaDos($estilos['colorColumnaDos']);
$oPlanning->setColorColumnaDomingo($estilos['colorColumnaDomingo']);
$oPlanning->setTable_border($estilos['table_border']);
$oPlanning->setDd(3);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);

$msg_txt = '';
if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpropuesta)) {
    $msg_txt = _("Propuesta de calendario: actividades en cualquier estado (menos borrable)");
}
$a_campos = [
    'msg_txt' => $msg_txt,
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'titulo' => $zonesSelect['titulo'],
    'zonas' => $zonesSelect['zonas'],
    'actividades_por_zona' => $zonesSelect['actividades_por_zona'],
    'cabeceras_por_zona' => $zonesSelect['cabeceras_por_zona'],
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_zones_select.phtml', $a_campos);
