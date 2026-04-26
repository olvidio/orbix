<?php
/**
 * Formulario para escoger un centro (y un periodo) y lanzar un listado
 * de actividades, datos economicos, cdc, etc.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\value_objects\PauType;
use frontend\shared\web\CentrosQue;
use frontend\shared\web\DesplegableArray;
use web\Hash;
use frontend\shared\web\PeriodoQue;
use function src\shared\domain\helpers\strtoupper_dlb;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$Qtipo_ctr = (string)filter_input(INPUT_POST, 'tipo_ctr');
$Qtipo_lista = (string)filter_input(INPUT_POST, 'tipo_lista');
$Qver_ctr = (string)filter_input(INPUT_POST, 'ver_ctr');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

$oForm = new CentrosQue();
$miRolePau = OrbixRuntime::miRolePau();
$filtro = ['active' => true];
if ($miRolePau === PauType::PAU_CTR) {
    $id_pau = (string)$_SESSION['session_auth']['MiUsuario']->getCsv_id_pau();
    $filtro['id_ubi_in'] = array_values(array_filter(array_map('intval', explode(',', $id_pau)), static fn ($v) => $v > 0));
    $oForm->setCentros('centro');
} else {
    if ($Qtipo_ctr === 'sg') {
        if (OrbixRuntime::miSfsv() === 1) {
            $oForm->setCentros('sv');
            $filtro['sv'] = true;
            $filtro['tipo_ctr'] = 'seccion_no_s';
        } elseif (OrbixRuntime::miSfsv() === 2) {
            $oForm->setCentros('sf');
            $filtro['sf'] = true;
            $filtro['tipo_ctr'] = 'seccion_no_s';
        }
    } else {
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
            $oForm->setCentros('all');
        } else {
            if (OrbixRuntime::miSfsv() === 1) {
                $oForm->setCentros('sv');
                $filtro['sv'] = true;
            } elseif (OrbixRuntime::miSfsv() === 2) {
                $oForm->setCentros('sf');
                $filtro['sf'] = true;
            }
        }
    }
}

switch ($Qtipo_lista) {
    case 'lista_activ':
        $url = AppUrlConfig::getPublicAppBaseUrl() . "/programas/centro_ajax.php";
        $parametros = "pata+'&que=lista_activ'";
        break;
    case 'datosEc':
        $url = AppUrlConfig::getPublicAppBaseUrl() . "/programas/centro_ec_ajax.php";
        $parametros = "pata+'&que=get'";
        break;
    case 'datosEcGastos':
        $url = AppUrlConfig::getPublicAppBaseUrl() . "/programas/centro_ec_ajax.php";
        $parametros = "pata+'&que=getGastos'";
        break;
    case 'ctrsEncargados':
        $url = AppUrlConfig::getPublicAppBaseUrl() . "/frontend/actividades/controller/calendario_listas.php";
        $parametros = "pata+'&que=lista_cdc&ver_ctr=$Qver_ctr'";
        break;
    case 'crt':
    case 'cv':
        $url = AppUrlConfig::getPublicAppBaseUrl() . "/frontend/actividades/controller/lista_centros_activ.php";
        $parametros = "pata";
        break;
    default:
        $url = AppUrlConfig::getPublicAppBaseUrl() . "/programas/centro_ajax.php";
        $parametros = "pata+'&que=get'";
}

$oForm->setFiltroCentros($filtro);
$oForm->setAction('');
$aOpcionesCentros = $oForm->getPosiblesCentros();
$oSelects = new DesplegableArray('', $aOpcionesCentros, 'id_ctr');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_centros(event)');

$oFormP = null;
if ($Qperiodo === 'no') {
    if ($Qtipo_lista === 'datosEc') $oForm->setTitulo(strtoupper_dlb(_("resumen económico")));
    $oForm->setBoton("<input type=button name=\"buscar\" value=\"" . _('buscar') . "\" onclick=\"fnjs_ver();\">");
} else {
    $aOpciones = array(
        'tot_any' => _("todo el año"),
        'trimestre_1' => _("primer trimestre"),
        'trimestre_2' => _("segundo trimestre"),
        'trimestre_3' => _("tercer trimestre"),
        'trimestre_4' => _("cuarto trimestre"),
        'separador' => '---------',
        'curso_ca' => _("curso ca"),
        'curso_crt' => _("curso crt"),
        'separador1' => '---------',
        'otro' => _("otro")
    );
    $oFormP = new PeriodoQue();
    $oFormP->setFormName('seleccion');
    $oFormP->setPosiblesPeriodos($aOpciones);
    $oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
    $oFormP->setDesplAnysOpcion_sel($Qyear);
    $oFormP->setEmpiezaMin($Qempiezamin);
    $oFormP->setEmpiezaMax($Qempiezamax);

    $oFormP->setTitulo(strtoupper_dlb(_("seleccionar un centro y un período")));
    $oFormP->setAntes($oSelects->ListaSelects());
    $oFormP->setBoton("<input type=button name=\"buscar\" value=\"" . _('buscar') . "\" onclick=\"fnjs_ver();\">");
}


$url_ajax = AppUrlConfig::getPublicAppBaseUrl() . '/programas/centro_ajax.php';


$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('empiezamin!empiezamax!iactividad_val!iasistentes_val!id_ctr!id_ctr_mas!id_ctr_num!periodo!year');
$oHash->setCamposNo('id_ctr');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oSelects' => $oSelects,
    'Qperiodo' => $Qperiodo,
    'url' => $url,
    'parametros' => $parametros,
    'url_ajax' => $url_ajax,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('actividades_centro_que.phtml', $a_campos);
