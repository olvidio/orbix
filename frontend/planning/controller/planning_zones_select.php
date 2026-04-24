<?php

namespace frontend\planning\controller;

use src\shared\config\ConfigGlobal;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\model\ViewNewPhtml;
use src\planning\application\ActividadesPorZonasService;
use web\Hash;
use web\Posicion;

/**
 * Planning (calendario) por zonas sacd. El servicio
 * `ActividadesPorZonasService` devuelve los datos por zona; el frontend
 * solo arma el renderer y pasa los arrays a la vista.
 *
 * Migrado desde `apps/planning/controller/planning_zones_select.php`
 * (slice 3 de la migracion del modulo planning). La version legacy
 * hacia `echo` de HTML e incluia directamente los CSS y el calendario;
 * ahora la presentacion vive en la vista PHTML.
 */
require_once("frontend/shared/global_header_front.inc");
require_once("apps/core/global_object.inc");

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

$id_nom_jefe = null;
$data = ActividadesPorZonasService::execute(
    $Qid_zona,
    $Qtrimestre,
    $Qyear,
    $Qactividad,
    $Qpropuesta,
    $id_nom_jefe
);

$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

switch ($Qmodelo) {
    case 2:
    case 1:
        include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');
        break;
    case 3:
        include_once(ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
include_once(ConfigGlobal::$dir_estilos . '/calendario_color_cols.css.php');

$oPlanning = new PlanningRenderer();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);
$oPlanning->setDd(3);
$oPlanning->setInicio($data['oIniPlanning']);
$oPlanning->setFin($data['oFinPlanning']);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'titulo' => $data['titulo'],
    'zonas' => $data['zonas'],
    'actividades_por_zona' => $data['actividades_por_zona'],
    'cabeceras_por_zona' => $data['cabeceras_por_zona'],
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_zones_select.phtml', $a_campos);
