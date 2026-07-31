<?php

namespace frontend\shared\layouts;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;

/**
 * Layout clásico (árbol UDM + barra de grupos).
 *
 * Los ítems de menú se obtienen vía HTTP desde `src/menus`; rutas de estáticos vía {@see OrbixRuntime}.
 */
class LegacyLayout implements LayoutInterface
{
    public function generateMenuHtml(array $params): array
    {
        $htmlComponents = [];
        $li_submenus = '';
        $indice_old = 1;
        $id_grupmenu = self::scalarString($params['id_grupmenu'] ?? '1');
        $oPermisoMenu = $params['oPermisoMenu'] ?? null;
        $oUsuario = $params['oUsuario'] ?? null;
        $gm = self::scalarInt($params['gm'] ?? 0);

        $payload = PostRequest::getDataFromUrl('/src/menus/menus_legacy_layout_items_data', [
            'id_grupmenu' => $id_grupmenu,
        ]);
        $menuData = [];
        if (isset($payload['items']) && is_array($payload['items'])) {
            $menuData = $this->parseMenuItems($payload['items']);
        }

        foreach ($menuData as $menuItem) {
            $indice = $menuItem['indice'];
            $menu = $menuItem['menu'];
            $url = $menuItem['url'];
            $full_url = $menuItem['full_url'];
            $parametros = $menuItem['parametros'];
            $menu_perm = $menuItem['menu_perm'];

            if (!is_object($oPermisoMenu) || !method_exists($oPermisoMenu, 'visible') || !$oPermisoMenu->visible($menu_perm)) {
                continue;
            }

            if ($indice == $indice_old) {
                if ($full_url !== '') {
                    if ($url !== '' && str_contains($url, 'fnjs')) {
                        $li_submenus .= "<li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . '</a>';
                    } else {
                        $li_submenus .= "<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . '</a>';
                    }
                } else {
                    $li_submenus .= "<li><a class=\"nohref dropdown\" >" . _($menu) . '</a>';
                }
            } elseif ($indice > $indice_old) {
                if ($url !== '' && str_contains($url, 'fnjs')) {
                    $li_submenus .= "<ul><li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . '</a>';
                } else {
                    $li_submenus .= "<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . '</a>';
                }
            } else {
                for ($n = $indice; $n < $indice_old; $n++) {
                    $li_submenus .= '</li></ul>';
                }
                if ($url !== '' && str_contains($url, 'fnjs')) {
                    $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"$url;\"  >" . _($menu) . '</a>';
                } else {
                    $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . '</a>';
                }
            }

            $indice_old = $indice;
        }

        for ($n = 1; $n < $indice_old; $n++) {
            $li_submenus .= '</li></ul>';
        }
        $li_submenus .= '</li>';

        $usuarioLabel = (is_object($oUsuario) && method_exists($oUsuario, 'getUsuarioAsString'))
            ? $oUsuario->getUsuarioAsString()
            : '';

        if ($gm < 2) {
            $html_exit = "<li><a class=\"nohref\" onclick=\"fnjs_logout();\" >| " . ucfirst(_('salir')) . '</a></li>';
            $html_exit .= '<li><a class="nohref"> (login as: ' . $usuarioLabel . '[' . OrbixRuntime::miRegionDl() . '])</a></li>';

            $li_submenus .= $html_exit;
        }
        $li_submenus .= '</ul>';

        $html_barra = '<ul id="menu" class="menu">';
        if (isset($params['grupMenuData']) && is_array($params['grupMenuData'])) {
            foreach ($this->parseGrupMenuItems($params['grupMenuData']) as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $clase = $grupMenuItem['clase'];
                $html_barra .= "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
            }
        }

        $html_exit = '<li onclick="fnjs_logout();" >' . ucfirst(_('salir'));
        $html_exit .= ' (login as: ' . $usuarioLabel . '[' . OrbixRuntime::miRegionDl() . '])</li>';
        $html_barra .= $html_exit;
        $html_barra .= '</ul>';

        $htmlComponents['li_submenus'] = $li_submenus;
        $htmlComponents['html_barra'] = $html_barra;

        return $htmlComponents;
    }

