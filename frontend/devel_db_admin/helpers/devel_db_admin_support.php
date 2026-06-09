<?php

/**
 * Helpers compartidos del módulo frontend/devel_db_admin.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @return array<int|string, string>
 */
function devel_db_admin_desplegable_opciones(mixed $raw): array
{
    return notas_desplegable_opciones($raw);
}

/**
 * @return list<string>
 */
function devel_db_admin_avisos_list(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        $out[] = tessera_imprimir_string($item);
    }

    return $out;
}

/**
 * @return list<string>
 */
function devel_db_admin_migraciones_sel(mixed $raw): array
{
    if (!is_array($raw)) {
        if ($raw === null) {
            return [];
        }

        return devel_db_admin_migraciones_sel([$raw]);
    }
    $out = [];
    foreach ($raw as $value) {
        $s = tessera_imprimir_string($value);
        if ($s !== '') {
            $out[] = $s;
        }
    }

    return $out;
}

function devel_db_admin_line_string(mixed $line): string
{
    return tessera_imprimir_string($line);
}
