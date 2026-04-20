<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewTwig;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$webBase = rtrim(ConfigGlobal::getWeb(), '/');
$url_lista = $webBase . '/src/actividades/tipo_activ_lista';
$url_form_nuevo = $webBase . '/src/actividades/tipo_activ_form_nuevo';
$url_form_modificar = $webBase . '/src/actividades/tipo_activ_form_modificar';
$url_nuevo = $webBase . '/src/actividades/tipo_activ_nuevo';
$url_update = $webBase . '/src/actividades/tipo_activ_update';
$url_eliminar = $webBase . '/src/actividades/tipo_activ_eliminar';

$oHashLista = new Hash();
$oHashLista->setUrl($url_lista);
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();
// linkSinVal con camposForm vacio devuelve "?hnov=1&h=..."; lo convertimos en
// "&hnov=1&h=..." para poder concatenarlo a una cadena de parametros vacia.
if (!empty($h_lista) && $h_lista[0] === '?') {
    $h_lista = '&' . substr($h_lista, 1);
}

$oHashFormNuevo = new Hash();
$oHashFormNuevo->setUrl($url_form_nuevo);
$oHashFormNuevo->setCamposForm('');
$h_form_nuevo = $oHashFormNuevo->linkSinVal();
if (!empty($h_form_nuevo) && $h_form_nuevo[0] === '?') {
    $h_form_nuevo = '&' . substr($h_form_nuevo, 1);
}

$oHashFormMod = new Hash();
$oHashFormMod->setUrl($url_form_modificar);
$oHashFormMod->setCamposForm('id_tipo_activ');
$h_form_modificar = $oHashFormMod->linkSinVal();

$txt_eliminar = _("¿Está seguro que quiere eliminar este tipo de actividad?");

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_lista' => $h_lista,
    'h_form_nuevo' => $h_form_nuevo,
    'h_form_modificar' => $h_form_modificar,
    'url_lista' => $url_lista,
    'url_form_nuevo' => $url_form_nuevo,
    'url_form_modificar' => $url_form_modificar,
    'url_nuevo' => $url_nuevo,
    'url_update' => $url_update,
    'url_eliminar' => $url_eliminar,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewNewTwig('actividades/controller');
$oView->renderizar('tipo_activ.html.twig', $a_campos);
