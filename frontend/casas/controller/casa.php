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

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\CasasQue;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;

use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qtipo_lista = (string)filter_input(INPUT_POST, 'tipo_lista');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

if ($Qtipo_lista === 'datosEcGastos') {
    $Qperiodo = 'ninguno';
}

$oForm = new CasasQue();
$miRolePau = OrbixRuntime::miRolePau();
$filtro = ['active' => true];
// PauType::PAU_CDC (literal 'cdc').
if ($miRolePau === 'cdc') {
    $id_pau = casas_mi_usuario_csv_id_pau();
    $filtro['id_ubi_in'] = array_values(array_filter(array_map('intval', explode(',', $id_pau)), static fn ($v) => $v > 0));
    $oForm->setCasas('casa');
} elseif (actividades_have_perm_oficina('des') || actividades_have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
} elseif (OrbixRuntime::miSfsv() === 1) {
    $oForm->setCasas('sv');
    $filtro['sv'] = true;
} elseif (OrbixRuntime::miSfsv() === 2) {
    $oForm->setCasas('sf');
    $filtro['sf'] = true;
}
$oForm->setFiltroCasas($filtro);
$oForm->setAction('');

$oSelects = $oForm->getSelects();

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
        $oFormP->setDesplAnysOpcion_sel(casas_periodo_year_sel($Qyear));
    }
    $oFormP->setAntes($oSelects->ListaSelects());
    $oFormP->setBoton("<input type='button' name='buscar' value='" . _('buscar') . "' onclick='fnjs_ver();'>");
}

$web = AppUrlConfig::getPublicAppBaseUrl();

// Solo nombres que realmente envía `#seleccion` (serialize): no hay `que`, `tipo_lista` ni radios `cdc_sel`
// en esta vista (solo DesplegableArray `id_cdc[*]` + periodo/year/fechas + hidden del hash).
$sCamposForm = 'id_cdc!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!year';

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

$oHash = new HashFront();
$oHash->setCamposForm($sCamposForm);

$oHashForm = new HashFront();
$oHashForm->setUrl($web . '/frontend/casas/controller/casa_ingreso_form.php');
$oHashForm->setCamposForm('id_activ');
$url_form = $web . '/frontend/casas/controller/casa_ingreso_form.php' . $oHashForm->linkSinVal();

$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($web . '/src/casas/casa_ingreso_update');
$oHashUpdate->setCamposForm('id_activ!id_tarifa!precio!ingresos!num_asistentes!observ');
$url_update = $web . '/src/casas/casa_ingreso_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new HashFront();
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
