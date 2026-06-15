<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();

$Qid_sel = null;
$Qscroll_id = null;
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), $Qid_sel, isset($Qscroll_id) ? (string) $Qscroll_id : ''));


$params = ubis_post_data($_POST);
if ($Qid_sel !== null && $Qid_sel !== '') {
    $params['id_sel'] = tessera_imprimir_string($Qid_sel);
}
if ($Qscroll_id !== null && $Qscroll_id !== '') {
    $params['scroll_id'] = tessera_imprimir_string($Qscroll_id);
}

$tabla = ubis_tabla_from_payload(ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/ubis_tabla_data', $params)));

$oPosicion->setParametros($tabla['go_back'], 1);

$a_valores = ubis_sign_lista_valores($tabla['valores']);
$pagina_link = ubis_pagina_link_from_tabla($tabla);

$oTabla = new Lista();
$oTabla->setId_tabla('ubis_tabla');
$oTabla->setCabeceras($tabla['cabeceras']);
$oTabla->setBotones($tabla['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('!sel');
$oHash->setCamposNo('!scroll_id');
$oHash->setArrayCamposHidden($tabla['hash_hidden']);

$a_campos = [
    'oHash' => $oHash,
    'titulo' => $tabla['titulo'],
    'oTabla' => $oTabla,
    'nueva_ficha' => $tabla['nueva_ficha'],
    'pagina_link' => $pagina_link,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_tabla.phtml', $a_campos);
