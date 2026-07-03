<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/trasladar_ubis', PostRequest::requestPayloadForHash()));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    exit($error);
}
