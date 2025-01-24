<?php

use core\ConfigGlobal;
use usuarios\model\entity as usuarios;
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

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv = core\ConfigGlobal::mi_sfsv();

$mes = date('m');
$fin_m = $_SESSION['oConfig']->getMesFinStgr();
if ($mes > $fin_m) {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/%s hasta 31/5)"), $fin_m + 1);
} else {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/6 hasta 30/%s)"), $fin_m + 1);
}
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qna = (string)filter_input(INPUT_POST, 'na');

$Qsin_activ = (integer)filter_input(INPUT_POST, 'sin_activ');
$chk_actividad_no = 'checked';
$chk_actividad_si = '';
if (!empty($Qsin_activ && $Qsin_activ === 1)) {
    $chk_actividad_no = '';
    $chk_actividad_si = 'checked';
}

$Qcdc_sel = (integer)filter_input(INPUT_POST, 'cdc_sel');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$QsSeleccionados = (string)filter_input(INPUT_POST, 'sSeleccionados');

//personas
$oHash = new web\Hash();
$oHash->setCamposForm('nombre!apellido1!apellido2!centro!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('modelo');
$a_camposHidden = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
    'na' => $Qna
);
$oHash->setArraycamposHidden($a_camposHidden);
// centros
$oHash1 = new web\Hash();
$oHash1->setCamposForm('sacd!ctr!empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!periodo!year');
$oHash1->setcamposNo('todos_n!todos_agd!todos_s!modelo');
$a_camposHidden1 = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau,
);
$oHash1->setArraycamposHidden($a_camposHidden1);
//casas
$oHash2 = new web\Hash();
$oHash2->setCamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash2->setcamposNo('id_cdc!sin_activ!modelo');
$a_camposHidden2 = array(
    'tipo' => $Qtipo,
    'obj_pau' => $Qobj_pau
);
$oHash2->setArraycamposHidden($a_camposHidden2);

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


if ($Qtipo === 'planning_cdc') {
    $oForm = new web\CasasQue();
    $oForm->setTitulo(core\strtoupper_dlb(_("búsqueda de casas cuyo planning interesa")));
    // miro que rol tengo. Si soy casa, sólo veo la mía
    if ($oMiUsuario->isRolePau('cdc')) {
        $id_pau = $oMiUsuario->getId_pau();
        $sDonde = str_replace(",", " OR id_ubi=", $id_pau);
        //formulario para casas cuyo calendario de actividades interesa 
        $donde = "WHERE status='t' AND (id_ubi=$sDonde)";
        $oForm->setCasas('casa');
    } else {
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
            $oForm->setCasas('all');
            $donde = "WHERE status='t'";
        } else {
            if ($miSfsv == 1) {
                $oForm->setCasas('sv');
                $donde = "WHERE status='t' AND sv='t'";
            }
            if ($miSfsv == 2) {
                $oForm->setCasas('sf');
                $donde = "WHERE status='t' AND sf='t'";
            }
        }
    }
    $oForm->setPosiblesCasas($donde);
    $oForm->setCdcSel($Qcdc_sel);
    $oForm->setSeleccionados($QsSeleccionados);

    $a_campos = ['oPosicion' => $oPosicion,
        'oHash2' => $oHash2,
        'oFormP' => $oFormP,
        'oForm' => $oForm,
        'locale_us' => ConfigGlobal::is_locale_us(),
        'chk_actividad_no' => $chk_actividad_no,
        'chk_actividad_si' => $chk_actividad_si,
    ];

    $oView = new core\View('actividades/controller');
    $oView->renderizar('planning_casa_que.phtml', $a_campos);
} else {
    $err_switch = sprintf(_("opción no definida en %s, linea %s"), __FILE__, __LINE__);
    exit ($err_switch);
}