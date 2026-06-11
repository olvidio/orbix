<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX: la lógica vive en {@see \src\encargossacd\application\EncargoComprobacionesCtr}
 * y se expone en `/src/encargossacd/comprobaciones_ctr`.
 */

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/comprobaciones_ctr');
ajax_json_response('', ['text' => encargossacd_comprobaciones_texto($data)]);
