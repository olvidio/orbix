<?php

/**
 * Helpers compartidos del módulo frontend/cartaspresentacion.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @return array<string, mixed>
 */
function cartaspresentacion_post_data(mixed $data): array
{
    if (!is_array($data)) {
        return [];
    }
    $out = [];
    foreach ($data as $key => $value) {
        if (is_string($key)) {
            $out[$key] = $value;
        }
    }

    return $out;
}

/**
 * @return array<string, mixed>
 */
function cartaspresentacion_hash_campos_hidden(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $k => $v) {
        if (is_string($k)) {
            $out[$k] = $v;
        }
    }

    return $out;
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     mi_dele: string,
 *     url_ctr: string,
 *     h_ctr: string,
 *     url_lista: string,
 *     hash_lista_html: string,
 *     url_form: string,
 *     h_form: string,
 *     url_poblaciones: string,
 *     h_poblaciones: string,
 *     url_update: string,
 *     url_eliminar: string,
 *     h_eliminar: string,
 * }
 */
function cartaspresentacion_shell_view_from_payload(array $payload): array
{
    return [
        'mi_dele' => tessera_imprimir_string($payload['mi_dele'] ?? ''),
        'url_ctr' => tessera_imprimir_string($payload['url_ctr'] ?? ''),
        'h_ctr' => tessera_imprimir_string($payload['h_ctr'] ?? ''),
        'url_lista' => tessera_imprimir_string($payload['url_lista'] ?? ''),
        'hash_lista_html' => tessera_imprimir_string($payload['hash_lista_html'] ?? ''),
        'url_form' => tessera_imprimir_string($payload['url_form'] ?? ''),
        'h_form' => tessera_imprimir_string($payload['h_form'] ?? ''),
        'url_poblaciones' => tessera_imprimir_string($payload['url_poblaciones'] ?? ''),
        'h_poblaciones' => tessera_imprimir_string($payload['h_poblaciones'] ?? ''),
        'url_update' => tessera_imprimir_string($payload['url_update'] ?? ''),
        'url_eliminar' => tessera_imprimir_string($payload['url_eliminar'] ?? ''),
        'h_eliminar' => tessera_imprimir_string($payload['h_eliminar'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     url_lista: string,
 *     hash_lista_html: string,
 *     opciones_region: array<int|string, string>,
 *     opciones_pais: array<int|string, string>,
 *     opciones_delegacion: array<int|string, string>,
 * }
 */
function cartaspresentacion_buscar_view_from_payload(array $payload): array
{
    return [
        'url_lista' => tessera_imprimir_string($payload['url_lista'] ?? ''),
        'hash_lista_html' => tessera_imprimir_string($payload['hash_lista_html'] ?? ''),
        'opciones_region' => notas_desplegable_opciones($payload['opciones_region'] ?? []),
        'opciones_pais' => notas_desplegable_opciones($payload['opciones_pais'] ?? []),
        'opciones_delegacion' => notas_desplegable_opciones($payload['opciones_delegacion'] ?? []),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{
 *     ok: bool,
 *     mensaje: string,
 *     nombre_ubi: string,
 *     pres_nom: string,
 *     pres_telf: string,
 *     pres_mail: string,
 *     zona: string,
 *     observ: string,
 *     hash_update_html: string,
 * }
 */
function cartaspresentacion_form_view_from_payload(array $payload): array
{
    return [
        'ok' => !empty($payload['ok']),
        'mensaje' => tessera_imprimir_string($payload['mensaje'] ?? ''),
        'nombre_ubi' => tessera_imprimir_string($payload['nombre_ubi'] ?? ''),
        'pres_nom' => tessera_imprimir_string($payload['pres_nom'] ?? ''),
        'pres_telf' => tessera_imprimir_string($payload['pres_telf'] ?? ''),
        'pres_mail' => tessera_imprimir_string($payload['pres_mail'] ?? ''),
        'zona' => tessera_imprimir_string($payload['zona'] ?? ''),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'hash_update_html' => tessera_imprimir_string($payload['hash_update_html'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{html_lista: string, html_errores: string}
 */
function cartaspresentacion_lista_html_from_payload(array $payload): array
{
    return [
        'html_lista' => tessera_imprimir_string($payload['html_lista'] ?? ''),
        'html_errores' => tessera_imprimir_string($payload['html_errores'] ?? ''),
    ];
}

/**
 * @param array<string, mixed> $payload
 * @return array{cabeceras: list<array<string, mixed>|string>, valores: array<int|string, mixed>, explicacion: string}
 */
function cartaspresentacion_ubis_lista_from_payload(array $payload): array
{
    return [
        'cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'explicacion' => tessera_imprimir_string($payload['explicacion'] ?? ''),
    ];
}
