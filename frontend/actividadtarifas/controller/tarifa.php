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

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$web = rtrim(ConfigGlobal::getWeb(), '/');

$oHashLista = new Hash();
$oHashLista->setUrl($web . '/frontend/actividadtarifas/controller/tarifa_lista.php');
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();

$oHashForm = new Hash();
$oHashForm->setUrl($web . '/frontend/actividadtarifas/controller/tarifa_form.php');
$oHashForm->setCamposForm('id_tarifa');
$h_form = $oHashForm->linkSinVal();

$oHashUpdate = new Hash();
$oHashUpdate->setUrl($web . '/src/actividadtarifas/tipo_tarifa_update');
$oHashUpdate->setCamposForm('id_tarifa!letra!modo!observ');
$url_update = $web . '/src/actividadtarifas/tipo_tarifa_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new Hash();
$oHashEliminar->setUrl($web . '/src/actividadtarifas/tipo_tarifa_eliminar');
$oHashEliminar->setCamposForm('id_tarifa');
$url_eliminar = $web . '/src/actividadtarifas/tipo_tarifa_eliminar' . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_lista' => $web . '/frontend/actividadtarifas/controller/tarifa_lista.php',
    'h_lista' => $h_lista,
    'url_form' => $web . '/frontend/actividadtarifas/controller/tarifa_form.php',
    'h_form' => $h_form,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => _("¿Está seguro de borrar esta tarifa?"),
];

$oView = new ViewNewPhtml('frontend\\actividadtarifas\\controller');
$oView->renderizar('tarifa.phtml', $a_campos);
