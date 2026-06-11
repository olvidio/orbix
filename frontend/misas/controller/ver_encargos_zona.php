<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
require_once 'frontend/misas/helpers/misas_support.php';

FrontBootstrap::boot();
$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
if ($Qorden === '') {
    $Qorden = 'orden';
}

$data = PostRequest::getDataFromUrl('/src/misas/ver_encargos_zona_data', [
    'id_zona' => $Qid_zona,
    'orden' => $Qorden,
]);

$columns = $data['columns'] ?? [];
$rows = $data['rows'] ?? [];
$tipos_encargo = $data['tipos_encargo'] ?? [];
$centros = $data['centros'] ?? [];
$idiomas = $data['idiomas'] ?? [];

$oDesplNoms = new Desplegable();
$oDesplNoms->setNombre('id_tipo_enc');
$oDesplNoms->setOpciones(misas_desplegable_opciones($tipos_encargo));
$oDesplNoms->setBlanco('t');

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones(misas_desplegable_opciones($centros));

$oDesplIdiomas = new Desplegable('idioma_enc', misas_desplegable_opciones($idiomas), '', true);

// URL absoluta del endpoint backend: web\Hash genera el hash a partir de la
// URL; el JS posteara contra la misma ruta para que el hash coincida.
$url_guardar_encargo_zona = AppUrlConfig::getApiBaseUrl() . '/src/misas/guardar_encargo_zona';
$oHashGuardar = new HashFront();
$oHashGuardar->setUrl($url_guardar_encargo_zona);
$oHashGuardar->setCamposForm('id_enc!id_tipo_enc!id_ubi!id_zona!descripcion_lugar!encargo!idioma_enc!observ!orden!prioridad');
$h_guardar_encargo_zona = $oHashGuardar->linkSinValParams();

$url_eliminar_encargo_zona = AppUrlConfig::getApiBaseUrl() . '/src/misas/eliminar_encargo_zona';
$oHashEliminar = new HashFront();
$oHashEliminar->setUrl($url_eliminar_encargo_zona);
$oHashEliminar->setCamposForm('id_enc');
$h_eliminar_encargo_zona = $oHashEliminar->linkSinValParams();

$url_ver_encargos_zona = 'frontend/misas/controller/ver_encargos_zona.php';
$oHashVer = new HashFront();
$oHashVer->setUrl($url_ver_encargos_zona);
$oHashVer->setCamposForm('id_zona!orden');
$h_ver_encargos_zona = $oHashVer->linkSinValParams();

$a_campos = [
    'json_columns_cuadricula' => json_encode($columns),
    'json_data_cuadricula' => json_encode($rows),
    'oDesplNoms' => $oDesplNoms,
    'oDesplCentros' => $oDesplCentros,
    'oDesplIdiomas' => $oDesplIdiomas,
    'id_zona' => $Qid_zona,
    'url_guardar_encargo_zona' => $url_guardar_encargo_zona,
    'h_guardar_encargo_zona' => $h_guardar_encargo_zona,
    'url_eliminar_encargo_zona' => $url_eliminar_encargo_zona,
    'h_eliminar_encargo_zona' => $h_eliminar_encargo_zona,
    'url_ver_encargos_zona' => $url_ver_encargos_zona,
    'h_ver_encargos_zona' => $h_ver_encargos_zona,
];

ajax_json_render_phtml('frontend\\misas\\controller', 'ver_encargos_zona.phtml', $a_campos);
