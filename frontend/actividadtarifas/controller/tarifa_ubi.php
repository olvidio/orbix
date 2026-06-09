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

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\CasasQue;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/actividadtarifas/helpers/actividadtarifas_support.php';

$oPosicion = FrontBootstrap::boot();
$miSfsv = OrbixRuntime::miSfsv();

$oForm = new CasasQue();
$filtro = ['active' => true];
$oPerm = actividades_o_perm();
if ($oPerm !== null && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))) {
    $oForm->setCasas('all');
} elseif ($miSfsv === 1) {
    $oForm->setCasas('sv');
    $filtro['sv'] = true;
} elseif ($miSfsv === 2) {
    $oForm->setCasas('sf');
    $filtro['sf'] = true;
}
$oForm->setFiltroCasas($filtro);
$oForm->setAction('fnjs_ver()');

$oFormAny = new PeriodoQue();
$oFormAny->setAction('fnjs_ver()');

$public = AppUrlConfig::getPublicAppBaseUrl();
$api = AppUrlConfig::getApiBaseUrl();

$oHashLista = new HashFront();
$oHashLista->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_ubi_lista.php');
$oHashLista->setCamposForm('id_ubi!year');
$h_lista = $oHashLista->linkSinVal();

// El form se pide via AJAX en dos escenarios distintos y cada uno
// envia un subconjunto diferente de parametros. Como `validatePost`
// espera que los campos firmados coincidan exactamente con los que
// llegan, firmamos una URL por escenario (igual que hacia el legacy
// `apps/actividadtarifas/controller/tarifa_ubi.php`).
$oHashFormModificar = new HashFront();
$oHashFormModificar->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_ubi_form.php');
$oHashFormModificar->setCamposForm('id_item!letra');
$h_form_modificar = $oHashFormModificar->linkSinVal();

$oHashFormNuevo = new HashFront();
$oHashFormNuevo->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_ubi_form.php');
$oHashFormNuevo->setCamposForm('id_ubi!year');
$h_form_nuevo = $oHashFormNuevo->linkSinVal();

$oHashCopiar = new HashFront();
$oHashCopiar->setUrl($api . '/src/actividadtarifas/tarifa_ubi_copiar');
$oHashCopiar->setCamposForm('id_ubi!year');
$url_copiar = $api . '/src/actividadtarifas/tarifa_ubi_copiar' . $oHashCopiar->linkSinVal();

$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($api . '/src/actividadtarifas/tarifa_ubi_update');
$oHashUpdate->setCamposForm('id_item!id_ubi!year!id_tarifa!id_serie!cantidad');
$url_update = $api . '/src/actividadtarifas/tarifa_ubi_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new HashFront();
$oHashEliminar->setUrl($api . '/src/actividadtarifas/tarifa_ubi_eliminar');
$oHashEliminar->setCamposForm('id_item');
$url_eliminar = $api . '/src/actividadtarifas/tarifa_ubi_eliminar' . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oForm' => $oForm,
    'oFormAny' => $oFormAny,
    'url_lista' => $public . '/frontend/actividadtarifas/controller/tarifa_ubi_lista.php' . $h_lista,
    'url_form_modificar' => $public . '/frontend/actividadtarifas/controller/tarifa_ubi_form.php' . $h_form_modificar,
    'url_form_nuevo' => $public . '/frontend/actividadtarifas/controller/tarifa_ubi_form.php' . $h_form_nuevo,
    'url_copiar' => $url_copiar,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => _("¿Está seguro que quiere eliminar esta tarifa?"),
    'txt_copiar' => _("¿Seguro que desea eliminar las tarifas actuales?"),
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_ubi.phtml', $a_campos);
