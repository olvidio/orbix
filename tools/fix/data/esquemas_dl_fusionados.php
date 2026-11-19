<?php

declare(strict_types=1);

// Script de línea de órdenes: no hace nada si se pide por el servidor web.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

/**
 * @deprecated Fuente única: `comun.public.mapa_prefijo_acta_esquema` (migración 202607211100).
 *
 * Este fichero solo es fallback vacío si la tabla aún no existe en la BD del CLI.
 * Las fusiones se derivan de filas con `notas` que contienen «fusion»
 * (p. ej. dlz → H-dlal; dlv/dlva/dlst → H-dln).
 *
 * @return array<string, string>
 */
return [];
