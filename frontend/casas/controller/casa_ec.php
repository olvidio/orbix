<?php
/**
 * Pantalla `casa_ec`: filtro casa y carga AJAX de la estadística
 * económica por año (5 años). Migrada desde
 * `apps/casas/controller/casa_ec_que.php`, cuyo JS delegaba en
 * `casas_resumen_ajax.php?que=get`. Ahora llama a
 * `frontend/casas/controller/casas_resumen_lista.php` con `cdc_sel=9`
 * y `que=get`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\CasasQue;
use frontend\shared\security\HashFront;

use function frontend\shared\helpers\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$oForm = new CasasQue();
$oMiUsuario = $_SESSION['session_auth']['MiUsuario'];
$miRolePau = OrbixRuntime::miRolePau();
$filtro = ['active' => true];
// PauType::PAU_CDC (literal 'cdc').
if ($miRolePau === 'cdc') {
    $id_pau = $oMiUsuario->getCsv_id_pau();
    $filtro['id_ubi_in'] = array_values(array_filter(array_map('intval', explode(',', (string)$id_pau)), static fn ($v) => $v > 0));
    $oForm->setCasas('casa');
} elseif ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
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

$oSelects = $oForm->getDesplCasas();
$oSelects->setAction('');
$oSelects->setAccionConjunto('fnjs_mas_casas(event)');

$oForm->setTitulo(strtoupper_dlb((string)_("resumen económico")));
$oForm->setBoton("<input type='button' name='buscar' value='" . _('buscar') . "' onclick='fnjs_ver();'>");

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/casas/controller/casas_resumen_lista.php';

$oHash = new HashFront();
$sCamposForm = 'cdc_sel!id_cdc!id_cdc_mas!id_cdc_num!que';
$oHash->setCamposForm($sCamposForm);

$param = 'cdc_sel=9&que=get';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_ajax' => $url_ajax,
    'param' => $param,
    'oForm' => $oForm,
    'oSelects' => $oSelects,
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('casa_ec.phtml', $a_campos);
