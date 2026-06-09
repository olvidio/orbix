<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

$data = PostRequest::getDataFromUrl('/src/misas/ver_encargos_centros_data', [
    'id_zona' => $Qid_zona,
]);

$columns = $data['columns'] ?? [];
$rows = $data['rows'] ?? [];
$a_opciones_zona = $data['a_opciones_zona'] ?? [];

// Desplegable de zonas para el modal: cambiar la zona recarga el desplegable
// de encargos via `fnjs_prepara_select_encargo()`.
$oDesplZonasCtr = new Desplegable();
$oDesplZonasCtr->setOpciones($a_opciones_zona);
$oDesplZonasCtr->setBlanco(true);
$oDesplZonasCtr->setNombre('id_zona_enc');
$oDesplZonasCtr->setAction('fnjs_prepara_select_encargo()');

$url_guardar_encargo_centro = AppUrlConfig::getApiBaseUrl() . '/src/misas/guardar_encargo_centro';
$oHashGuardar = new HashFront();
$oHashGuardar->setUrl($url_guardar_encargo_centro);
$oHashGuardar->setCamposForm('id_item!id_enc!id_ctr');
$h_guardar_encargo_centro = $oHashGuardar->linkSinValParams();

$url_eliminar_encargo_centro = AppUrlConfig::getApiBaseUrl() . '/src/misas/eliminar_encargo_centro';
$oHashEliminar = new HashFront();
$oHashEliminar->setUrl($url_eliminar_encargo_centro);
$oHashEliminar->setCamposForm('id_item');
$h_eliminar_encargo_centro = $oHashEliminar->linkSinValParams();

$url_desplegable_encargos = AppUrlConfig::getApiBaseUrl() . '/src/misas/desplegable_encargos';
$oHashDespl = new HashFront();
$oHashDespl->setUrl($url_desplegable_encargos);
$oHashDespl->setCamposForm('id_zona!id_enc');
$h_desplegable_encargos = $oHashDespl->linkSinValParams();

$url_desplegable_centros_zona = AppUrlConfig::getApiBaseUrl() . '/src/misas/desplegable_centros_zona';
$oHashDesplCtr = new HashFront();
$oHashDesplCtr->setUrl($url_desplegable_centros_zona);
$oHashDesplCtr->setCamposForm('id_zona!id_ubi');
$h_desplegable_centros_zona = $oHashDesplCtr->linkSinValParams();

$url_ver_encargos_centros = 'frontend/misas/controller/ver_encargos_centros.php';
$oHashVer = new HashFront();
$oHashVer->setUrl($url_ver_encargos_centros);
$oHashVer->setCamposForm('id_zona');
$h_ver_encargos_centros = $oHashVer->linkSinValParams();

$a_campos = [
    'json_columns_cuadricula' => json_encode($columns),
    'json_data_cuadricula' => json_encode($rows),
    'oDesplZonasCtr' => $oDesplZonasCtr,
    'id_zona' => $Qid_zona,
    'url_guardar_encargo_centro' => $url_guardar_encargo_centro,
    'h_guardar_encargo_centro' => $h_guardar_encargo_centro,
    'url_eliminar_encargo_centro' => $url_eliminar_encargo_centro,
    'h_eliminar_encargo_centro' => $h_eliminar_encargo_centro,
    'url_desplegable_encargos' => $url_desplegable_encargos,
    'h_desplegable_encargos' => $h_desplegable_encargos,
    'url_desplegable_centros_zona' => $url_desplegable_centros_zona,
    'h_desplegable_centros_zona' => $h_desplegable_centros_zona,
    'url_ver_encargos_centros' => $url_ver_encargos_centros,
    'h_ver_encargos_centros' => $h_ver_encargos_centros,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_encargos_centros.phtml', $a_campos);
