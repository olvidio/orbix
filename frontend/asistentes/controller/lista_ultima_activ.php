<?php

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_ultima_activ_data', $campos);
$payload = is_array($data) ? $data : [];

echo (string)($payload['alert_html'] ?? '');
echo '<h3>' . htmlspecialchars((string)($payload['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</h3>';
echo (string)($payload['stats_html'] ?? '');
echo (string)($payload['tabla_html'] ?? '');
