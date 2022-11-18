<?php

use actividades\model\entity\GestorActividad;
use ubis\model\entity\GestorCasaDl;
use ubis\model\entity\GestorCentroEllas;
use ubis\model\entity\Ubi;
use web\Periodo;

/**
 * Esta página tiene la misión de realizar la llamada a calendario php;
 * y lo hace con distintos valores, en función de las páginas anteriores
 *
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qmodelo = (integer)filter_input(INPUT_POST, 'modelo');

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    // puede ser más de uno
    if (is_array($a_sel) && count($a_sel) > 1) {
        $aid_nom = array();
        foreach ($a_sel as $nom_sel) {
            $aid_nom[] = $nom_sel;
        }
    } else {
        $aid_nom[] = $a_sel[0];
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    }
}

$Qcdc_sel = (integer)filter_input(INPUT_POST, 'cdc_sel');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qdd = (integer)filter_input(INPUT_POST, 'dd');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$Qid_cdc_mas = (string)filter_input(INPUT_POST, 'id_cdc_mas');
$Qid_cdc_num = (string)filter_input(INPUT_POST, 'id_cdc_num');
$Qiasistentes_val = (string)filter_input(INPUT_POST, 'iasistentes_val');
$Qiactividad_val = (string)filter_input(INPUT_POST, 'iactividad_val');


// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();
$oIniPlanning = $oPeriodo->getF_ini();
$oFinPlanning = $oPeriodo->getF_fin();

$a_id_cdc = [];
// valores por defecto.
//divisiones por día
if (empty($Qdd) || (($Qdd <> 1) && ($Qdd <> 3))) {
    $Qdd = 3;
}

$mod = 1; // 0 u otro valor (1 ver, 2 modificar, 3 eliminar..) el valor se pasa a la página link.

/* En este caso casi todos los usuarios que entran en esta pagina de calendario es
 * para poder crear actividades. Por tanto nueva=1. Según el tipo de actividad no podrá
 * ser, pero esto hay que mirarlo a la horta de guardar.
 */
$nueva = 1; // 0 o 1 para asignar una nueva actividad.

if (core\ConfigGlobal::is_app_installed('procesos')) {
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
}

// mostrar encabezados arriba y abajo; derecha e izquierda.
if (!empty($print)) {
    $doble = 0;
    $mod = 0;
    $nueva = 0;
} else {
    $doble = 1;
}
// si es sólo un mes tampoco pongo doble (cabecera y pie)
$interval = $oFinPlanning->diff($oIniPlanning)->format('%m');
if ($interval < 2) {
    $doble = 0;
}

$cabecera = ucfirst(_("calendario de casas"));

$GesActividades = new GestorActividad();

