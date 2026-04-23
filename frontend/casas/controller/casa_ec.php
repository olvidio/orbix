<?php
/**
 * Pantalla `casa_ec`: filtro casa y carga AJAX de la estadística
 * económica por año (5 años). Migrada desde
 * `apps/casas/controller/casa_ec_que.php`, cuyo JS delegaba en
 * `casas_resumen_ajax.php?que=get`. Ahora llama a
 * `frontend/casas/controller/casas_resumen_lista.php` con `cdc_sel=9`
 * y `que=get`.
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\usuarios\domain\value_objects\PauType;
use web\CasasQue;
use web\Hash;

use function core\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

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

$oForm->setTitulo(strtoupper_dlb((string)_("resumen económico")));
$oForm->setBoton("<input type='button' name='buscar' value='" . _('buscar') . "' onclick='fnjs_ver();'>");

$web = rtrim(ConfigGlobal::getWeb(), '/');
$url_ajax = $web . '/frontend/casas/controller/casas_resumen_lista.php';

$oHash = new Hash();
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
