<?php

use frontend\shared\PostRequest;
use function frontend\shared\helpers\payload_string;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$campos = array_merge($_GET, $_POST);
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/lista_ultima_activ_data', $campos));

echo payload_string($payload, 'alert_html');
echo '<h3>' . htmlspecialchars(payload_string($payload, 'titulo'), ENT_QUOTES, 'UTF-8') . '</h3>';
echo payload_string($payload, 'stats_html');
echo payload_string($payload, 'tabla_html');
