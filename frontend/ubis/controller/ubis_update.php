<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/ubis_guardar', PostRequest::requestPayloadForHash()));
$error = ubis_api_error($data);
if ($error !== '') {
    ajax_json_response($error);
}
ajax_json_response();
