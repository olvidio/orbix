<?php
/**
 * Pantalla principal del módulo `casas` - grupos de casas (padre ↔ hijo).
 *
 * Renderiza el shell HTML + JS. Lista y form se cargan via AJAX desde
 * `grupo_lista.php` y `grupo_form.php`. Las mutaciones llaman
 * directamente a `/src/casas/grupo_update` y `/src/casas/grupo_eliminar`.
 *
 * Migrada desde `apps/casas/controller/grupo_lista.php` +
 * `grupo_form.php` + `grupo_ajax.php` siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$web = AppUrlConfig::getPublicAppBaseUrl();

$oHashLista = new HashFront();
$oHashLista->setUrl($web . '/frontend/casas/controller/grupo_lista.php');
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();

$oHashForm = new HashFront();
$oHashForm->setUrl($web . '/frontend/casas/controller/grupo_form.php');
$oHashForm->setCamposForm('id_item');
$h_form = $oHashForm->linkSinVal();

$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($web . '/src/casas/grupo_update');
$oHashUpdate->setCamposForm('id_item!id_ubi_padre!id_ubi_hijo');
$url_update = $web . '/src/casas/grupo_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new HashFront();
$oHashEliminar->setUrl($web . '/src/casas/grupo_eliminar');
$oHashEliminar->setCamposForm('id_item');
$url_eliminar = $web . '/src/casas/grupo_eliminar' . $oHashEliminar->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'url_lista' => $web . '/frontend/casas/controller/grupo_lista.php' . $h_lista,
    'url_form' => $web . '/frontend/casas/controller/grupo_form.php' . $h_form,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => _("¿Está seguro?"),
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('grupo.phtml', $a_campos);
