<?php

use frontend\shared\PostRequest;

/**
 * Listado de cl para cr (solo centros de sss+).
 *
 * Obtiene el HTML ya compuesto de `/src/encargossacd/listas_cl_data`
 * ({@see \src\encargossacd\application\ListasClData}) y lo vuelca
 * directamente al cliente.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_cl_data', []);

echo (string)($datos['Html'] ?? '');
