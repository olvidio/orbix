<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPayload;

/**
 * Proxy AJAX: la lógica vive en {@see \src\encargossacd\application\EncargoComprobacionesCtr}
 * y se expone en `/src/encargossacd/comprobaciones_ctr`.
 */

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/comprobaciones_ctr');
AjaxJsonSupport::response('', ['text' => EncargossacdPayload::comprobacionesTexto($data)]);
