<?php

use frontend\shared\PostRequest;
use function frontend\shared\helpers\payload_string;

require_once 'frontend/shared/global_header_front.inc';

/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_ultima_activ_data', $campos);
$payload = is_array($data) ? $data : [];

echo payload_string($payload, 'alert_html');
echo '<h3>' . htmlspecialchars(payload_string($payload, 'titulo'), ENT_QUOTES, 'UTF-8') . '</h3>';
echo payload_string($payload, 'stats_html');
echo payload_string($payload, 'tabla_html');
