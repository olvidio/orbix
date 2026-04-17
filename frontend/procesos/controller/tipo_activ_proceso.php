<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewTwig;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$url_ajax = rtrim(ConfigGlobal::getWeb(), '/') . '/src/procesos/tipo_activ_proceso_ajax';

$oHashAsig = new Hash();
$oHashAsig->setUrl($url_ajax);
$oHashAsig->setCamposForm('que!id_tipo_activ!propio!id_tipo_proceso');
$h_asignar = $oHashAsig->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_ajax);
$oHashNew->setCamposForm('que!id_tipo_activ!propio');
$h_nuevo = $oHashNew->linkSinVal();

$oHashLista = new Hash();
$oHashLista->setUrl($url_ajax);
$oHashLista->setCamposForm('que');
$h_lista = $oHashLista->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_asignar' => $h_asignar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_ajax' => $url_ajax,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('tipo_activ_proceso.html.twig', $a_campos);
