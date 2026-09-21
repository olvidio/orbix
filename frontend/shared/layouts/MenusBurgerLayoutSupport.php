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
                $out[$key] = self::layoutHydrateMenuNodes($value);
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
        if (isset($payload['user_menu_nodes']) && is_array($payload['user_menu_nodes'])) {
            $userMenus = self::layoutRenderUserMenus(
                self::layoutHydrateMenuNodes($payload['user_menu_nodes'])
            );
        }

        return $userMenus;
    }

    private static function layoutHydrateMenuNodes(mixed $raw): mixed
    {
        if (!is_array($raw)) {
            return $raw;
        }
        if (array_key_exists('name', $raw)) {
            $node = $raw;
            $link = MenuNavigationLink::fromSpec($node['link_spec'] ?? null);
            $node['full_url'] = $link['full_url'];
            $node['parametros'] = $link['parametros'];
            $clientAction = self::layoutScalarString($node['client_action'] ?? '');
            if ($clientAction !== '') {
                $node['onClick'] = $clientAction . ';';
            } elseif ($link['full_url'] !== '') {
                $node['onClick'] = "fnjs_link_submenu('{$link['full_url']}','{$link['parametros']}');";
            } else {
                $node['onClick'] = '';
            }
            if (isset($node['submenu']) && is_array($node['submenu'])) {
                $node['submenu'] = self::layoutHydrateMenuNodes($node['submenu']);
            }

            return $node;
        }

        $out = [];
        foreach ($raw as $key => $value) {
            $out[$key] = self::layoutHydrateMenuNodes($value);
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $nodes
     */
    private static function layoutRenderUserMenus(array $nodes): string
    {
        $html = '';
        $indiceOld = 0;
        foreach ($nodes as $node) {
            if (!is_array($node)) {
                continue;
            }
            $indice = self::layoutScalarInt($node['indice'] ?? 0);
            $name = htmlspecialchars(self::layoutScalarString($node['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $onClick = htmlspecialchars(self::layoutScalarString($node['onClick'] ?? ''), ENT_QUOTES, 'UTF-8');
            $submenu = $node['submenu'] ?? [];
            if (!is_array($submenu) || $submenu === []) {
                if ($indiceOld > $indice) {
                    $html .= '</ul></div></li>';
                }
                $html .= '<li><a href="#" onclick="' . $onClick . '">' . $name . '</a></li>';
            } else {
                $html .= '<li><a href="#" class="has-submenu" onclick="">' . $name . '</a>';
                $html .= '<div class="user-dropdown"><ul>';
            }
            $indiceOld = $indice;
        }

        return $html;
    }

    private static function layoutScalarInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }
        if (is_float($value)) {
            return (int) $value;
        }

        return 0;
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
