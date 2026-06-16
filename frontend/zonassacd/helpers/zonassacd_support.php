<?php

/**
 * Helpers compartidos del módulo frontend/zonassacd.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

/**
 * @param array<int|string, mixed> $payload
 * @return array{a_opciones: array<int|string, string>, perm_des: bool}
 */
function zonassacd_page_from_payload(array $payload): array
{
    return [
        'a_opciones' => notas_desplegable_opciones($payload['a_opciones'] ?? []),
        'perm_des' => !empty($payload['perm_des']),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     id_tabla: string,
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_botones: list<array<string, mixed>>,
 *     con_sel: bool,
 *     a_valores: array<int|string, mixed>,
 * }
 */
function zonassacd_lista_from_payload(array $payload): array
{
    return [
        'id_tabla' => tessera_imprimir_string($payload['id_tabla'] ?? ''),
        'a_cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? null),
        'a_botones' => actividades_lista_botones($payload['a_botones'] ?? null),
        'con_sel' => !empty($payload['con_sel']),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? null),
    ];
}

/**
 * @param array<int|string, mixed> $payload
 * @return array{
 *     a_cabeceras: list<array<string, mixed>|string>,
 *     a_valores: array<int|string, mixed>,
 * }
 */
function zonassacd_lista_tot_from_payload(array $payload): array
{
    return [
        'a_cabeceras' => actividades_lista_cabeceras($payload['a_cabeceras'] ?? null),
        'a_valores' => actividades_lista_datos($payload['a_valores'] ?? null),
    ];
}
