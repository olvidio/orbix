<?php

namespace frontend\planning\controller;

use frontend\planning\support\PeriodoPlanningHelper;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;
use frontend\shared\web\CasasQue;
use web\Hash;
use frontend\shared\web\Posicion;
use function src\shared\domain\helpers\strtoupper_dlb;

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

$oMiUsuario = $_SESSION['session_auth']['MiUsuario'];
$oRole = new Role();
$oRole->setId_role($oMiUsuario->getId_role());
$miSfsv = OrbixRuntime::miSfsv();

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

$filtro = ['active' => true];
if ($oRole->isRolePau(PauType::PAU_CDC)) {
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $filtro['id_ubi_in'] = array_values(array_filter(array_map('intval', explode(',', (string)$id_pau)), static fn ($v) => $v > 0));
    $oForm->setCasas('casa');
} elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
} elseif ($miSfsv === 1) {
    $oForm->setCasas('sv');
    $filtro['sv'] = true;
} elseif ($miSfsv === 2) {
    $oForm->setCasas('sf');
    $filtro['sf'] = true;
}
$oForm->setFiltroCasas($filtro);
$oForm->setCdcSel($Qcdc_sel);
$oForm->setSeleccionados($QsSeleccionados);

$urlSelect = 'frontend/planning/controller/planning_casa_select.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'propuesta_calendario' => $Qpropuesta_calendario,
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'chk_actividad_no' => $chk_actividad_no,
    'chk_actividad_si' => $chk_actividad_si,
    'urlSelect' => $urlSelect,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_casa_que.phtml', $a_campos);
