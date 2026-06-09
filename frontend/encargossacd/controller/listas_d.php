<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

/**
 * Listado SACD "d" (cr 9/20, 10).
 *
 * Los datos se obtienen del endpoint `/src/encargossacd/listas_d_data`
 * ({@see \src\encargossacd\application\ListasDData}). Al tratarse de una
 * salida que se imprime tal cual, se vuelca el HTML directamente sin vista.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once __DIR__ . '/../helpers/encargossacd_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsf = encargossacd_post_int('sf');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_d_data', ['sf' => $Qsf]);

echo tessera_imprimir_string($datos['Html'] ?? '');
