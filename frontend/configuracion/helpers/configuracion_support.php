<?php

/**
 * Helpers compartidos del módulo frontend/configuracion.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/../../notas/helpers/notas_support.php';
require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @param array<int|string, mixed> $raw
 * @return array<string, mixed>
 */
function configuracion_string_key_payload(array $raw): array
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
 * @return array<string, mixed>
 */
function configuracion_hash_campos_hidden(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array<string, mixed>
 */
function configuracion_modulos_form_view_from_payload(array $payload): array
{
    return [
        'hash_form_html' => tessera_imprimir_string($payload['hash_form_html'] ?? ''),
        'hash_actualizar_html' => tessera_imprimir_string($payload['hash_actualizar_html'] ?? ''),
        'id_mod' => tessera_imprimir_int($payload['id_mod'] ?? 0),
        'nom' => tessera_imprimir_string($payload['nom'] ?? ''),
        'descripcion' => tessera_imprimir_string($payload['descripcion'] ?? ''),
        'a_mods_todos' => is_array($payload['a_mods_todos'] ?? null) ? $payload['a_mods_todos'] : [],
        'a_apps_todas' => is_array($payload['a_apps_todas'] ?? null) ? $payload['a_apps_todas'] : [],
        'a_mods_req' => is_array($payload['a_mods_req'] ?? null) ? $payload['a_mods_req'] : [],
        'a_apps_req' => is_array($payload['a_apps_req'] ?? null) ? $payload['a_apps_req'] : [],
        'a_apps_mod' => is_array($payload['a_apps_mod'] ?? null) ? $payload['a_apps_mod'] : [],
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     hash_lista_html: string,
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_botones: list<array<string, mixed>>,
 *     a_valores: array<int|string, mixed>,
 *     txt_eliminar: string,
 *     txt_anadir_modulo: string,
 * }
 */
function configuracion_modulos_select_view_from_payload(array $payload): array
{
    return [
        'hash_lista_html' => tessera_imprimir_string($payload['hash_lista_html'] ?? ''),
        'a_cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'a_botones' => actividades_lista_botones($payload['a_botones'] ?? []),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'txt_eliminar' => tessera_imprimir_string($payload['txt_eliminar'] ?? ''),
        'txt_anadir_modulo' => tessera_imprimir_string($payload['txt_anadir_modulo'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $data
 * @return array{a_locales: array<int|string, string>, idioma_select: string}
 */
function configuracion_parametros_idioma_desplegable(array $data): array
{
    return [
        'a_locales' => notas_desplegable_opciones($data['a_locales'] ?? []),
        'idioma_select' => tessera_imprimir_string($data['idioma_select'] ?? ''),
    ];
}

/**
 * @param array<int|string, mixed> $data
 * @return array<string, mixed>
 */
function configuracion_parametros_view_from_payload(array $data): array
{
    $view = configuracion_string_key_payload($data);
    $idioma = configuracion_parametros_idioma_desplegable($data);
    $view['a_locales'] = $idioma['a_locales'];
    $view['idioma_select'] = $idioma['idioma_select'];

    return $view;
}
