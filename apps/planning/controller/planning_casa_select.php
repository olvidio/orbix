<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use planning\domain\ActividadesPorCasas;
use planning\domain\Planning;
use web\Hash;
use web\Periodo;
use function core\is_true;
use function core\urlsafe_b64encode;

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
$Qcdc_sel = (integer)filter_input(INPUT_POST, 'cdc_sel');
$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
//Para dibujar cuadricula aunque no tenga actividades.
$sin_activ = (integer)filter_input(INPUT_POST, 'sin_activ');

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

$mod = 0;
$nueva = 0;
if (is_true($Qpropuesta_calendario)) {
    $mod = 1; // 0 u otro valor (1 ver, 2 modificar, 3 eliminar..) el valor se pasa a la página link.

    /* En este caso casi todos los usuarios que entran en esta pagina de calendario es
     * para poder crear actividades. Por tanto nueva=1. Según el tipo de actividad no podrá
     * ser, pero esto hay que mirarlo a la hora de guardar.
     */
    $nueva = 1; // 0 o 1 para asignar una nueva actividad.

    if (ConfigGlobal::is_app_installed('procesos')) {
        /* Por el momento dejo a todos. Si añado el permiso de crear, como tiene
         * que ser al nivel superior, porque todavía no se sabe que tipo de actividad va a ser
         * quizá tenga consecuencias indeseadas. Si los menús me han llevado hasta aquí,
         * seguramente tengo permiso.
         */
        /*
        // sv
        $_SESSION['oPermActividades']->setId_tipo_activ('1.....');
        $permCrearActivDl_sv = $_SESSION['oPermActividades']->getPermisoCrear(TRUE);
        //sf
        $_SESSION['oPermActividades']->setId_tipo_activ('2.....');
        $permCrearActivDl_sf = $_SESSION['oPermActividades']->getPermisoCrear(TRUE);

        if (!$permCrearActivDl_sv && !$permCrearActivDl_sf) {
            // no tiene permisos para crea una nueva
            $nueva = 0;
        }
        */
    }
}

// mostrar encabezados arriba y abajo; derecha e izquierda.
if ($Qmodelo !== 2) {
    $doble = 1;
} else {
    $doble = 0;
}
// si es sólo un mes tampoco pongo doble (cabecera y pie)
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) {
    $doble = 0;
}

$cabecera = ucfirst(_("calendario de casas"));

list($sCdc, $a_actividades) = ActividadesPorCasas::actividadesPorCasas($Qcdc_sel, $oIniPlanning, $oFinPlanning, $sin_activ, $fin_iso, $inicio_iso);

$aGoBack = [
    'modelo' => $Qmodelo,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
    'cdc_sel' => $Qcdc_sel,
    'sin_activ' => $sin_activ,
    'sSeleccionados' => $sCdc,
];
$oPosicion->setParametros($aGoBack, 1);


$oHashMod = new Hash();
$oHashMod->setUrl(ConfigGlobal::getWeb() . '/apps/actividades/controller/planning_casa_modificar.php');
$a_camposHidden = array(
    'que' => 'modificar',
);
$oHashMod->setArraycamposHidden($a_camposHidden);
$oHashMod->setCamposForm('id_activ');
$param_mod = $oHashMod->getParamAjax();

$oHashNew = new Hash();
$oHashNew->setUrl(ConfigGlobal::getWeb() . '/apps/actividades/controller/planning_casa_nueva.php');
$a_camposHidden = array(
    'que' => 'nueva',
);
$oHashNew->setArraycamposHidden($a_camposHidden);
$oHashNew->setCamposForm('id_ubi');
$param_new = $oHashNew->getParamAjax();

$sactividades = urlsafe_b64encode(json_encode($a_actividades), JSON_THROW_ON_ERROR);
$sIniPlanning = urlsafe_b64encode(json_encode($oIniPlanning), JSON_THROW_ON_ERROR);
$sFinPlanning = urlsafe_b64encode(json_encode($oFinPlanning), JSON_THROW_ON_ERROR);

$oHashVer = new Hash();
$oHashVer->setUrl(ConfigGlobal::getWeb() . '/apps/planning/controller/planning_casa_ver.php');
$a_camposHidden = array(
    'que' => 'get',
    'modelo' => $Qmodelo,
    'dd' => $Qdd,
    'cabecera' => $cabecera,
    'sactividades' => $sactividades,
    'sIniPlanning' => $sIniPlanning,
    'sFinPlanning' => $sFinPlanning,
    'mod' => $mod,
    'nueva' => $nueva,
    'doble' => $doble,
);
$oHashVer->setArraycamposHidden($a_camposHidden);
$param_ver = $oHashVer->getParamAjax();

$a_campos = ['oPosicion' => $oPosicion,
    'param_ver' => $param_ver,
    'param_mod' => $param_mod,
    'param_new' => $param_new,
];

$oView = new ViewPhtml('planning\controller');
$oView->renderizar('planning_casa_select.phtml', $a_campos);
