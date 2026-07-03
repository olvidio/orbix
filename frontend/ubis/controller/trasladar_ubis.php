<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$data = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/trasladar_ubis', PostRequest::requestPayloadForHash()));
$error = UbisPayload::apiError($data);
if ($error !== '') {
    exit($error);
}
