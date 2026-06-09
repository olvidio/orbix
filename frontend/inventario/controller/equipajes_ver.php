<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
FrontBootstrap::boot();

$Qfiltro = (string)filter_input(INPUT_POST, 'filtro');
$Qimprimir = (string)filter_input(INPUT_POST, 'imprimir');
$Qeliminar = (string)filter_input(INPUT_POST, 'eliminar');

$f_ini_iso = date('Y-m-d');
$chk_hoy = 'checked';
$chk_curs = '';
$chk_tot = '';
if ($Qfiltro !== '') {
    if ($Qfiltro === 'tot') {
        $f_ini_iso = date('Y') . '-01-01';
        $chk_hoy = '';
        $chk_curs = '';
        $chk_tot = 'checked';
    }
    if ($Qfiltro === 'curs') {
        $any_anterior = (int)date('Y') - 1;
        $f_ini_iso = (string)$any_anterior . '-10-01';
        $chk_hoy = '';
        $chk_curs = 'checked';
        $chk_tot = '';
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

$oHash = new HashFront();
$oHash->setCamposForm('filtro!id_equipaje');
$oHash->setArrayCamposHidden(['eliminar' => $Qeliminar, 'imprimir' => $Qimprimir]);

$oHash1 = new HashFront();
$oHash1->setUrl('frontend/inventario/controller/equipajes_form_texto_listado.php');
$oHash1->setCamposForm('loc!id_equipaje!texto');
$h_mod_txt = $oHash1->linkSinValParams();

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

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_ver.phtml', $a_campos);
