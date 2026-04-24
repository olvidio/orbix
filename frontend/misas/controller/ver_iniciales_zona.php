<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

$data = PostRequest::getDataFromUrl('/src/misas/ver_iniciales_zona_data', [
    'id_zona' => $Qid_zona,
]);

$columns = $data['columns'] ?? [];
$rows = $data['rows'] ?? [];
$id_zona = (int)($data['id_zona'] ?? $Qid_zona);

// URL absoluta del endpoint backend: web\Hash genera el hash a partir de la
// URL; el JS posteara contra la misma ruta para que el hash coincida.
$url_update_iniciales = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/update_iniciales';
$oHashIniciales = new Hash();
$oHashIniciales->setUrl($url_update_iniciales);
$oHashIniciales->setCamposForm('id_sacd!iniciales!color');
$h_iniciales = $oHashIniciales->linkSinValParams();

$a_campos = [
    'json_columns_cuadricula' => json_encode($columns),
    'json_data_cuadricula' => json_encode($rows),
    'h_iniciales' => $h_iniciales,
    'url_update_iniciales' => $url_update_iniciales,
    'id_zona' => $id_zona,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_iniciales_zona.phtml', $a_campos);
