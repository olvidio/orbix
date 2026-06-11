<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\permisos\MenuPermCheckboxReadHtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/centros_get_labor'));
$lista = ubis_lista_from_payload($data);
$laborMap = ubis_perm_bit_map($data['tipo_labor_bit_map'] ?? []);
$iconsBase = OrbixRuntime::getWebIcons();
$a_valores = [];
$c = 0;
foreach (ubis_lista_filas($data['a_valores'] ?? []) as $row) {
    $c++;
    $parsed = ubis_centro_labor_row(ubis_post_data($row));
    $a_valores[$c][1] = ['script' => "fnjs_modificar({$parsed['id_ubi']},\"labor\")", 'valor' => $parsed['nombre_ubi']];
    $a_valores[$c][2] = $parsed['tipo_ctr'];
    $a_valores[$c][3] = MenuPermCheckboxReadHtml::render($parsed['tipo_labor'], $laborMap, $iconsBase);
}

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_labor');
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($a_valores);
ajax_json_html($oLista->mostrar_tabla());
