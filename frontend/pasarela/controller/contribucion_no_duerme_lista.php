<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_ajax = $web . '/frontend/pasarela/controller/contribucion_no_duerme_ajax.php';

$oHashDefault = new HashFront();
$oHashDefault->setUrl($url_ajax);
$oHashDefault->setCamposForm('que');
$h_default = $oHashDefault->linkSinValParams();

$oHashMod = new HashFront();
$oHashMod->setUrl($url_ajax);
$oHashMod->setCamposForm('contribucion!que!id_tipo_activ');
$h_modificar = $oHashMod->linkSinValParams();

$oHashNew = new HashFront();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que');
$h_nuevo = $oHashNew->linkSinValParams();

$oHashLista = new HashFront();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinValParams();

$txt_eliminar = _('¿Está seguro que quiere eliminar esta fila?');

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_default' => $h_default,
    'h_modificar' => $h_modificar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
    'txt_eliminar' => $txt_eliminar,
];

$oView = new ViewNewTwig('frontend\\pasarela\\controller');
$oView->renderizar('contribucion_x_lista.html.twig', $a_campos);
