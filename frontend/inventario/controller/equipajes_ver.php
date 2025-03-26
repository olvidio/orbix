<?php

use core\ConfigGlobal;
use core\ViewPhtml;
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
$chk_hoy = 'checked';
$chk_curs = '';
$chk_tot = '';
if (!empty($Qfiltro)) {
    // if ($Qfiltro === 'hoy') --> ya es por defecto
    if ($Qfiltro === 'tot') {
        $f_ini_iso = date('Y') . '-01-01';
        $chk_hoy = '';
        $chk_curs = '';
        $chk_tot = 'checked';
    }
    if ($Qfiltro === 'curs') {
        $any_anterior = (int)date('Y') -1 ;
        $f_ini_iso = (string)$any_anterior . '-10-01';
        $chk_hoy = '';
        $chk_curs = 'checked';
        $chk_tot = '';
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

$oHash = new Hash();
$oHash->setCamposForm('filtro!id_equipaje');
$oHash->setArrayCamposHidden(['eliminar' => $Qeliminar, 'imprimir' => $Qimprimir]);

$oHash1 = new Hash();
$oHash1->setUrl('frontend/inventario/controller/equipajes_form_texto_listado.php');
$oHash1->setCamposForm('loc!id_equipaje!texto');
$h_mod_txt = $oHash1->linkSinVal();

$a_campos = [
    'oHash' => $oHash,
    'imprimir' => $Qimprimir,
    'eliminar' => $Qeliminar,
    'oDesplEquipajes' => $oDesplEquipajes,
    'chk_hoy' => $chk_hoy,
    'chk_curs' => $chk_curs,
    'chk_tot' => $chk_tot,
    'h_mod_txt' => $h_mod_txt,
];

$oView = new ViewPhtml('../frontend/inventario/controller');
$oView->renderizar('equipajes_ver.phtml', $a_campos);
