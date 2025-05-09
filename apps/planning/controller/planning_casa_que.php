<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use src\usuarios\domain\entity\Role;
use web\CasasQue;
use web\Hash;
use web\PeriodoQue;
use web\Posicion;
use function core\strtoupper_dlb;

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
$oRole = new Role();
$oRole->setId_role($oMiUsuario->getId_role());
$miSfsv = ConfigGlobal::mi_sfsv();

$mes = date('m');
$fin_m = $_SESSION['oConfig']->getMesFinStgr();
if ($mes > $fin_m) {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/%s hasta 31/5)"), $fin_m + 1);
} else {
    $periodo_txt = sprintf(_("(por defecto: periodo desde 1/6 hasta 30/%s)"), $fin_m + 1);
}
$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');

$Qsin_activ = (integer)filter_input(INPUT_POST, 'sin_activ');
$chk_actividad_no = 'checked';
$chk_actividad_si = '';
if ($Qsin_activ === 1) {
    $chk_actividad_no = '';
    $chk_actividad_si = 'checked';
}

$Qcdc_sel = (integer)filter_input(INPUT_POST, 'cdc_sel');
$Qyear = (integer)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$QsSeleccionados = (string)filter_input(INPUT_POST, 'sSeleccionados');

//casas
$oHash = new Hash();
$oHash->setCamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!modelo!periodo!sin_activ!year');
$oHash->setcamposNo('id_cdc');
$a_camposHidden2 = array(
    'propuesta_calendario' => $Qpropuesta_calendario,
);
$oHash->setArraycamposHidden($a_camposHidden2);

$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(strtoupper_dlb(_("periodo del planning actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
if (empty($Qyear)) {
    $Qyear = date("Y");
}
$oFormP->setDesplAnysOpcion_sel($Qyear);

$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);


$oForm = new CasasQue();
$oForm->setTitulo(strtoupper_dlb(_("búsqueda de casas cuyo planning interesa")));
// miro que rol tengo. Si soy casa, sólo veo la mía
$donde = '';
if ($oRole->isRolePau(Role::PAU_CDC)) { //casa
    $id_pau = $oMiUsuario->getId_pau(); //pueden ser varios separados por comas
    //$sDonde = str_replace(",", " OR id_ubi=", $id_pau);
    //$donde = "WHERE status='t' AND (id_ubi=$sDonde)";
    //formulario para casas cuyo calendario de actividades interesa
    $donde = "WHERE status='t' AND id_ubi IN ($id_pau)";
    $oForm->setCasas('casa');
} else {
    if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
        $oForm->setCasas('all');
        $donde = "WHERE status='t'";
    } else {
        if ($miSfsv === 1) {
            $oForm->setCasas('sv');
            $donde = "WHERE status='t' AND sv='t'";
        }
        if ($miSfsv === 2) {
            $oForm->setCasas('sf');
            $donde = "WHERE status='t' AND sf='t'";
        }
    }
}
$oForm->setPosiblesCasas($donde);
$oForm->setCdcSel($Qcdc_sel);
$oForm->setSeleccionados($QsSeleccionados);

$a_campos = ['oPosicion' => $oPosicion,
    'propuesta_calendario' => $Qpropuesta_calendario,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'locale_us' => ConfigGlobal::is_locale_us(),
    'chk_actividad_no' => $chk_actividad_no,
    'chk_actividad_si' => $chk_actividad_si,
];

$oView = new ViewPhtml('planning/controller');
$oView->renderizar('planning_casa_que.phtml', $a_campos);