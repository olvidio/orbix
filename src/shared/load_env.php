<?php

declare(strict_types=1);

/**
 * Carga `.env` en la raíz del proyecto (`ORBIX_*`, etc.).
 * Sin excepción si falta el fichero; idempotente con require_once del caller.
 */

use src\shared\infrastructure\CargarEnv;

CargarEnv::desde(dirname(__DIR__, 2));
