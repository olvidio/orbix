<?php

use frontend\shared\config\OrbixRuntime;
use frontend\shared\permisos\MenuPermCheckboxReadHtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/ubis/centros_get_labor');
$a_cabeceras = $data['a_cabeceras'];
$laborMap = [];
if (isset($data['tipo_labor_bit_map']) && is_array($data['tipo_labor_bit_map'])) {
    $laborMap = $data['tipo_labor_bit_map'];
}
$iconsBase = OrbixRuntime::getWebIcons();
$a_valores = [];
$c = 0;
foreach ($data['a_valores'] as $row) {
    $c++;
    $id_ubi = (int)$row['id_ubi'];
    $a_valores[$c][1] = ['script' => "fnjs_modificar($id_ubi,\"labor\")", 'valor' => $row['nombre_ubi']];
    $a_valores[$c][2] = $row['tipo_ctr'];
    $a_valores[$c][3] = MenuPermCheckboxReadHtml::render((int)($row['tipo_labor'] ?? 0), $laborMap, $iconsBase);
}

$oLista = new Lista();
$oLista->setId_tabla('centros_ajax_labor');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
echo $oLista->mostrar_tabla();