if ($Qcdc_sel < 10) { //Para buscar por casas.
    $aWhere = array();
    $aOperador = array();
    switch ($Qcdc_sel) {
        case 1:
            $aWhere['sv'] = 't';
            $aWhere['sf'] = 't';
            break;
        case 2:
            $aWhere['sv'] = 'f';
            $aWhere['sf'] = 't';
            break;
        case 3: // casas comunes: cdr + dlb + sf +sv
            $aWhere['sv'] = 't';
            $aWhere['sf'] = 't';
            $aWhere['tipo_ubi'] = 'cdcdl';
            $aWhere['tipo_casa'] = 'cdc|cdr';
            $aOperador['tipo_casa'] = '~';
            break;
        case 4:
            $aWhere['sv'] = 't';
            break;
        case 5:
            $aWhere['sf'] = 't';
            break;
        case 6:
            $aWhere['sf'] = 't';
            // también los centros que son como cdc
            $GesCentrosSf = new GestorCentroEllas();
            $cCentrosSf = $GesCentrosSf->getCentros(array('cdc' => 't', '_ordre' => 'nombre_ubi'));
            break;
        case 9:
            // posible selección múltiple de casas
            $a_id_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            if (!empty($a_id_cdc)) {
                $aWhere['id_ubi'] = '^' . implode('$|^', $a_id_cdc) . '$';
                $aOperador['id_ubi'] = '~';
            }
            break;
    }
    $aWhere['_ordre'] = 'nombre_ubi';
    $GesCasaDl = new GestorCasaDl();
    $cCasasDl = $GesCasaDl->getCasas($aWhere, $aOperador);

    if ($Qcdc_sel == 6) { //añado los ctr de sf
        foreach ($cCentrosSf as $oCentroSf) {
            array_push($cCasasDl, $oCentroSf);
        }
    }

    $p = 0;
    $cdc = [];
    $a_actividades = [];
    foreach ($cCasasDl as $oCasaDl) {
        $a_cdc = array();
        $id_ubi = $oCasaDl->getId_ubi();
        $nombre_ubi = $oCasaDl->getNombre_ubi();

        $cdc[$p] = "u#$id_ubi#$nombre_ubi";

        $a_cdc = $GesActividades->actividadesDeUnaCasa($id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
        if ($a_cdc !== false) {
            $a_actividades[$nombre_ubi] = array($cdc[$p] => $a_cdc);
            $p++;
        }
    }
    ksort($a_actividades);
    /*
     lo que sigue es para que nos represente una linea en blanco al final:
     esto permite visualizar correctamente las 3 divisiones en los días
     en que todas las casas están ocupadas.
     */
    $cdc[$p + 1] = "##";
    $a_actividades[] = array($cdc[$p + 1] => array());
} else { // cdc_sel > 10 Para buscar por actividades (todas).
    // busco todas las actividades del periodo y las agrupo por ubis.
    $oGesActividades = new GestorActividad();
    $aWhere = array();
    $aOperador = array();
    switch ($Qcdc_sel) {
        case 11:
            $aWhere['id_tipo_activ'] = '^1';
            $aOperador['id_tipo_activ'] = '~';
            break;
        case 12:
            $aWhere['id_tipo_activ'] = '^2';
            $aOperador['id_tipo_activ'] = '~';
            break;
    }
    $aWhere['f_ini'] = $finIso;
    $aOperador['f_ini'] = '<=';
    $aWhere['f_fin'] = $inicioIso;
    $aOperador['f_fin'] = '>=';
    $aWhere['status'] = 3;
    $aOperador['status'] = '<';
    $aWhere['_ordre'] = 'id_ubi';

    $aUbis = $oGesActividades->getUbis($aWhere, $aOperador);
    $p = 0;
    $a_actividades = array();
    foreach ($aUbis as $id_ubi) {
        $a_cdc = array();
        if (empty($id_ubi)) {
            $nombre_ubi = _("por determinar");
            $cdc[$p] = "u#2#$nombre_ubi"; // hay que poner un id_ubi para que vaya bien la función de dibujar el calendario.
        } elseif ($id_ubi == 1) {
            $nombre_ubi = _("otros lugares");
            $cdc[$p] = "u#$id_ubi#$nombre_ubi";
        } else {
            $oCasa = Ubi::NewUbi($id_ubi);
            $id_ubi = $oCasa->getId_ubi();
            $nombre_ubi = $oCasa->getNombre_ubi();
            $cdc[$p] = "u#$id_ubi#$nombre_ubi";
        }
        $a_cdc = $GesActividades->actividadesDeUnaCasa($id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
        if ($a_cdc !== false) {
            $a_actividades[$nombre_ubi] = array($cdc[$p] => $a_cdc);
            $p++;
        }
    }
    ksort($a_actividades);
    /*
     lo que sigue es para que nos represente una linea en blanco al final:
     esto permite visualizar correctamente las 3 divisiones en los días
     en que todas las casas están ocupadas.
     */
    $cdc[$p + 1] = "##";
    $a_actividades[] = array($cdc[$p + 1] => array());
}

$aGoBack = array(
    'modelo' => $Qmodelo,
    'cdc_sel' => $Qcdc_sel,
    'dd' => $Qdd,
    'id_cdc' => $a_id_cdc,
    'id_cdc_mas' => $Qid_cdc_mas,
    'id_cdc_num' => $Qid_cdc_num,
    'iasistentes_val' => $Qiasistentes_val,
    'iactividad_val' => $Qiactividad_val,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax);
$oPosicion->setParametros($aGoBack, 1);


$oHashMod = new web\Hash();
$oHashMod->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividades/controller/calendario_ajax.php');
$a_camposHidden = array(
    'que' => 'modificar',
);
$oHashMod->setArraycamposHidden($a_camposHidden);
$oHashMod->setCamposForm('id_activ');
$param_mod = $oHashMod->getParamAjax();

$oHashNew = new web\Hash();
$oHashNew->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividades/controller/calendario_ajax.php');
$a_camposHidden = array(
    'que' => 'nueva',
);
$oHashNew->setArraycamposHidden($a_camposHidden);
$oHashNew->setCamposForm('id_ubi');
$param_new = $oHashNew->getParamAjax();

$sactividades = base64_encode(serialize($a_actividades));
$sIniPlanning = base64_encode(serialize($oIniPlanning));
$sFinPlanning = base64_encode(serialize($oFinPlanning));

$oHashVer = new web\Hash();
$oHashVer->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividades/controller/calendario_ajax.php');
$a_camposHidden = array(
    'que' => 'get',
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

$oView = new core\View('actividades/controller');
$oView->renderizar('calendario_planning.phtml', $a_campos);
