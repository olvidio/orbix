<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/centros_get_plazas'));
$lista = UbisPayload::listaFromPayload($data);

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_plazas');
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
AjaxJsonSupport::html($oLista->mostrar_tabla());
