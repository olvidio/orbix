<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/ubis/direccion_update', PostRequest::requestPayloadForHash());
if (!empty($data['error'])) {
    echo (string)$data['error'];
}
