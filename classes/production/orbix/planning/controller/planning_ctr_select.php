<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use personas\model\entity\GestorPersonaDl;
use planning\domain\ActividadesDePersona;
use planning\domain\Planning;
use web\Hash;
use web\Periodo;
use ubis\model\entity\GestorCentroDl;

/**
 * Esta página tiene la misión de realizar la llamada a calendario php;
 * y lo hace con distintos valores, en función de las páginas anteriores
 *
 * @param string $tipo planning-> de un grupo de personas n o agd.
 *                    p_de_paso-> de un grupo de personas de paso.
 *                    ctr-> de las personas de un ctr.
 *                    planning_ctr->  de las personas de un ctr.
 *                    planning_cdc-> actividades que se realizan en una casa del a dl.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

// periodo.
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

// valores por defecto.
//divisiones por día
$Qdd = 3;
$mod = 0; // 0 u otro valor (1 ver, 2 modificar, 3 eliminar..) el valor se pasa a la página link.
$nueva = 0; // 0 o 1 para asignar una nueva actividad.
// mostrar encabezados arriba y abajo; derecha e izquierda.
if ($Qmodelo !== 2) {
    $doble = 1;
} else {
    $doble = 0;
}
// si es sólo un mes tampoco pongo doble (cabecera y pie)
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) $doble = 0;

$Qsacd = '';
$Qctr = '';
$Qtodos_n = '';
$Qtodos_agd = '';
$Qtodos_s = '';

/*
switch ($Qtipo) {
    case 'ctr':
        $aWhereP = [];
        $Qid_ubi = (string)filter_input(INPUT_POST, 'id_ubi');
        if (!empty($Qid_ubi)) {
            $id_ubi = (integer)strtok($Qid_ubi, '#');
            $nombre_ubi = (string)strtok('#');
            $cabecera = ucfirst(sprintf(_("personas de: %s"), $nombre_ubi));
            $GesPersonas = new personas\GestorPersonaDl();
            $aWhereP['id_ctr'] = $id_ubi;
            $cPersonas = $GesPersonas->getPersonasDl($aWhereP);
        }
        break;
*/

// case 'planning_ctr':
$aWhere = [];
$aWhereP = array('situacion' => 'A');
$Qsacd = (string)filter_input(INPUT_POST, 'sacd');
$Qctr = (string)filter_input(INPUT_POST, 'ctr');
if (empty($Qsacd)) {
    $aWhereP['sacd'] = 'f';
}

$msg_txt = '';
if (!empty($Qctr)) {
    $Qtodos_n = '';
    $Qtodos_agd = '';
    $Qtodos_s = '';
    $nom_ubi = str_replace("+", "\+", $Qctr); // para los centros de la sss+
    $aWhere['nombre_ubi'] = '^' . $nom_ubi;
    $aOperador['nombre_ubi'] = 'sin_acentos';
    $GesCentros = new GestorCentroDl();
    $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
    if (!empty($cCentros)) {
        $cPersonas = []; // para unir todas las personas de más de un centro.
        $GesPersonas = new GestorPersonaDl();
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $cabecera_title = ucfirst(sprintf(_("personas de: %s"), $nombre_ubi));
            $aWhereP['id_ctr'] = $id_ubi;
            $aWhereP['_ordre'] = 'apellido1';
            $cPersonas2 = $GesPersonas->getPersonas($aWhereP);
            if (is_array($cPersonas2) && count($cPersonas2) >= 1) {
                if (is_array($cPersonas)) {
                    $cPersonas = array_merge($cPersonas, $cPersonas2);
                } else {
                    $cPersonas = $cPersonas2;
                }
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
    // Pro defecto los 'n':
    $aWhereP['id_tabla'] = 'n';
    if (!empty($Qtodos_n)) $aWhereP['id_tabla'] = 'n';
    if (!empty($Qtodos_agd)) $aWhereP['id_tabla'] = 'a';
    if (!empty($Qtodos_s)) $aWhereP['id_tabla'] = 's';
    $aWhereP['_ordre'] = 'id_ctr, apellido1';
    $GesPersonas = new GestorPersonaDl();
    $cPersonas = $GesPersonas->getPersonas($aWhereP);
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

//por cada persona busco las actividades.
$a_actividades2 = ActividadesDePersona::actividadesPorPersona($cPersonas, $fin_iso, $inicio_iso, $oIniPlanning, $inicio_local);

$goLeyenda = Hash::link(ConfigGlobal::getWeb() . '/apps/planning/controller/leyenda.php?' . http_build_query(array('id_item' => 1)));
switch ($Qmodelo) {
    case 2:
    case 1:
        include_once(ConfigGlobal::$dir_estilos . '/calendario.css.php');
        //include_once('apps/web/calendario.php');
        break;
    case 3:
        include_once(ConfigGlobal::$dir_estilos . '/calendario_grid.css.php');
        include_once('apps/web/calendario_grid.php');
        break;
}
// Las variables de color de las columnas están en la página css.
include_once(ConfigGlobal::$dir_estilos . '/calendario_color_cols.css.php');
$oPlanning = new Planning();
$oPlanning->setColorColumnaUno($colorColumnaUno);
$oPlanning->setColorColumnaDos($colorColumnaDos);
$oPlanning->setTable_border($table_border);

$oPlanning->setDd($Qdd);
$oPlanning->setInicio($oIniPlanning);
$oPlanning->setFin($oFinPlanning);
$oPlanning->setMod($mod);
$oPlanning->setNueva($nueva);
$oPlanning->setDoble($doble);

$a_campos = ['oPosicion' => $oPosicion,
    'oPlanning' => $oPlanning,
    'msg_txt' => $msg_txt,
    'cabecera_title' => $cabecera_title,
    'a_actividades2' => $a_actividades2,
    'goLeyenda' => $goLeyenda,
];

$oView = new ViewPhtml('planning\controller');
$oView->renderizar('planning_ctr_select.phtml', $a_campos);