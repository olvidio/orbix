<?php

namespace frontend\planning\controller;

use frontend\planning\support\PlanningRenderer;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Planning (calendario) por zonas sacd. Datos vía `PostRequest` → `/src/planning/planning_zones_select_data`
 * (`PlanningZonesSelectData` / `ActividadesPorZonasService` en backend); solo se reconstruyen fechas para `PlanningRenderer`.
 *
 * Migrado desde `apps/planning/controller/planning_zones_select.php`
 * (slice 3 de la migracion del modulo planning). La version legacy
 * hacia `echo` de HTML e incluia directamente los CSS y el calendario;
 * ahora la presentacion vive en la vista PHTML.
 */
require_once("frontend/shared/global_header_front.inc");


/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qtrimestre = (int)filter_input(INPUT_POST, 'trimestre');
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qactividad = (string)filter_input(INPUT_POST, 'actividad');
$Qpropuesta = (string)filter_input(INPUT_POST, 'propuesta');

$oPosicion->setParametros([
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'trimestre' => $Qtrimestre,
    'id_zona' => $Qid_zona,
    'actividad' => $Qactividad,
    'propuesta' => $Qpropuesta,
], 1);

$data = PostRequest::getDataFromUrl('/src/planning/planning_zones_select_data', $_POST);
$data = is_array($data) ? $data : [];
$isoIni = (string)($data['planning_ini_iso'] ?? '');
$isoFin = (string)($data['planning_fin_iso'] ?? '');
$oIniPlanning = DateTimeLocal::createFromFormat('Y-m-d', $isoIni) ?: new DateTimeLocal($isoIni);
$oFinPlanning = DateTimeLocal::createFromFormat('Y-m-d', $isoFin) ?: new DateTimeLocal($isoFin);

$goLeyenda = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

switch ($Qmodelo) {
    case 2:
    case 1:
        include_once(OrbixRuntime::dirEstilos() . '/calendario.css.php');
        break;
    case 3:
        include_once(OrbixRuntime::dirEstilos() . '/calendario_grid.css.php');
        include_once('frontend/shared/web/calendario_grid.php');
        break;
}
include_once(OrbixRuntime::dirEstilos() . '/calendario_color_cols.css.php');

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);
$oPlanning->setDd(3);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'titulo' => $data['titulo'] ?? '',
    'zonas' => (int)($data['zonas'] ?? 0),
    'actividades_por_zona' => (array)($data['actividades_por_zona'] ?? []),
    'cabeceras_por_zona' => (array)($data['cabeceras_por_zona'] ?? []),
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_zones_select.phtml', $a_campos);
