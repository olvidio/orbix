<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/trasladar_ubis', PostRequest::requestPayloadForHash()));
$error = ubis_api_error($data);
if ($error !== '') {
    exit($error);
}
