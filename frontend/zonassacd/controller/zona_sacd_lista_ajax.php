<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\zonassacd\helpers\ZonassacdPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qque = (string)(filter_input(INPUT_POST, 'que') ?: filter_input(INPUT_GET, 'que'));

if ($Qque === 'get_lista_tot') {
    $lista = ZonassacdPayload::listaTotFromPayload(PostRequest::getDataFromUrl('/src/zonassacd/zona_sacd_lista_tot'));
    $oTabla = new Lista();
    $oTabla->setCabeceras($lista['a_cabeceras']);
    $oTabla->setDatos($lista['a_valores']);
    AjaxJsonSupport::html($oTabla->lista());
}

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$lista = ZonassacdPayload::listaFromPayload(PostRequest::getDataFromUrl('/src/zonassacd/zona_sacd_lista', ['id_zona' => $Qid_zona]));

$oTabla = new Lista();
$oTabla->setId_tabla($lista['id_tabla']);
$oTabla->setCabeceras($lista['a_cabeceras']);
$oTabla->setBotones($lista['a_botones']);
$oTabla->setConSel($lista['con_sel']);
$oTabla->setDatos($lista['a_valores']);
AjaxJsonSupport::html($oTabla->mostrar_tabla());
