<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/activacion_ajax.php';

$oHashDefault = new HashFront();
$oHashDefault->setUrl($url_ajax);
$oHashDefault->setCamposForm('que');
$h_default = $oHashDefault->linkSinValParams();

$oHashMod = new HashFront();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('activacion!que!id_tipo_activ');
$h_modificar = $oHashMod->linkSinValParams();

$oHashNew = new HashFront();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que');
$h_nuevo = $oHashNew->linkSinValParams();

$oHashLista = new HashFront();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinValParams();

// Mutaciones modificar/eliminar → /src/... con linkSinValParams (hnov=1), sin hash del formulario
// completo (evita campos extra del widget ActividadTipo y el redirect a index).
$url_src_excepcion_guardar = AppUrlConfig::srcBrowserUrl('/src/pasarela/activacion_excepcion_guardar');
$oHashSrcGuardar = new HashFront();
$oHashSrcGuardar->setUrl($url_src_excepcion_guardar);
$oHashSrcGuardar->setCamposForm('id_tipo_activ!valor');
$h_src_excepcion_guardar = $oHashSrcGuardar->linkSinValParams();

$url_src_excepcion_eliminar = AppUrlConfig::srcBrowserUrl('/src/pasarela/activacion_excepcion_eliminar');
$oHashSrcEliminar = new HashFront();
$oHashSrcEliminar->setUrl($url_src_excepcion_eliminar);
$oHashSrcEliminar->setCamposForm('id_tipo_activ');
$h_src_excepcion_eliminar = $oHashSrcEliminar->linkSinValParams();

$txt_eliminar = _('¿Está seguro que quiere eliminar esta fila?');

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_default' => $h_default,
    'h_modificar' => $h_modificar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
    'url_src_excepcion_guardar' => $url_src_excepcion_guardar,
    'url_src_excepcion_eliminar' => $url_src_excepcion_eliminar,
    'h_src_excepcion_guardar' => $h_src_excepcion_guardar,
    'h_src_excepcion_eliminar' => $h_src_excepcion_eliminar,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewNewTwig('frontend\\pasarela\\controller');
$oView->renderizar('activacion_lista.html.twig', $a_campos);
