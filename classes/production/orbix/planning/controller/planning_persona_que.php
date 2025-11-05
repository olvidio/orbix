<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;
use web\Posicion;

/**
 * Página que presentará los formularios de los distintos plannings
 * Según sea el submenú seleccionado seleccionará el formulario
 * correspondiente
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Josep Companys
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once('apps/core/global_header.inc');
// Archivos requeridos por esta url **********************************************


// Crea los objetos de uso global **********************************************
require_once('apps/core/global_object.inc');
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
//Si vengo de vuelta y le paso la referencia del stack donde está la información.
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$oMiUsuario = ConfigGlobal::MiUsuario();
$miSfsv = ConfigGlobal::mi_sfsv();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = (string)filter_input(INPUT_POST, 'na');

$mes = date('m');
$fin_m = $_SESSION['oConfig']->getMesFinStgr();
if ($mes > $fin_m) {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/%s hasta 31/5)"), $fin_m + 1);
} else {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/6 hasta 30/%s)"), $fin_m + 1);
}
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

// para formato fecha del javascript:
$locale_us = ConfigGlobal::is_locale_us();

//personas
$oHash = new Hash();
$oHash->setCamposForm('nombre!apellido1!apellido2!centro!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('modelo');
$a_camposHidden = array(
    'obj_pau' => $Qobj_pau,
    'na' => $Qna
);
$oHash->setArraycamposHidden($a_camposHidden);

$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo del planning actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
if (empty($Qyear)) {
    $Qyear = date("Y");
}
$oFormP->setDesplAnysOpcion_sel($Qyear);

$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$personas_txt = '';
switch ($Qobj_pau) {
    case 'PersonaN':
        $personas_txt = _("numerarios");
        break;
    case 'PersonaNax':
        $personas_txt = _("nax");
        break;
    case 'PersonaAgd':
        $personas_txt = _("agregados");
        break;
    case 'PersonaS':
        $personas_txt = _("supernumerarios");
        break;
    case 'PersonaSSSC':
        $personas_txt = _("de la sss+");
        break;
    case 'PersonaDl':
        $personas_txt = _("de la dl");
        break;
    case 'PersonaEx':
        $personas_txt = _("de paso");
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'personas_txt' => $personas_txt,
    'locale_us' => $locale_us,
];

$oView = new ViewPhtml('planning\controller');
$oView->renderizar('planning_persona_que.phtml', $a_campos);