    public function includeCss(array $params): string
    {
        $tipo_menu = self::scalarString($params['tipo_menu'] ?? 'horizontal');

        ob_start();
        ?>
        <!-- ULTIMATE DROP DOWN MENU Version 4.5 by Brothercake -->
        <link rel="stylesheet"
              href="<?= OrbixRuntime::getWebScripts() ?>/udm4-php/udm-resources/udm-style.php"
              media="screen, projection"/>
        <?php
        include_once (OrbixRuntime::dirEstilos() . '/layout_legacy.css.php');

        switch ($tipo_menu) {
            case 'horizontal':
                include_once (OrbixRuntime::dirEstilos() . '/menu_horizontal.css.php');
                break;
            case 'vertical':
                include_once (OrbixRuntime::dirEstilos() . '/menu_vertical.css.php');
                break;
        }

        return (string) ob_get_clean();
    }

    public function includeJs(array $params): string
    {
        ob_start();
        ?>
        <script>
            if (!window.__orbixLayoutLegacyJsLoaded) {
                window.__orbixLayoutLegacyJsLoaded = true;
            <?php
            include_once OrbixRuntime::dirScripts() . '/layout_legacy.js.php';
            ?>
            }
        </script>
        <?php

        return (string) ob_get_clean();
    }

    public function renderHtml(array $htmlComponents, array $params): string
    {
        $li_submenus = self::scalarString($htmlComponents['li_submenus'] ?? '');
        $html_barra = self::scalarString($htmlComponents['html_barra'] ?? '');
        $gm = self::scalarInt($params['gm'] ?? 0);

        ob_start();
        ?>
        <div class="orbix-layout-legacy" id="orbixLegacyShell">
        <?php
        if ($gm > 1) {
            echo $html_barra;
        }
        ?>
        <!-- menu tree for legacy layout -->
        <div id="submenu">
            <!-- PHP generated menu script [must come *before* any other modules or extensions] -->
            <script>
                <?php
                require_once (OrbixRuntime::dirScripts() . '/udm4-php/udm-resources/udm-dom.php');
                ?>
            </script>
            <!-- keyboard navigation module -->
            <!-- <script type="text/javascript" src="/udm4-php/udm-resources/udm-mod-keyboard.js"></script> -->
            <ul id="udm" class="udm">
                <?= $li_submenus ?>
            </ul>
        </div>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    private static function scalarString(mixed $value): string
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

    private static function scalarInt(mixed $value): int
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
     * @param array<int|string, mixed> $itemsRaw
     * @return list<array{indice:int,menu:string,url:string,full_url:string,parametros:string,menu_perm:int}>
     */
    private function parseMenuItems(array $itemsRaw): array
    {
        $items = [];
        foreach ($itemsRaw as $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $items[] = [
                'indice' => self::scalarInt($raw['indice'] ?? 0),
                'menu' => self::scalarString($raw['menu'] ?? ''),
                'url' => self::scalarString($raw['url'] ?? ''),
                'full_url' => self::scalarString($raw['full_url'] ?? ''),
                'parametros' => self::scalarString($raw['parametros'] ?? ''),
                'menu_perm' => self::scalarInt($raw['menu_perm'] ?? 0),
            ];
        }

        return $items;
    }

    /**
     * @param array<int|string, mixed> $grupMenuData
     * @return list<array{id_gm:string,grup_menu:string,clase:string}>
     */
    private function parseGrupMenuItems(array $grupMenuData): array
    {
        ksort($grupMenuData);

        $items = [];
        foreach ($grupMenuData as $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $items[] = [
                'id_gm' => self::scalarString($raw['id_gm'] ?? ''),
                'grup_menu' => self::scalarString($raw['grup_menu'] ?? ''),
                'clase' => self::scalarString($raw['clase'] ?? ''),
            ];
        }

        return $items;
    }
}
