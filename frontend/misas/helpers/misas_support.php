<?php

/**
 * Helpers compartidos del módulo frontend/misas.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/../../notas/helpers/notas_support.php';

/**
 * @return array<int|string, string>
 */
function misas_desplegable_opciones(mixed $raw): array
{
    return notas_desplegable_opciones($raw);
}

function misas_string(mixed $value, string $default = ''): string
{
    return tessera_imprimir_string($value, $default);
}

function misas_int(mixed $value, int $default = 0): int
{
    return tessera_imprimir_int($value, $default);
}
