<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/ubiscamas/cama_form_data', $campos);
$payload = is_array($data) ? $data : [];

$a_campos = [
    'oPosicion' => $oPosicion,
    'hash_form_html' => (string)($payload['hash_form_html'] ?? ''),
    'id_cama' => (string)($payload['id_cama'] ?? ''),
    'id_habitacion' => (string)($payload['id_habitacion'] ?? ''),
    'id_ubi' => (int)($payload['id_ubi'] ?? 0),
    'descripcion' => (string)($payload['descripcion'] ?? ''),
    'larga' => (bool)($payload['larga'] ?? false),
    'vip' => (bool)($payload['vip'] ?? false),
    'cama_update_url' => (string)($payload['cama_update_url'] ?? ''),
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('cama_form.phtml', $a_campos);
