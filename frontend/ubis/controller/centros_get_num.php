<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/centros_get_num'));
$lista = ubis_lista_from_payload($data);

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_num');
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($lista['valores']);
echo $oLista->mostrar_tabla();
