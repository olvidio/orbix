<?php

/**
 * Helpers compartidos del módulo frontend/actividadcargos.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/../../notas/helpers/notas_support.php';

/**
 * @param array<int|string, mixed> $raw
 * @return array<string, mixed>
 */
function actividadcargos_string_key_payload(array $raw): array
{
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @return array{campos_form?: string, campos_no: string, campos_hidden?: array<string, mixed>}|null
 */
function actividadcargos_hash_form_config(mixed $raw): ?array
{
    if (!is_array($raw)) {
        return null;
    }
    $camposNo = $raw['campos_no'] ?? null;
    if (!is_string($camposNo)) {
        return null;
    }
    $cfg = ['campos_no' => $camposNo];
    $cf = $raw['campos_form'] ?? null;
    if (is_string($cf) && $cf !== '') {
        $cfg['campos_form'] = $cf;
    }
    $hidden = $raw['campos_hidden'] ?? null;
    if (is_array($hidden)) {
        $hiddenOut = [];
        foreach ($hidden as $k => $v) {
            if (is_string($k)) {
                $hiddenOut[$k] = $v;
            }
        }
        if ($hiddenOut !== []) {
            $cfg['campos_hidden'] = $hiddenOut;
        }
    }

    return $cfg;
}

/**
 * @return array{opciones: array<int|string, string>, opcion_sel: string}|null
 */
function actividadcargos_desplegable_select(mixed $raw): ?array
{
    if (!is_array($raw)) {
        return null;
    }
    $opcionesRaw = $raw['opciones'] ?? null;
    if (!is_array($opcionesRaw)) {
        return null;
    }

    return [
        'opciones' => notas_desplegable_opciones($opcionesRaw),
        'opcion_sel' => tessera_imprimir_string($raw['opcion_sel'] ?? ''),
    ];
}
