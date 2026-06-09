<?php

use frontend\shared\PostRequest;
use function frontend\shared\helpers\payload_string;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = array_merge($_GET, $_POST);
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/lista_asis_conjunto_activ_data', $campos));

echo payload_string($payload, 'content_html');
