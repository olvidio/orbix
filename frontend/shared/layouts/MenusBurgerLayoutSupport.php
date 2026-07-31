<?php

namespace frontend\shared\layouts;

use frontend\shared\PostRequest;

/**
 * Carga de menú y normalización de params compartida por layouts basados en
 * {@see PostRequest::getDataFromUrl} `/src/menus/menus_burger_layout_data`.
 */
trait MenusBurgerLayoutSupport
{
    /** @var array<string, mixed> */
    private array $menuConfigArray = [];

    /** @var array<int|string, string> */
    private array $listaGrupMenu = [];

    private static function layoutScalarString(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }

    /**
     * @param array<string, mixed> $params
     * @return array<int|string, string>
     */
    private static function layoutListaGrupMenuFromParams(array $params): array
    {
        $raw = $params['listaGrupMenu'] ?? null;
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $value) {
            $out[is_int($key) ? $key : self::layoutScalarString($key)] = self::layoutScalarString($value);
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $grupMenuData
     * @return list<array{grup_menu: string}>
     */
    private static function layoutGrupMenuItems(array $grupMenuData): array
    {
        ksort($grupMenuData);

        $items = [];
        foreach ($grupMenuData as $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $items[] = [
                'grup_menu' => _(self::layoutScalarString($raw['grup_menu'] ?? '')),
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    private static function layoutMenuConfigArray(mixed $raw): array
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
     * @param array<string, mixed> $params
     */
    protected function loadBurgerMenuPayload(array $params): string
    {
        $this->listaGrupMenu = self::layoutListaGrupMenuFromParams($params);

        $payload = PostRequest::getDataFromUrl('/src/menus/menus_burger_layout_data', [
            'lista_grup_menu_json' => json_encode($this->listaGrupMenu, JSON_UNESCAPED_UNICODE),
        ]);
        $this->menuConfigArray = [];
        $userMenus = '';
        if (isset($payload['menu_config'])) {
            $this->menuConfigArray = self::layoutMenuConfigArray($payload['menu_config']);
        }
        if (isset($payload['user_menus_html']) && is_string($payload['user_menus_html'])) {
            $userMenus = $payload['user_menus_html'];
        }

        return $userMenus;
    }

    /**
     * @param array<string, mixed> $params
     */
    protected function defaultGrupMenuFromParams(array $params): string
    {
        $idGrupmenu = self::layoutScalarString($params['id_grupmenu'] ?? '');
        if ($idGrupmenu === '') {
            return '';
        }

        return _($this->listaGrupMenu[$idGrupmenu] ?? '');
    }

    protected function menuConfigJson(): string
    {
        $menuJson = json_encode($this->menuConfigArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($menuJson === false) {
            return '{}';
        }

        return $menuJson;
    }
}
