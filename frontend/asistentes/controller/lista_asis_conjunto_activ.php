<?php

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_asis_conjunto_activ_data', $campos);
$payload = is_array($data) ? $data : [];

echo (string)($payload['content_html'] ?? '');
