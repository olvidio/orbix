<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();

$Qid_equipaje = (string)filter_input(INPUT_POST, 'id_equipaje');
$Qid_tipo_doc = (string)filter_input(INPUT_POST, 'id_tipo_doc');

$url_backend = '/src/inventario/lista_docs_libres';
$a_campos_backend = [
    'id_equipaje' => $Qid_equipaje,
    'id_tipo_doc' => $Qid_tipo_doc,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$a_valores = inventario_docs_libres_rows(inventario_post_payload($data)['a_valores'] ?? []);

$txt = '<br>';
foreach ($a_valores as $a_valor) {
    $id = $a_valor['sigla'];
    $txt .= "<input class='sel' type='checkbox' name='sel[]' id='a$id' value='$id'>";
    $txt .= $a_valor['identificador'] . ' ' . $a_valor['etiqueta'];
    $txt .= '<br>';
}
ajax_json_html($txt);
