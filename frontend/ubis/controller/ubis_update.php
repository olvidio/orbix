<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/ubis/ubis_guardar', PostRequest::requestPayloadForHash(), false);
if (!empty($data['error'])) {
    $msg = PostRequest::stripInternalCallProvenance(PayloadCoercion::string($data['error']));
    $msg = html_entity_decode(strip_tags($msg), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $msg = trim((string) preg_replace('/\s+/', ' ', $msg));
    AjaxJsonSupport::response($msg !== '' ? $msg : _('Error al guardar'));
}
$error = UbisPayload::apiError(UbisPayload::postData($data));
if ($error !== '') {
    AjaxJsonSupport::response($error);
}
AjaxJsonSupport::response();
