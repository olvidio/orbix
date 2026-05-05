<?php

declare(strict_types=1);

/**
 * Carga `.env` en la raíz del proyecto (`ORBIX_*`, etc.).
 * Sin excepción si falta el fichero ({@see Dotenv::safeLoad}); idempotente con require_once del caller.
 */

use Dotenv\Dotenv;

$orbixRoot = dirname(__DIR__, 2);

if (! class_exists(Dotenv::class)) {
    return;
}

Dotenv::createImmutable($orbixRoot)->safeLoad();
