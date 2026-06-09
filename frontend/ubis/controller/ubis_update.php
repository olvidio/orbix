<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/ubis/ubis_guardar', PostRequest::requestPayloadForHash());
if (!empty($data['error'])) {
    echo $data['error'];
}
