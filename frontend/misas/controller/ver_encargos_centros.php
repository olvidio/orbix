<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

$data = PostRequest::getDataFromUrl('/src/misas/ver_encargos_centros_data', [
    'id_zona' => $Qid_zona,
]);

$columns = $data['columns'] ?? [];
$rows = $data['rows'] ?? [];
$a_opciones_zona = $data['a_opciones_zona'] ?? [];
$a_centros_zona = $data['a_centros_zona'] ?? [];

// Desplegable de zonas para el modal: cambiar la zona recarga el desplegable
// de encargos via `fnjs_prepara_select_encargo()`.
$oDesplZonasCtr = new Desplegable();
$oDesplZonasCtr->setOpciones($a_opciones_zona);
$oDesplZonasCtr->setBlanco(false);
$oDesplZonasCtr->setNombre('id_zona_enc');
$oDesplZonasCtr->setOpcion_sel($Qid_zona);
$oDesplZonasCtr->setAction('fnjs_prepara_select_encargo()');

// Desplegable estatico de centros de la zona (contenido fijo; no necesita
// endpoint aparte).
$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($a_centros_zona);

// URLs absolutas para los endpoints backend: los hashes se calculan sobre la
// URL absoluta, y el JS debe postear contra la misma ruta. Usamos
// `linkSinValParams()` porque en el phtml el hash se concatena SIEMPRE tras
// otros parametros en el body POST (p.e. 'id_zona=' + id_zona + $h).
$url_guardar_encargo_centro = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/guardar_encargo_centro';
$oHashGuardar = new Hash();
$oHashGuardar->setUrl($url_guardar_encargo_centro);
$oHashGuardar->setCamposForm('id_item!id_enc!id_ctr');
$h_guardar_encargo_centro = $oHashGuardar->linkSinValParams();

$url_eliminar_encargo_centro = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/eliminar_encargo_centro';
$oHashEliminar = new Hash();
$oHashEliminar->setUrl($url_eliminar_encargo_centro);
$oHashEliminar->setCamposForm('id_item');
$h_eliminar_encargo_centro = $oHashEliminar->linkSinValParams();

$url_desplegable_encargos = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/desplegable_encargos';
$oHashDespl = new Hash();
$oHashDespl->setUrl($url_desplegable_encargos);
$oHashDespl->setCamposForm('id_zona!id_enc');
$h_desplegable_encargos = $oHashDespl->linkSinValParams();

$url_ver_encargos_centros = 'frontend/misas/controller/ver_encargos_centros.php';
$oHashVer = new Hash();
$oHashVer->setUrl($url_ver_encargos_centros);
$oHashVer->setCamposForm('id_zona');
$h_ver_encargos_centros = $oHashVer->linkSinValParams();

$a_campos = [
    'json_columns_cuadricula' => json_encode($columns),
    'json_data_cuadricula' => json_encode($rows),
    'oDesplZonasCtr' => $oDesplZonasCtr,
    'oDesplCentros' => $oDesplCentros,
    'id_zona' => $Qid_zona,
    'url_guardar_encargo_centro' => $url_guardar_encargo_centro,
    'h_guardar_encargo_centro' => $h_guardar_encargo_centro,
    'url_eliminar_encargo_centro' => $url_eliminar_encargo_centro,
    'h_eliminar_encargo_centro' => $h_eliminar_encargo_centro,
    'url_desplegable_encargos' => $url_desplegable_encargos,
    'h_desplegable_encargos' => $h_desplegable_encargos,
    'url_ver_encargos_centros' => $url_ver_encargos_centros,
    'h_ver_encargos_centros' => $h_ver_encargos_centros,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_encargos_centros.phtml', $a_campos);
