<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/ubis/centros_get_plazas');
$a_cabeceras = $data['a_cabeceras'];
$a_valores = $data['a_valores'];

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_plazas');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->mostrar_tabla();

