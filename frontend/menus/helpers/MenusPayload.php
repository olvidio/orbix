<?php

declare(strict_types=1);

namespace frontend\menus\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class MenusPostInput
{
    public static function selFirstItem(mixed $aSel): mixed
    {
        if (!is_array($aSel)) {
            return null;
        }
        foreach ($aSel as $item) {
            return $item;
        }

        return null;
    }

    public static function idFromSelItem(mixed $sel0): int
    {
        if (!is_string($sel0) || $sel0 === '') {
            return 0;
        }
        $parts = explode('#', $sel0, 2);
        $idRaw = $parts[0];

        return is_numeric($idRaw) ? (int) $idRaw : 0;
    }
}

final class MenusPayload
{
    /**
     * @return array<int|string, mixed>
     */
    public static function listaDatos(mixed $raw): array
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
    public static function getPageFromPayload(array $pageData): array
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
            $idMetamenu = PayloadCoercion::string($idMetamenuRaw);
        }

        return [
            'mode' => PayloadCoercion::string($pageData['mode'] ?? ''),
            'id_menu' => PayloadCoercion::string($pageData['id_menu'] ?? ''),
            'orden_txt' => PayloadCoercion::string($pageData['orden_txt'] ?? ''),
            'menu' => PayloadCoercion::string($pageData['menu'] ?? ''),
            'parametros' => PayloadCoercion::string($pageData['parametros'] ?? ''),
            'id_metamenu' => $idMetamenu,
            'menu_perm' => PayloadCoercion::int($pageData['menu_perm'] ?? 0),
            'txt_ok' => PayloadCoercion::string($pageData['txt_ok'] ?? ''),
            'campos_chk' => PayloadCoercion::string($pageData['campos_chk'] ?? 'ok'),
            'menu_rows' => $menuRows,
        ];
    }

    /**
     * @return array<int|string, int>
     */
    public static function permMenuBitMap(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $value) {
            $out[$key] = PayloadCoercion::int($value);
        }

        return $out;
    }
}
