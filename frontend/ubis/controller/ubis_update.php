<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_guardar', PostRequest::requestPayloadForHash()));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    AjaxJsonSupport::response($error);
}
AjaxJsonSupport::response();
