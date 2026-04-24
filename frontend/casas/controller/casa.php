<?php
/**
 * Pantalla principal del módulo `casas` — filtro casa + periodo y
 * delegación a distintas vistas AJAX según `tipo_lista`:
 *
 * - default               → listado económico (`casa_ingresos_lista.php`)
 * - `lista_activ`         → listado de actividades (`casa_actividades_lista.php`)
 * - `ctrsEncargados`      → `frontend/actividades/controller/calendario_listas.php`
 * - `datosEcGastos`       → `frontend/casas/controller/casa_ec_gastos_lista.php`
 *
 * Migrada desde `apps/casas/controller/casa_que.php` +
 * `casa_ajax.php` siguiendo `refactor.md`.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\value_objects\PauType;
use web\CasasQue;
use web\Hash;
use web\PeriodoQue;

use function core\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qtipo_lista = (string)filter_input(INPUT_POST, 'tipo_lista');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

if ($Qtipo_lista === 'datosEcGastos') {
    $Qperiodo = 'ninguno';
}

$oForm = new CasasQue();
$oMiUsuario = ConfigGlobal::MiUsuario();
$miRolePau = ConfigGlobal::mi_role_pau();
if ($miRolePau === PauType::PAU_CDC) {
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $sDonde = str_replace(',', ' OR id_ubi=', $id_pau);
    $donde = "WHERE active='t' AND (id_ubi=$sDonde)";
    $oForm->setCasas('casa');
} elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
    $donde = "WHERE active='t'";
} elseif (ConfigGlobal::mi_sfsv() === 1) {
    $oForm->setCasas('sv');
    $donde = "WHERE active='t' AND sv='t'";
} elseif (ConfigGlobal::mi_sfsv() === 2) {
    $oForm->setCasas('sf');
    $donde = "WHERE active='t' AND sf='t'";
} else {
    $donde = "WHERE active='t'";
}
$oForm->setPosiblesCasas($donde);
$oForm->setAction('');

$oSelects = $oForm->getDesplCasas();
$oSelects->setAction('');
$oSelects->setAccionConjunto('fnjs_mas_casas(event)');

$oFormP = null;
if ($Qperiodo === 'no') {
    if ($Qtipo_lista === 'datosEc') {
        $oForm->setTitulo(strtoupper_dlb((string)_("resumen económico")));
    }
    $oForm->setBoton("<input type='button' name='buscar' value='" . _('buscar') . "' onclick='fnjs_ver();'>");
} else {
    if ($Qperiodo === 'ninguno') {
        $aOpciones = ['ninguno' => _('ninguno')];
    } else {
        $aOpciones = [
            'tot_any' => _('todo el año'),
            'trimestre_1' => _('primer trimestre'),
            'trimestre_2' => _('segundo trimestre'),
            'trimestre_3' => _('tercer trimestre'),
            'trimestre_4' => _('cuarto trimestre'),
            'separador' => '---------',
            'otro' => _('otro'),
        ];
    }
    $oFormP = new PeriodoQue();
    $oFormP->setFormName('seleccion');
    $oFormP->setTitulo(strtoupper_dlb((string)_("seleccionar una casa y un período")));
    $oFormP->setPosiblesPeriodos($aOpciones);
    if ($Qyear !== 0) {
        $oFormP->setDesplAnysOpcion_sel($Qyear);
    }
    $oFormP->setAntes($oSelects->ListaSelects());
    $oFormP->setBoton("<input type='button' name='buscar' value='" . _('buscar') . "' onclick='fnjs_ver();'>");
}

$web = rtrim(ConfigGlobal::getWeb(), '/');

$sCamposForm = 'que!id_cdc!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!year';

switch ($Qtipo_lista) {
    case 'lista_activ':
        $url_ajax = $web . '/frontend/casas/controller/casa_actividades_lista.php';
        $sCamposForm .= '!periodo';
        break;
    case 'ctrsEncargados':
        $Qver_ctr = (string)filter_input(INPUT_POST, 'ver_ctr');
        $url_ajax = $web . '/frontend/actividades/controller/calendario_listas.php?que=lista_cdc&ver_ctr=' . urlencode($Qver_ctr);
        $sCamposForm .= '!periodo!ver_ctr';
        break;
    case 'datosEcGastos':
        $url_ajax = $web . '/frontend/casas/controller/casa_ec_gastos_lista.php';
        break;
    default:
        $url_ajax = $web . '/frontend/casas/controller/casa_ingresos_lista.php';
        $sCamposForm .= '!periodo';
}

$oHash = new Hash();
$oHash->setCamposForm($sCamposForm);

$oHashForm = new Hash();
$oHashForm->setUrl($web . '/frontend/casas/controller/casa_ingreso_form.php');
$oHashForm->setCamposForm('id_activ');
$url_form = $web . '/frontend/casas/controller/casa_ingreso_form.php' . $oHashForm->linkSinVal();

$oHashUpdate = new Hash();
$oHashUpdate->setUrl($web . '/src/casas/casa_ingreso_update');
$oHashUpdate->setCamposForm('id_activ!id_tarifa!precio!ingresos!num_asistentes!observ');
$url_update = $web . '/src/casas/casa_ingreso_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new Hash();
$oHashEliminar->setUrl($web . '/src/casas/casa_ingreso_eliminar');
$oHashEliminar->setCamposForm('id_activ');
$url_eliminar = $web . '/src/casas/casa_ingreso_eliminar' . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'url_form' => $url_form,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'oForm' => $oForm,
    'oFormP' => $oFormP,
    'oSelects' => $oSelects,
    'periodo' => $Qperiodo,
    'id_ubi' => $Qid_ubi,
    'txt_eliminar' => (string)_("¿Está seguro de borrar los datos económicos de esta actividad?"),
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('casa.phtml', $a_campos);
