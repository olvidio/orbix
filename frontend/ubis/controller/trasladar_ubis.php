<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/trasladar_ubis', PostRequest::requestPayloadForHash()));
$error = ubis_api_error($data);
if ($error !== '') {
    exit($error);
}
