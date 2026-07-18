<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/nombre_ajax.php';

$oHashMod = new HashFront();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('que!id_tipo_activ!nombre_actividad');
$h_modificar = $oHashMod->linkSinValParams();

$oHashNew = new HashFront();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que');
$h_nuevo = $oHashNew->linkSinValParams();

$oHashLista = new HashFront();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinValParams();

// Alta (form_nuevo): POST mínimo a /src/... + linkSinValParams (sin hash del formulario con ActividadTipo).
$url_src_nombre_guardar = AppUrlConfig::srcBrowserUrl('/src/pasarela/nombre_excepcion_guardar');
$oHashSrcNombreGuardar = new HashFront();
$oHashSrcNombreGuardar->setUrl($url_src_nombre_guardar);
$oHashSrcNombreGuardar->setCamposForm('id_tipo_activ!valor');
$h_src_nombre_guardar = $oHashSrcNombreGuardar->linkSinValParams();

$txt_eliminar = _('¿Está seguro que quiere eliminar esta fila?');

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_modificar' => $h_modificar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
    'url_src_nombre_guardar' => $url_src_nombre_guardar,
    'h_src_nombre_guardar' => $h_src_nombre_guardar,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewNewTwig('frontend\\pasarela\\controller');
$oView->renderizar('nombre_lista.html.twig', $a_campos);
