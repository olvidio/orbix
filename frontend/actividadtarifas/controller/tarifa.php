<?php
/**
 * Pantalla principal del modulo `actividadtarifas` - catalogo de
 * `TipoTarifa`.
 *
 * La pantalla se limita a renderizar el shell HTML + JS; el listado y
 * el formulario se cargan via AJAX desde
 * `frontend/actividadtarifas/controller/tarifa_lista.php` y
 * `tarifa_form.php`, que a su vez consumen los endpoints JSON
 * `/src/actividadtarifas/tipo_tarifa_lista_data` y
 * `tipo_tarifa_form_data`. Las mutaciones (`tipo_tarifa_update` /
 * `tipo_tarifa_eliminar`) se invocan directamente desde JS.
 *
 * Migrada desde `apps/actividadtarifas/controller/tarifa.php` +
 * `tarifa_ajax.php` (dispatcher legacy) siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$public = AppUrlConfig::getPublicAppBaseUrl();
$api = AppUrlConfig::getApiBaseUrl();

$oHashLista = new HashFront();
$oHashLista->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_lista.php');
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();

$oHashForm = new HashFront();
$oHashForm->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_form.php');
$oHashForm->setCamposForm('id_tarifa');
$h_form = $oHashForm->linkSinVal();

$oHashUpdate = new HashFront();
$oHashUpdate->setUrl(AppUrlConfig::srcBrowserUrl('/src/actividadtarifas/tipo_tarifa_update'));
$oHashUpdate->setCamposForm('id_tarifa!letra!modo!observ');
$url_update = AppUrlConfig::srcBrowserUrl('/src/actividadtarifas/tipo_tarifa_update') . $oHashUpdate->linkSinVal();

$oHashEliminar = new HashFront();
$oHashEliminar->setUrl(AppUrlConfig::srcBrowserUrl('/src/actividadtarifas/tipo_tarifa_eliminar'));
$oHashEliminar->setCamposForm('id_tarifa');
$url_eliminar = AppUrlConfig::srcBrowserUrl('/src/actividadtarifas/tipo_tarifa_eliminar') . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_lista' => $public . '/frontend/actividadtarifas/controller/tarifa_lista.php',
    'h_lista' => $h_lista,
    'url_form' => $public . '/frontend/actividadtarifas/controller/tarifa_form.php',
    'h_form' => $h_form,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => _("¿Está seguro de borrar esta tarifa?"),
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa.phtml', $a_campos);
