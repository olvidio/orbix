<?php

/**
 * Helpers compartidos del módulo frontend/dossiers.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use frontend\shared\security\HashFrontSignedLink;

/**
 * @return list<array<string, mixed>>
 */
function dossiers_list_rows(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $item) {
        if (is_array($item)) {
            $out[] = $item;
        }
    }

    return $out;
}

/**
 * @param list<string> $cols
 * @return list<array<string, mixed>>
 */
function dossiers_sign_lista_filas(mixed $raw, array $cols): array
{
    return HashFrontSignedLink::signRowLinkSpecs(dossiers_list_rows($raw), $cols);
}

/**
 * @return array<string, int>
 */
function dossiers_perm_bit_map(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = tessera_imprimir_int($value);
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $data
 * @return array<string, mixed>
 */
function dossiers_view_variables(array $data): array
{
    $out = [];
    foreach ($data as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @param array<int|string, mixed> $seg
 * @return array{
 *     titulo: string,
 *     action_tabla_url: string,
 *     ins_traslado_url: string,
 *     hash_campos_form: string,
 *     hash_campos_no: string,
 *     hash_campos_hidden: array<string, mixed>,
 *     tabla_id: string,
 *     tabla_cabeceras: list<array<string, mixed>|string>,
 *     tabla_botones: list<array<string, mixed>>,
 *     tabla_valores: array<int|string, mixed>,
 *     permiso: int,
 *     script_ctx: array{bloque: string, action_form: string, action_update: string, eliminar_txt: string},
 * }
 */
function dossiers_segmento_datos_tabla(array $seg): array
{
    $tablaRaw = $seg['tabla'] ?? [];
    $tabla = is_array($tablaRaw) ? $tablaRaw : [];
    $hashRaw = $seg['hash'] ?? [];
    $hash = is_array($hashRaw) ? $hashRaw : [];
    $hiddenRaw = $hash['campos_hidden'] ?? [];
    $hidden = [];
    if (is_array($hiddenRaw)) {
        foreach ($hiddenRaw as $k => $v) {
            if (is_string($k)) {
                $hidden[$k] = $v;
            }
        }
    }
    $scriptCtxRaw = $seg['script_ctx'] ?? [];
    $scriptCtx = is_array($scriptCtxRaw) ? $scriptCtxRaw : [];

    return [
        'titulo' => tessera_imprimir_string($seg['titulo'] ?? ''),
        'action_tabla_url' => HashFrontSignedLink::tryFromSpec($seg['action_tabla_link_spec'] ?? null),
        'ins_traslado_url' => HashFrontSignedLink::tryFromSpec($seg['ins_traslado_link_spec'] ?? null),
        'hash_campos_form' => tessera_imprimir_string($hash['campos_form'] ?? 'mod'),
        'hash_campos_no' => tessera_imprimir_string($hash['campos_no'] ?? ''),
        'hash_campos_hidden' => $hidden,
        'tabla_id' => tessera_imprimir_string($tabla['id_tabla'] ?? 'datos_sql'),
        'tabla_cabeceras' => actividades_lista_cabeceras($tabla['cabeceras'] ?? []),
        'tabla_botones' => actividades_lista_botones($tabla['botones'] ?? []),
        'tabla_valores' => actividades_lista_datos($tabla['valores'] ?? []),
        'permiso' => tessera_imprimir_int($seg['permiso'] ?? 0),
        'script_ctx' => [
            'bloque' => tessera_imprimir_string($scriptCtx['bloque'] ?? ''),
            'action_form' => tessera_imprimir_string($scriptCtx['action_form'] ?? ''),
            'action_update' => tessera_imprimir_string($scriptCtx['action_update'] ?? ''),
            'eliminar_txt' => tessera_imprimir_string($scriptCtx['eliminar_txt'] ?? ''),
        ],
    ];
}
