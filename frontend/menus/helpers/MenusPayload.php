<?php

declare(strict_types=1);

namespace frontend\menus\helpers;

use frontend\shared\helpers\PayloadCoercion;

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
            $idMetamenu = \frontend\shared\helpers\PayloadCoercion::string($idMetamenuRaw);
        }

        return [
            'mode' => \frontend\shared\helpers\PayloadCoercion::string($pageData['mode'] ?? ''),
            'id_menu' => \frontend\shared\helpers\PayloadCoercion::string($pageData['id_menu'] ?? ''),
            'orden_txt' => \frontend\shared\helpers\PayloadCoercion::string($pageData['orden_txt'] ?? ''),
            'menu' => \frontend\shared\helpers\PayloadCoercion::string($pageData['menu'] ?? ''),
            'parametros' => \frontend\shared\helpers\PayloadCoercion::string($pageData['parametros'] ?? ''),
            'id_metamenu' => $idMetamenu,
            'menu_perm' => \frontend\shared\helpers\PayloadCoercion::int($pageData['menu_perm'] ?? 0),
            'txt_ok' => \frontend\shared\helpers\PayloadCoercion::string($pageData['txt_ok'] ?? ''),
            'campos_chk' => \frontend\shared\helpers\PayloadCoercion::string($pageData['campos_chk'] ?? 'ok'),
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
            $out[$key] = \frontend\shared\helpers\PayloadCoercion::int($value);
        }

        return $out;
    }
}
