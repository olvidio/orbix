<?php

declare(strict_types=1);

/**
 * Proxy AJAX (JSON) hacia /src/actividades/actividad_que_filtros.
 * Ruta frontend/*.php para que funcione también bajo /orbixsf (fichero físico).
 */

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::sendRawPost(
    '/src/actividades/actividad_que_filtros',
    PostRequest::requestPayloadForHash(),
);
