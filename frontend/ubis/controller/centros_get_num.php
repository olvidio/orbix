<?php

use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/centros_get_num');
$a_cabeceras = $data['a_cabeceras'];
$a_valores = $data['a_valores'];

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_num');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->mostrar_tabla();

