<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\encargossacd\helpers\EncargossacdPostInput;

/**
 * Listado SACD "d" (cr 9/20, 10).
 *
 * Los datos se obtienen del endpoint `/src/encargossacd/listas_d_data`
 * ({@see \src\encargossacd\application\ListasDData}). Al tratarse de una
 * salida que se imprime tal cual, se vuelca el HTML directamente sin vista.
 */

// INICIO Cabecera global de URL de controlador (frontend) *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qsf = EncargossacdPostInput::postInt('sf');

$datos = PostRequest::getDataFromUrl('/src/encargossacd/listas_d_data', ['sf' => $Qsf]);

echo PayloadCoercion::string($datos['Html'] ?? '');
