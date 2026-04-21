<?php

use frontend\shared\PostRequest;

/**
 * Proxy AJAX: la lógica vive en {@see \src\encargossacd\application\EncargoComprobacionesCtr}
 * y se expone en `/src/encargossacd/comprobaciones_ctr`.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/** @var array{texto?: string}|string $data */
$data = PostRequest::getDataFromUrl('/src/encargossacd/comprobaciones_ctr');
header('Content-Type: text/plain; charset=UTF-8');
if (is_array($data) && isset($data['texto'])) {
    echo $data['texto'];
} else {
    echo is_string($data) ? $data : '';
}
