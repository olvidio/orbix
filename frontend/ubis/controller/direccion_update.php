<?php

use frontend\shared\PostRequest;

/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/direccion_update', $_POST);
if (!empty($data['error'])) {
    echo (string)$data['error'];
}
