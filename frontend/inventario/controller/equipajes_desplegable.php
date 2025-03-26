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

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/lista_equipajes_desde_fecha.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['f_ini_iso' => $f_ini_iso]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

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