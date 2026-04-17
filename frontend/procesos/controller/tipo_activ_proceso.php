<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewTwig;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$webBase = rtrim(ConfigGlobal::getWeb(), '/');
$url_lista = $webBase . '/src/procesos/tipo_activ_proceso_lista';
$url_lst_posibles = $webBase . '/src/procesos/tipo_activ_proceso_lst_posibles';
$url_asignar = $webBase . '/src/procesos/tipo_activ_proceso_asignar';

$oHashAsig = new Hash();
$oHashAsig->setUrl($url_asignar);
$oHashAsig->setCamposForm('id_tipo_activ!propio!id_tipo_proceso');
$h_asignar = $oHashAsig->linkSinVal();

$oHashNew = new Hash();
$oHashNew->setUrl($url_lst_posibles);
$oHashNew->setCamposForm('id_tipo_activ!propio');
$h_nuevo = $oHashNew->linkSinVal();

$oHashLista = new Hash();
$oHashLista->setUrl($url_lista);
$oHashLista->setCamposForm('');
$h_lista = $oHashLista->linkSinVal();
// linkSinVal con camposForm vacio devuelve "?hnov=1&h=...": lo convertimos
// en "&hnov=1&h=..." para poder concatenarlo tras unos parametros vacios.
if (!empty($h_lista) && $h_lista[0] === '?') {
    $h_lista = '&' . substr($h_lista, 1);
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'h_asignar' => $h_asignar,
    'h_nuevo' => $h_nuevo,
    'h_lista' => $h_lista,
    'url_lista' => $url_lista,
    'url_lst_posibles' => $url_lst_posibles,
    'url_asignar' => $url_asignar,
];

$oView = new ViewNewTwig('procesos/controller');
$oView->renderizar('tipo_activ_proceso.html.twig', $a_campos);
