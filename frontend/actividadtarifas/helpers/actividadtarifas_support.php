<?php

/**
 * Helpers compartidos del módulo frontend/actividadtarifas.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     id_tarifa: string,
 *     es_nuevo: bool,
 *     letra: string,
 *     modo: int,
 *     observ: string,
 *     opciones_modo: array<int|string, string>,
 *     id_item: string,
 *     id_tipo_activ: int,
 *     id_tarifa_sel: int,
 *     isfsv: int,
 *     nom_tipo_activ: string,
 *     opciones_tarifa: array<int|string, string>,
 *     id_ubi: int,
 *     year: int,
 *     cantidad: string,
 *     opciones_serie: array<int|string, string>,
 *     id_serie_sel: int,
 *     token_update: string,
 *     token_eliminar: string,
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_valores: array<int|string, mixed>,
 *     puede_anadir: bool,
 *     any_anterior: int,
 *     any_actual: int,
 *     token_copiar: string,
 * }
 */
function actividadtarifas_payload_fields(array $payload): array
{
    return [
        'id_tarifa' => tessera_imprimir_string($payload['id_tarifa'] ?? 'nuevo'),
        'es_nuevo' => ($payload['es_nuevo'] ?? true) === true,
        'letra' => tessera_imprimir_string($payload['letra'] ?? ''),
        'modo' => tessera_imprimir_int($payload['modo'] ?? 0),
        'observ' => tessera_imprimir_string($payload['observ'] ?? ''),
        'opciones_modo' => notas_desplegable_opciones($payload['opciones_modo'] ?? []),
        'id_item' => tessera_imprimir_string($payload['id_item'] ?? ''),
        'id_tipo_activ' => tessera_imprimir_int($payload['id_tipo_activ'] ?? 0),
        'id_tarifa_sel' => tessera_imprimir_int($payload['id_tarifa_sel'] ?? 0),
        'isfsv' => tessera_imprimir_int($payload['isfsv'] ?? 0),
        'nom_tipo_activ' => tessera_imprimir_string($payload['nom_tipo_activ'] ?? ''),
        'opciones_tarifa' => notas_desplegable_opciones($payload['opciones_tarifa'] ?? []),
        'id_ubi' => tessera_imprimir_int($payload['id_ubi'] ?? 0),
        'year' => tessera_imprimir_int($payload['year'] ?? 0),
        'cantidad' => tessera_imprimir_string($payload['cantidad'] ?? ''),
        'opciones_serie' => notas_desplegable_opciones($payload['opciones_serie'] ?? []),
        'id_serie_sel' => tessera_imprimir_int($payload['id_serie_sel'] ?? 1),
        'token_update' => tessera_imprimir_string($payload['token_update'] ?? ''),
        'token_eliminar' => tessera_imprimir_string($payload['token_eliminar'] ?? ''),
        'a_cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? []),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? []),
        'puede_anadir' => ($payload['puede_anadir'] ?? false) === true,
        'any_anterior' => tessera_imprimir_int($payload['any_anterior'] ?? 0),
        'any_actual' => tessera_imprimir_int($payload['any_actual'] ?? 0),
        'token_copiar' => tessera_imprimir_string($payload['token_copiar'] ?? ''),
    ];
}

function actividadtarifas_json_for_html(string $value): string
{
    $encoded = json_encode($value, JSON_UNESCAPED_SLASHES);

    return htmlspecialchars($encoded !== false ? $encoded : '""', ENT_QUOTES, 'UTF-8');
}
