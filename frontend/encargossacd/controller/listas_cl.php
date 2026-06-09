<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Listado de cl para cr (solo centros de sss+).
 *
 * Obtiene el HTML ya compuesto de `/src/encargossacd/listas_cl_data`
 * ({@see \src\encargossacd\application\ListasClData}) y lo vuelca
 * directamente al cliente.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_cl_data', []);

echo tessera_imprimir_string($datos['Html'] ?? '');
