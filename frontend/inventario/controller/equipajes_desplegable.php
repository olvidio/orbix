<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qfiltro = (string)filter_input(INPUT_POST, 'filtro');
$Qimprimir = (string)filter_input(INPUT_POST, 'imprimir');
$Qeliminar = (string)filter_input(INPUT_POST, 'eliminar');

$f_ini_iso = date('Y-m-d');
if (!empty($Qfiltro)) {
    // if ($Qfiltro === 'hoy') --> ya es por defecto
    if ($Qfiltro === 'tot') {
        $f_ini_iso = date('Y') . '-01-01';
    }
    if ($Qfiltro === 'curs') {
        $aa = date('Y');
        $aaa = $aa - 1;
        $f_ini_iso = $aaa . '-10-01';
    }

}

$url_backend = '/src/inventario/infrastructure/controllers/lista_equipajes_desde_fecha.php';
$a_campos_backend = ['f_ini_iso' => $f_ini_iso];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    exit($data['error']);
}

$a_opciones = $data['a_opciones'];


$oDesplEquipajes = new Desplegable('id_equipaje', $a_opciones, '', true);
if (!empty($Qimprimir)) {
    $oDesplEquipajes->setAction('fnjs_ver_2()');
} else {
    $oDesplEquipajes->setAction('fnjs_ver_1()');
}

if (!empty($Qeliminar)) {
    $oDesplEquipajes->setAction('');
}


echo $oDesplEquipajes->desplegable();