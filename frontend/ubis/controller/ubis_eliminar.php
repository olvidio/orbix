<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_eliminar', [
    'ctx_eliminar' => (string)filter_input(INPUT_POST, 'ctx_eliminar'),
]));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    AjaxJsonSupport::response($error);
}
AjaxJsonSupport::response();
