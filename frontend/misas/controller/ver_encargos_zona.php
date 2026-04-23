<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

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
$oDesplNoms->setOpciones($tipos_encargo);
$oDesplNoms->setBlanco('t');

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($centros);

$oDesplIdiomas = new Desplegable('idioma_enc', $idiomas, '', true);

// URL absoluta del endpoint backend: web\Hash genera el hash a partir de la
// URL; el JS posteara contra la misma ruta para que el hash coincida.
$url_guardar_encargo_zona = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/guardar_encargo_zona';
$oHashGuardar = new Hash();
$oHashGuardar->setUrl($url_guardar_encargo_zona);
$oHashGuardar->setCamposForm('id_enc!id_tipo_enc!id_ubi!id_zona!descripcion_lugar!encargo!idioma_enc!observ!orden!prioridad');
$h_guardar_encargo_zona = $oHashGuardar->linkSinValParams();

$url_eliminar_encargo_zona = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/eliminar_encargo_zona';
$oHashEliminar = new Hash();
$oHashEliminar->setUrl($url_eliminar_encargo_zona);
$oHashEliminar->setCamposForm('id_enc');
$h_eliminar_encargo_zona = $oHashEliminar->linkSinValParams();

$url_ver_encargos_zona = 'frontend/misas/controller/ver_encargos_zona.php';
$oHashVer = new Hash();
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

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_encargos_zona.phtml', $a_campos);
