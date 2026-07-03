<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_eliminar', [
    'obj_pau' => (string)filter_input(INPUT_POST, 'obj_pau'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
]));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    AjaxJsonSupport::response($error);
}
AjaxJsonSupport::response();
