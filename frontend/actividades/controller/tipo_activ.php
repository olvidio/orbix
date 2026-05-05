<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;

require_once("frontend/shared/global_header_front.inc");

$webBase = AppUrlConfig::getPublicAppBaseUrl();
$url_lista = $webBase . '/src/actividades/tipo_activ_lista';
$url_form_nuevo = $webBase . '/src/actividades/tipo_activ_form_nuevo';
$url_form_modificar = $webBase . '/src/actividades/tipo_activ_form_modificar';
$url_nuevo = $webBase . '/src/actividades/tipo_activ_nuevo';
$url_update = $webBase . '/src/actividades/tipo_activ_update';
$url_eliminar = $webBase . '/src/actividades/tipo_activ_eliminar';

$oHashLista = new HashFront();
$oHashLista->setUrl($url_lista);
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinValParams();

$oHashFormNuevo = new HashFront();
$oHashFormNuevo->setUrl($url_form_nuevo);
$oHashFormNuevo->setCamposForm('');
$h_form_nuevo = $oHashFormNuevo->linkSinValParams();

$oHashFormMod = new HashFront();
$oHashFormMod->setUrl($url_form_modificar);
$oHashFormMod->setCamposForm('id_tipo_activ');
$h_form_modificar = $oHashFormMod->linkSinValParams();

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

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('tipo_activ.html.twig', $a_campos);
