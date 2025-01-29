<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use usuarios\model\entity\Usuario;
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

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miSfsv = ConfigGlobal::mi_sfsv();

$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

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

$Qsacd = (string)filter_input(INPUT_POST, 'sacd');
$Qctr = (string)filter_input(INPUT_POST, 'ctr');
$Qtodos_n = (string)filter_input(INPUT_POST, 'todos_n');
$Qtodos_agd = (string)filter_input(INPUT_POST, 'todos_agd');
$Qtodos_s = (string)filter_input(INPUT_POST, 'todos_s');

// para formato fecha del javascript:
$locale_us = ConfigGlobal::is_locale_us();

// centros
$oHash1 = new Hash();
$oHash1->setCamposForm('sacd!ctr!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash1->setcamposNo('todos_n!todos_agd!todos_s!modelo');
$a_camposHidden1 = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
);
$oHash1->setArraycamposHidden($a_camposHidden1);

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

//cuando queramos visualizar el calendario de actividades de
//todas las personas de 1 ctr
$chk_sacd_no = 'checked';
$chk_sacd_si = '';
if (!empty($Qsacd)) {
    $chk_sacd_no = '';
    $chk_sacd_si = 'checked';
}
$chk_todos_n = '';
$chk_todos_agd = '';
$chk_todos_s = '';
if (!empty($Qtodos_n)) {
    $chk_todos_n = 'checked';
}
if (!empty($Qtodos_agd)) {
    $chk_todos_agd = 'checked';
}
if (!empty($Qtodos_s)) {
    $chk_todos_s = 'checked';
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash1' => $oHash1,
    'oFormP' => $oFormP,
    'locale_us' => $locale_us,
    'chk_sacd_no' => $chk_sacd_no,
    'chk_sacd_si' => $chk_sacd_si,
    'ctr' => $Qctr,
    'chk_todos_n' => $chk_todos_n,
    'chk_todos_agd' => $chk_todos_agd,
    'chk_todos_s' => $chk_todos_s,
];

$oView = new ViewPhtml('planning/controller');
$oView->renderizar('planning_ctr_que.phtml', $a_campos);