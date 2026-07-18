<?php

declare(strict_types=1);

/**
 * Proxy AJAX (JSON) hacia /src/devel_db_admin/migraciones_quitar_registro.
 * Usar ruta frontend/*.php para que funcione también bajo /orbixsf (fichero físico).
 */

use frontend\shared\FrontBootstrap;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::sendRawPost(
    '/src/devel_db_admin/migraciones_quitar_registro',
    PostRequest::requestPayloadForHash(),
);
