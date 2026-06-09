<?php
require_once __DIR__ . '/../helpers/encargossacd_support.php';

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Proxy AJAX: la lógica vive en {@see \src\encargossacd\application\EncargoComprobacionesCtr}
 * y se expone en `/src/encargossacd/comprobaciones_ctr`.
 */

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$data = PostRequest::getDataFromUrl('/src/encargossacd/comprobaciones_ctr');
header('Content-Type: text/plain; charset=UTF-8');
echo encargossacd_comprobaciones_texto($data);
