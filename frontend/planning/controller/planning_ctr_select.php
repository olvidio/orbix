<?php

namespace frontend\planning\controller;

use src\shared\config\ConfigGlobal;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\model\ViewNewPhtml;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\planning\application\ActividadesDePersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;
use web\Periodo;
use web\Posicion;

/**
 * Planning (calendario) de las personas de un centro (o grupo de
 * centros), filtrado por periodo y tipo de persona (n, agd, s).
 *
 * Migrado desde `apps/planning/controller/planning_ctr_select.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once("frontend/shared/global_header_front.inc");
require_once("apps/core/global_object.inc");

/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

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
$doble = $Qmodelo !== 2 ? 1 : 0;
$interval = (int)$oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) {
    $doble = 0;
}

$Qsacd = (string)filter_input(INPUT_POST, 'sacd');
$Qctr = (string)filter_input(INPUT_POST, 'ctr');
$Qtodos_n = '';
$Qtodos_agd = '';
$Qtodos_s = '';

$aWhereP = ['situacion' => 'A'];
if (empty($Qsacd)) {
    $aWhereP['sacd'] = 'f';
}

$msg_txt = '';
$cabecera_title = '';
$PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
$cPersonas = [];
if ($Qctr !== '') {
    $nom_ubi = str_replace("+", "\\+", $Qctr);
    $aWhere = ['nombre_ubi' => '^' . $nom_ubi];
    $aOperador = ['nombre_ubi' => 'sin_acentos'];
    $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
    $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
    if (!empty($cCentros)) {
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $cabecera_title = ucfirst(sprintf(_("personas de: %s"), $nombre_ubi));
            $aWhereP['id_ctr'] = $id_ubi;
            $aWhereP['_ordre'] = 'apellido1';
            $cPersonas2 = $PersonaDlRepository->getPersonas($aWhereP);
            if (is_array($cPersonas2) && count($cPersonas2) >= 1) {
                $cPersonas = array_merge($cPersonas, $cPersonas2);
            } else {
                $msg_txt .= sprintf(_("No encuentro personas para %s"), $nombre_ubi);
                $msg_txt .= '<br>';
            }
        }
    } else {
        $msg_txt = _("No encuentro este ctr");
    }
} else {
    $cabecera_title = ucfirst(_("centros"));
    $Qtodos_n = (string)filter_input(INPUT_POST, 'todos_n');
    $Qtodos_agd = (string)filter_input(INPUT_POST, 'todos_agd');
    $Qtodos_s = (string)filter_input(INPUT_POST, 'todos_s');
    $aWhereP['id_tabla'] = 'n';
    if (!empty($Qtodos_n)) {
        $aWhereP['id_tabla'] = 'n';
    }
    if (!empty($Qtodos_agd)) {
        $aWhereP['id_tabla'] = 'a';
    }
    if (!empty($Qtodos_s)) {
        $aWhereP['id_tabla'] = 's';
    }
    $aWhereP['_ordre'] = 'id_ctr, apellido1';
    $cPersonas = $PersonaDlRepository->getPersonas($aWhereP);
}

$aGoBack = [
    'modelo' => $Qmodelo,
    'tipo' => $Qtipo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'sacd' => $Qsacd,
    'ctr' => $Qctr,
    'todos_n' => $Qtodos_n,
    'todos_agd' => $Qtodos_agd,
    'todos_s' => $Qtodos_s,
];
$oPosicion->setParametros($aGoBack, 1);

$a_actividades2 = ActividadesDePersonaService::actividadesPorPersona(
    $cPersonas,
    $fin_iso,
    $inicio_iso,
    $oIniPlanning,
    $inicio_local,
    agruparPorCentro: true
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
$oPlanning->setDd($Qdd);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'msg_txt' => $msg_txt,
    'cabecera_title' => $cabecera_title,
    'a_actividades2' => $a_actividades2,
    'goLeyenda' => $goLeyenda,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_ctr_select.phtml', $a_campos);
