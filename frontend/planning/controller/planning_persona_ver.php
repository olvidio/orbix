<?php

namespace frontend\planning\controller;

use core\ConfigGlobal;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\model\ViewNewPhtml;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\planning\application\ActividadesDePersonaService;
use src\shared\infrastructure\ProvidesRepositories;
use web\Hash;
use web\Periodo;
use web\Posicion;

/**
 * Planning (calendario) de las actividades asignadas a un conjunto
 * de personas seleccionadas en `planning_persona_select`.
 *
 * Migrado desde `apps/planning/controller/planning_persona_ver.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once("frontend/shared/global_header_front.inc");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$aid_nom = [];
if (!empty($a_sel)) {
    if (count($a_sel) > 1) {
        foreach ($a_sel as $nom_sel) {
            $aid_nom[] = $nom_sel;
        }
    } else {
        $aid_nom[] = $a_sel[0];
    }
}

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicio_iso = $oPeriodo->getF_ini_iso();
$fin_iso = $oPeriodo->getF_fin_iso();
$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();
$inicio_local = $oIniPlanning->getFromLocal();

$Qdd = 3;
$mod = 0;
$nueva = 0;

$print = 0;
if ($Qmodelo === 2) {
    $print = 1;
}
$doble = $print === 1 ? 0 : 1;
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ((int)$interval < 2) {
    $doble = 0;
}

$cabecera_title = ucfirst(_("persona seleccionada"));

$aWhere = [
    'id_nom' => implode(',', $aid_nom),
];
$aOperador = [
    'id_nom' => 'OR',
];

$repositoryProvider = new class {
    use ProvidesRepositories;

    public function get(string $entityType): object
    {
        return $this->getRepository($entityType);
    }
};

try {
    if ($Qobj_pau === '' || $Qobj_pau === 'PersonaDl') {
        $PersonaRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
    } else {
        $PersonaRepository = $repositoryProvider->get($Qobj_pau);
    }
} catch (\InvalidArgumentException) {
    $PersonaRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
}
$cPersonas = $PersonaRepository->getPersonas($aWhere, $aOperador);

$aGoBack = [
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'sacd' => '',
    'ctr' => '',
    'todos_n' => '',
    'todos_agd' => '',
    'todos_s' => '',
    'id_ubi' => '',
];
$oPosicion->setParametros($aGoBack, 1);

$a_actividades = ActividadesDePersonaService::actividadesPorPersona(
    $cPersonas,
    $fin_iso,
    $inicio_iso,
    $oIniPlanning,
    $inicio_local,
    agruparPorCentro: false
);

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
$oPlanning->setDd($Qdd);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);
$oPlanning->setActividades($a_actividades);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'goLeyenda' => $goLeyenda,
    'cabecera_title' => $cabecera_title,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_persona_ver.phtml', $a_campos);
