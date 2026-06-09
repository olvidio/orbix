<?php
/**
 * Pantalla principal del modulo `actividadtarifas` - relacion
 * `TipoTarifa` ↔ tipo de actividad (`RelacionTarifaTipoActividad`).
 *
 * Renderiza el shell HTML + JS. Lista y form se cargan via AJAX desde
 * `tarifa_tipo_actividad_lista.php` y `tarifa_tipo_actividad_form.php`.
 * Las mutaciones llaman directamente a
 * `/src/actividadtarifas/relacion_tarifa_*`.
 *
 * Migrada desde
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad.php` +
 * `tarifa_tipo_actividad_ajax.php` siguiendo `refactor.md`.
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
$oHashLista->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php');
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();

$oHashForm = new HashFront();
$oHashForm->setUrl($public . '/frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php');
$oHashForm->setCamposForm('id_item');
$h_form = $oHashForm->linkSinVal();

$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($api . '/src/actividadtarifas/relacion_tarifa_update');
$oHashUpdate->setCamposForm('id_item!id_tarifa!id_tipo_activ');
$url_update = $api . '/src/actividadtarifas/relacion_tarifa_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new HashFront();
$oHashEliminar->setUrl($api . '/src/actividadtarifas/relacion_tarifa_eliminar');
$oHashEliminar->setCamposForm('id_item');
$url_eliminar = $api . '/src/actividadtarifas/relacion_tarifa_eliminar' . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_lista' => $public . '/frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php' . $h_lista,
    'url_form' => $public . '/frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php' . $h_form,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => _("¿Está seguro que desea quitar esta id_tarifa?"),
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa_tipo_actividad.phtml', $a_campos);
