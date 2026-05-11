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
        $id_grupmenu = (string)($params['id_grupmenu'] ?? '1');
        $oPermisoMenu = $params['oPermisoMenu'];
        $oUsuario = $params['oUsuario'];
        $gm = $params['gm'];

        $payload = PostRequest::getDataFromUrl('/src/menus/menus_legacy_layout_items_data', [
            'id_grupmenu' => $id_grupmenu,
        ]);
        $menuData = [];
        if (is_array($payload) && isset($payload['items']) && is_array($payload['items'])) {
            $menuData = $payload['items'];
        }

        foreach ($menuData as $menuItem) {
            $indice = (int)($menuItem['indice'] ?? 0);
            $menu = $menuItem['menu'] ?? '';
            $url = $menuItem['url'] ?? null;
            $full_url = $menuItem['full_url'] ?? '';
            $parametros = $menuItem['parametros'] ?? '';
            $menu_perm = $menuItem['menu_perm'] ?? '';

            if (!$oPermisoMenu->visible($menu_perm)) {
                continue;
            }

            if ($indice == $indice_old) {
                if (!empty($full_url)) {
                    if (!is_null($url) && str_contains((string)$url, 'fnjs')) {
                        $li_submenus .= "<li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . '</a>';
                    } else {
                        $li_submenus .= "<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . '</a>';
                    }
                } else {
                    $li_submenus .= "<li><a class=\"nohref dropdown\" >" . _($menu) . '</a>';
                }
            } elseif ($indice > $indice_old) {
                if (!is_null($url) && str_contains((string)$url, 'fnjs')) {
                    $li_submenus .= "<ul><li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . '</a>';
                } else {
                    $li_submenus .= "<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . '</a>';
                }
            } else {
                for ($n = $indice; $n < $indice_old; $n++) {
                    $li_submenus .= '</li></ul>';
                }
                if (!is_null($url) && str_contains((string)$url, 'fnjs')) {
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

        if ($gm < 2) {
            $html_exit = "<li><a class=\"nohref\" onclick=\"fnjs_logout();\" >| " . ucfirst(_('salir')) . '</a></li>';
            $html_exit .= '<li><a class="nohref"> (login as: ' . $oUsuario->getUsuarioAsString() . '[' . OrbixRuntime::miRegionDl() . '])</a></li>';

            $li_submenus .= $html_exit;
        }
        $li_submenus .= '</ul>';

        $html_barra = '<ul id="menu" class="menu">';
        if (isset($params['grupMenuData'])) {
            ksort($params['grupMenuData']);

            foreach ($params['grupMenuData'] as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $clase = $grupMenuItem['clase'];
                $html_barra .= "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
            }
        }

        $html_exit = '<li onclick="fnjs_logout();" >' . ucfirst(_('salir'));
        $html_exit .= ' (login as: ' . $oUsuario->getUsuarioAsString() . '[' . OrbixRuntime::miRegionDl() . '])</li>';
        $html_barra .= $html_exit;
        $html_barra .= '</ul>';

        $htmlComponents['li_submenus'] = $li_submenus;
        $htmlComponents['html_barra'] = $html_barra;

        return $htmlComponents;
    }

    public function includeCss(array $params): string
    {
        $tipo_menu = $params['tipo_menu'] ?? 'horizontal';

        ob_start();
        ?>
        <!-- ULTIMATE DROP DOWN MENU Version 4.5 by Brothercake -->
        <link rel="stylesheet"
              href="<?= OrbixRuntime::getWebScripts() ?>/udm4-php/udm-resources/udm-style.php"
              media="screen, projection"/>
        <?php
        switch ($tipo_menu) {
            case 'horizontal':
                include_once (OrbixRuntime::dirEstilos() . '/menu_horizontal.css.php');
                break;
            case 'vertical':
                include_once (OrbixRuntime::dirEstilos() . '/menu_vertical.css.php');
                break;
        }

        return ob_get_clean();
    }

    public function includeJs(array $params): string
    {
        return '';
    }

    public function renderHtml(array $htmlComponents, array $params): string
    {
        $li_submenus = $htmlComponents['li_submenus'] ?? '';
        $html_barra = $htmlComponents['html_barra'] ?? '';
        $gm = $params['gm'] ?? 0;

        ob_start();

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
        <?php

        return ob_get_clean();
    }
}
