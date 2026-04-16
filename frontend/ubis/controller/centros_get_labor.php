<?php

use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/centros_get_labor');
$a_cabeceras = $data['a_cabeceras'];
$a_valores = [];
$c = 0;
foreach ($data['a_valores'] as $row) {
    $c++;
    $id_ubi = (int)$row['id_ubi'];
    $a_valores[$c][1] = ['script' => "fnjs_modificar($id_ubi,\"labor\")", 'valor' => $row['nombre_ubi']];
    $a_valores[$c][2] = $row['tipo_ctr'];
    $a_valores[$c][3] = $row['tipo_labor_txt'];
}

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_labor');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->mostrar_tabla();

