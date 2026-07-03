<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_asis_conjunto_activ_data', $campos));

echo FuncTablasSupport::payloadString($payload, 'content_html');
