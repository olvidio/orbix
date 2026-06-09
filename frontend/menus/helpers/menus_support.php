<?php

/**
 * Helpers compartidos del módulo frontend/menus.
 */

require_once __DIR__ . '/../../notas/helpers/tessera_imprimir_support.php';
require_once __DIR__ . '/../../notas/helpers/notas_support.php';

function menus_sel_first_item(mixed $a_sel): mixed
{
    if (!is_array($a_sel)) {
        return null;
    }
    foreach ($a_sel as $item) {
        return $item;
    }

    return null;
}

function menus_id_from_sel_item(mixed $sel0): int
{
    if (!is_string($sel0) || $sel0 === '') {
        return 0;
    }
    $parts = explode('#', $sel0, 2);
    $idRaw = $parts[0];

    return is_numeric($idRaw) ? (int) $idRaw : 0;
}

/**
 * @return array<int|string, mixed>
 */
function menus_lista_datos(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }

    return $raw;
}

/**
 * @param array<int|string, mixed> $pageData
 * @return array{
 *     mode: string,
 *     id_menu: string,
 *     orden_txt: string,
 *     menu: string,
 *     parametros: string,
 *     id_metamenu: string,
 *     menu_perm: int,
 *     txt_ok: string,
 *     campos_chk: string,
 *     menu_rows: list<array<string, mixed>>,
 * }
 */
function menus_get_page_from_payload(array $pageData): array
{
    $menuRowsRaw = $pageData['menu_rows'] ?? [];
    $menuRows = [];
    if (is_array($menuRowsRaw)) {
        foreach ($menuRowsRaw as $row) {
            if (is_array($row)) {
                $menuRows[] = $row;
            }
        }
    }
    $idMetamenuRaw = $pageData['id_metamenu'] ?? null;
    $idMetamenu = '';
    if (is_int($idMetamenuRaw) || is_string($idMetamenuRaw)) {
        $idMetamenu = tessera_imprimir_string($idMetamenuRaw);
    }

    return [
        'mode' => tessera_imprimir_string($pageData['mode'] ?? ''),
        'id_menu' => tessera_imprimir_string($pageData['id_menu'] ?? ''),
        'orden_txt' => tessera_imprimir_string($pageData['orden_txt'] ?? ''),
        'menu' => tessera_imprimir_string($pageData['menu'] ?? ''),
        'parametros' => tessera_imprimir_string($pageData['parametros'] ?? ''),
        'id_metamenu' => $idMetamenu,
        'menu_perm' => tessera_imprimir_int($pageData['menu_perm'] ?? 0),
        'txt_ok' => tessera_imprimir_string($pageData['txt_ok'] ?? ''),
        'campos_chk' => tessera_imprimir_string($pageData['campos_chk'] ?? 'ok'),
        'menu_rows' => $menuRows,
    ];
}

/**
 * @return array<int|string, int>
 */
function menus_perm_menu_bit_map(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $key => $value) {
        $out[$key] = tessera_imprimir_int($value);
    }

    return $out;
}
