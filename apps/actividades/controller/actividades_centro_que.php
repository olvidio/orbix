<?php
/**
 * Esta página sirve para asignar una dirección a un determinado ubi.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use function core\strtoupper_dlb;
use usuarios\model\entity\Role;
use web\CentrosQue;
use web\DesplegableArray;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo_ctr = (string)\filter_input(INPUT_POST, 'tipo_ctr');
$Qtipo_lista = (string)\filter_input(INPUT_POST, 'tipo_lista');
$Qver_ctr = (string)\filter_input(INPUT_POST, 'ver_ctr');

$Qperiodo = (string)\filter_input(INPUT_POST, 'periodo');
$Qyear = (string)\filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)\filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)\filter_input(INPUT_POST, 'empiezamin');

$oForm = new CentrosQue();
// miro que rol tengo. Si soy centro, sólo veo la mía
$miRolePau = ConfigGlobal::mi_role_pau();
if ($miRolePau == Role::PAU_CTR) { //centro
    $id_pau = ConfigGlobal::mi_role_pau();
    $sDonde = str_replace(",", " OR id_ubi=", $id_pau);
    //formulario para centros cuyo calendario de actividades interesa
    $donde = "WHERE status='t' AND (id_ubi=$sDonde)";
    $oForm->setCentros('centro');
} else {
    // para los listados de sg. sólo ctrs de sg
    if ($Qtipo_ctr == 'sg') {
        if (ConfigGlobal::mi_sfsv() == 1) {
            $oForm->setCentros('sv');
            $donde = "WHERE status='t' AND sv='t' AND tipo_ctr ~ '^s[^s]'";
        } elseif (ConfigGlobal::mi_sfsv() == 2) {
            $oForm->setCentros('sf');
            $donde = "WHERE status='t' AND sf='t' AND tipo_ctr ~ '^s[^s]'";
        }
    } else {
        // Sólo quiero ver las centros comunes.
        //$donde="WHERE status='t' AND sf='t' AND sv='t'";
        // o (ara) no:
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
            $oForm->setCentros('all');
            $donde = "WHERE status='t'";
        } else {
            if (ConfigGlobal::mi_sfsv() == 1) {
                $oForm->setCentros('sv');
                $donde = "WHERE status='t' AND sv='t'";
            } elseif (ConfigGlobal::mi_sfsv() == 2) {
                $oForm->setCentros('sf');
                $donde = "WHERE status='t' AND sf='t'";
            }
        }
    }
}

switch ($Qtipo_lista) {
    case 'lista_activ':
        $url = ConfigGlobal::getWeb() . "/programas/centro_ajax.php";
        $parametros = "pata+'&que=lista_activ'";
        break;
    case 'datosEc':
        $url = ConfigGlobal::getWeb() . "/programas/centro_ec_ajax.php";
        $parametros = "pata+'&que=get'";
        break;
    case 'datosEcGastos':
        $url = ConfigGlobal::getWeb() . "/programas/centro_ec_ajax.php";
        $parametros = "pata+'&que=getGastos'";
        break;
    case 'ctrsEncargados':
        $url = ConfigGlobal::getWeb() . "/apps/actividades/controller/calendario_listas.php";
        $parametros = "pata+'&que=lista_cdc&ver_ctr=$Qver_ctr'";
        break;
    case 'crt':
        $url = ConfigGlobal::getWeb() . "/apps/actividades/controller/lista_centros_activ.php";
        $parametros = "pata";
        break;
    case 'cv':
        $url = ConfigGlobal::getWeb() . "/apps/actividades/controller/lista_centros_activ.php";
        $parametros = "pata";
        break;
    default:
        $url = ConfigGlobal::getWeb() . "/programas/centro_ajax.php";
        $parametros = "pata+'&que=get'";
}

$oForm->setPosiblesCentros($donde);
$oForm->setAction('');
// para seleccionar más de un centro
$aOpcionesCentros = $oForm->getPosiblesCentros();
$oSelects = new DesplegableArray('', $aOpcionesCentros, 'id_ctr');
$oSelects->setBlanco('t');
$oSelects->setAccionConjunto('fnjs_mas_centros(event)');

if ($Qperiodo == 'no') {
    if ($Qtipo_lista == 'datosEc') $oForm->setTitulo(strtoupper_dlb(_("resumen económico")));
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
    $oFormP = new web\PeriodoQue();
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


$url_ajax = ConfigGlobal::getWeb() . '/programas/centro_ajax.php';


$oHash = new web\Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('empiezamin!empiezamax!iactividad_val!iasistentes_val!id_ctr!id_ctr_mas!id_ctr_num!periodo!year');
$oHash->setCamposNo('id_ctr');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oSelects' => $oSelects,
    'Qperiodo' => $Qperiodo,
    'url' => $url,
    'parametros' => $parametros,
    'url_ajax' => $url_ajax,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
];

$oView = new core\View('actividades/controller');
echo $oView->render('actividades_centro_que.phtml', $a_campos);
