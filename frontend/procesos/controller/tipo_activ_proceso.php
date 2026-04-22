<?php

use core\ConfigGlobal;
use frontend\procesos\support\ProcesosHashes;
use frontend\shared\model\ViewNewTwig;

require_once("frontend/shared/global_header_front.inc");

$webBase = rtrim(ConfigGlobal::getWeb(), '/');
$url_lista = 'frontend/procesos/controller/tipo_activ_proceso_lista.php';
// Renderer frontend que consume /src/procesos/tipo_activ_proceso_lst_posibles
// y devuelve la mini-tabla HTML clickable.
$url_lst_posibles = 'frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php';
$url_asignar = $webBase . '/src/procesos/tipo_activ_proceso_asignar';

$h_asignar = ProcesosHashes::formLink($url_asignar, 'id_tipo_activ!propio!id_tipo_proceso');
$h_nuevo = ProcesosHashes::formLink($url_lst_posibles, 'id_tipo_activ!propio');
$h_lista = ProcesosHashes::formLink($url_lista, '');
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
