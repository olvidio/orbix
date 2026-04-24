<?php
/**
 * Pantalla principal del modulo `actividadtarifas` - tarifas por casa
 * y año (`TarifaUbi`).
 *
 * Renderiza el form de filtros (desplegable de casas + año) y el shell
 * JS. El listado y el form se cargan via AJAX contra
 * `frontend/actividadtarifas/controller/tarifa_ubi_lista.php` +
 * `tarifa_ubi_form.php`. Las mutaciones llaman directamente a
 * `/src/actividadtarifas/tarifa_ubi_*`.
 *
 * Migrada desde `apps/actividadtarifas/controller/tarifa_ubi.php` +
 * `tarifa_ajax.php` (dispatcher legacy) siguiendo `refactor.md`.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\CasasQue;
use web\Hash;
use web\PeriodoQue;

require_once 'frontend/shared/global_header_front.inc';

$miSfsv = ConfigGlobal::mi_sfsv();

$oForm = new CasasQue();
if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
    $oForm->setCasas('all');
    $donde = "WHERE active='t'";
} elseif ($miSfsv === 1) {
    $oForm->setCasas('sv');
    $donde = "WHERE active='t' AND sv='t'";
} elseif ($miSfsv === 2) {
    $oForm->setCasas('sf');
    $donde = "WHERE active='t' AND sf='t'";
} else {
    $donde = "WHERE active='t'";
}
$oForm->setPosiblesCasas($donde);
$oForm->setAction('fnjs_ver()');

$oFormAny = new PeriodoQue();
$oFormAny->setAction('fnjs_ver()');

$web = rtrim(ConfigGlobal::getWeb(), '/');

$oHashLista = new Hash();
$oHashLista->setUrl($web . '/frontend/actividadtarifas/controller/tarifa_ubi_lista.php');
$oHashLista->setCamposForm('id_ubi!year');
$h_lista = $oHashLista->linkSinVal();

$oHashForm = new Hash();
$oHashForm->setUrl($web . '/frontend/actividadtarifas/controller/tarifa_ubi_form.php');
$oHashForm->setCamposForm('id_item!id_ubi!year!letra');
$h_form = $oHashForm->linkSinVal();

$oHashCopiar = new Hash();
$oHashCopiar->setUrl($web . '/src/actividadtarifas/tarifa_ubi_copiar');
$oHashCopiar->setCamposForm('id_ubi!year');
$url_copiar = $web . '/src/actividadtarifas/tarifa_ubi_copiar' . $oHashCopiar->linkSinVal();

$oHashUpdate = new Hash();
$oHashUpdate->setUrl($web . '/src/actividadtarifas/tarifa_ubi_update');
$oHashUpdate->setCamposForm('id_item!id_ubi!year!id_tarifa!id_serie!cantidad');
$url_update = $web . '/src/actividadtarifas/tarifa_ubi_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new Hash();
$oHashEliminar->setUrl($web . '/src/actividadtarifas/tarifa_ubi_eliminar');
$oHashEliminar->setCamposForm('id_item');
$url_eliminar = $web . '/src/actividadtarifas/tarifa_ubi_eliminar' . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oForm' => $oForm,
    'oFormAny' => $oFormAny,
    'url_lista' => $web . '/frontend/actividadtarifas/controller/tarifa_ubi_lista.php' . $h_lista,
    'url_form' => $web . '/frontend/actividadtarifas/controller/tarifa_ubi_form.php' . $h_form,
    'url_copiar' => $url_copiar,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => _("¿Está seguro que quiere eliminar esta tarifa?"),
    'txt_copiar' => _("¿Seguro que desea eliminar las tarifas actuales?"),
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_ubi.phtml', $a_campos);
