<?php

namespace frontend\planning\controller;

use core\ConfigGlobal;
use frontend\planning\support\PeriodoPlanningHelper;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use web\CasasQue;
use web\Hash;
use web\Posicion;
use function core\strtoupper_dlb;

/**
 * Formulario de filtros para el planning por casas (se selecciona el
 * grupo de casas y el periodo).
 *
 * Migrado desde `apps/planning/controller/planning_casa_que.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once('frontend/shared/global_header_front.inc');
require_once('apps/core/global_object.inc');

/** @var Posicion $oPosicion */
$oPosicion->recordar();

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '' && $stack !== null) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack((int)$stack)) {
            $oPosicion2->olvidar((int)$stack);
        }
    }
}

$oMiUsuario = ConfigGlobal::MiUsuario();
$oRole = new Role();
$oRole->setId_role($oMiUsuario->getId_role());
$miSfsv = ConfigGlobal::mi_sfsv();

$periodo_txt = PeriodoPlanningHelper::textoPeriodoPorDefecto((int)$_SESSION['oConfig']->getMesFinStgr());

$Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
$Qsin_activ = (int)filter_input(INPUT_POST, 'sin_activ');
$chk_actividad_no = $Qsin_activ === 1 ? '' : 'checked';
$chk_actividad_si = $Qsin_activ === 1 ? 'checked' : '';

$Qcdc_sel = (int)filter_input(INPUT_POST, 'cdc_sel');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$QsSeleccionados = (string)filter_input(INPUT_POST, 'sSeleccionados');

$oHash = new Hash();
$oHash->setCamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!modelo!periodo!sin_activ!year');
$oHash->setcamposNo('id_cdc');
$oHash->setArraycamposHidden([
    'propuesta_calendario' => $Qpropuesta_calendario,
]);

$oFormP = PeriodoPlanningHelper::formPeriodo($Qperiodo, $Qyear, $Qempiezamin, $Qempiezamax);

$oForm = new CasasQue();
$oForm->setTitulo(strtoupper_dlb(_("búsqueda de casas cuyo planning interesa")));

$donde = '';
if ($oRole->isRolePau(PauType::PAU_CDC)) {
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $donde = "WHERE active='t' AND id_ubi IN ($id_pau)";
    $oForm->setCasas('casa');
} elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
    $donde = "WHERE active='t'";
} elseif ($miSfsv === 1) {
    $oForm->setCasas('sv');
    $donde = "WHERE active='t' AND sv='t'";
} elseif ($miSfsv === 2) {
    $oForm->setCasas('sf');
    $donde = "WHERE active='t' AND sf='t'";
}
$oForm->setPosiblesCasas($donde);
$oForm->setCdcSel($Qcdc_sel);
$oForm->setSeleccionados($QsSeleccionados);

$urlSelect = 'frontend/planning/controller/planning_casa_select.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'propuesta_calendario' => $Qpropuesta_calendario,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'locale_us' => ConfigGlobal::is_locale_us(),
    'chk_actividad_no' => $chk_actividad_no,
    'chk_actividad_si' => $chk_actividad_si,
    'urlSelect' => $urlSelect,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_casa_que.phtml', $a_campos);
