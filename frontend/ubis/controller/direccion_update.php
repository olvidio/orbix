<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/direccion_update', PostRequest::requestPayloadForHash()));
$error = ubis_api_error($data);
if ($error !== '') {
    echo $error;
}
