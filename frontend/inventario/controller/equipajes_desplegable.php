<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$Qfiltro = (string)filter_input(INPUT_POST, 'filtro');
$Qimprimir = (string)filter_input(INPUT_POST, 'imprimir');
$Qeliminar = (string)filter_input(INPUT_POST, 'eliminar');

$f_ini_iso = date('Y-m-d');
if ($Qfiltro !== '') {
    if ($Qfiltro === 'tot') {
        $f_ini_iso = date('Y') . '-01-01';
    }
    if ($Qfiltro === 'curs') {
        $aa = date('Y');
        $aaa = $aa - 1;
        $f_ini_iso = $aaa . '-10-01';
    }
}

$url_backend = '/src/inventario/lista_equipajes_desde_fecha';
$a_campos_backend = ['f_ini_iso' => $f_ini_iso];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$a_opciones = InventarioPayload::desplegableOpciones($payload['a_opciones'] ?? []);
$equipaje_ctx_map = InventarioPayload::equipajeCtxMap($payload['equipaje_ctx_map'] ?? []);

$oDesplEquipajes = new Desplegable('id_equipaje', $a_opciones, '', true);
if ($Qimprimir !== '') {
    $oDesplEquipajes->setAction('fnjs_ver_2()');
} else {
    $oDesplEquipajes->setAction('fnjs_ver_1()');
}

if ($Qeliminar !== '') {
    $oDesplEquipajes->setAction('');
}

$html = $oDesplEquipajes->desplegable();
$html .= '<script>window.equipaje_ctx_map = ' . json_encode($equipaje_ctx_map, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) . ';</script>';

AjaxJsonSupport::html($html);
