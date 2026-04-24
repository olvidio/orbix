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

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$web = rtrim(ConfigGlobal::getWeb(), '/');

$oHashLista = new Hash();
$oHashLista->setUrl($web . '/frontend/casas/controller/grupo_lista.php');
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();

$oHashForm = new Hash();
$oHashForm->setUrl($web . '/frontend/casas/controller/grupo_form.php');
$oHashForm->setCamposForm('id_item');
$h_form = $oHashForm->linkSinVal();

$oHashUpdate = new Hash();
$oHashUpdate->setUrl($web . '/src/casas/grupo_update');
$oHashUpdate->setCamposForm('id_item!id_ubi_padre!id_ubi_hijo');
$url_update = $web . '/src/casas/grupo_update' . $oHashUpdate->linkSinVal();

$oHashEliminar = new Hash();
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
