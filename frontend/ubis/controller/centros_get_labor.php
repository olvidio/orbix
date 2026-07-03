<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\permisos\MenuPermCheckboxReadHtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/centros_get_labor'));
$lista = UbisPayload::listaFromPayload($data);
$laborMap = UbisPayload::permBitMap($data['tipo_labor_bit_map'] ?? []);
$iconsBase = OrbixRuntime::getWebIcons();
$a_valores = [];
$c = 0;
foreach (UbisPayload::listaFilas($data['a_valores'] ?? []) as $row) {
    $c++;
    $parsed = UbisPayload::centroLaborRow(UbisPayload::postData($row));
    $a_valores[$c][1] = ['script' => "fnjs_modificar({$parsed['id_ubi']},\"labor\")", 'valor' => $parsed['nombre_ubi']];
    $a_valores[$c][2] = $parsed['tipo_ctr'];
    $a_valores[$c][3] = MenuPermCheckboxReadHtml::render($parsed['tipo_labor'], $laborMap, $iconsBase);
}

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_labor');
$oLista->setCabeceras($lista['cabeceras']);
$oLista->setDatos($a_valores);
AjaxJsonSupport::html($oLista->mostrar_tabla());
