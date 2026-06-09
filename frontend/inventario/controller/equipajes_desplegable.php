<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
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
$payload = inventario_post_payload($data);
$a_opciones = inventario_desplegable_opciones($payload['a_opciones'] ?? []);

$oDesplEquipajes = new Desplegable('id_equipaje', $a_opciones, '', true);
if ($Qimprimir !== '') {
    $oDesplEquipajes->setAction('fnjs_ver_2()');
} else {
    $oDesplEquipajes->setAction('fnjs_ver_1()');
}

if ($Qeliminar !== '') {
    $oDesplEquipajes->setAction('');
}

echo $oDesplEquipajes->desplegable();
