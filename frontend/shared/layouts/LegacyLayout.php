<?php

namespace frontend\shared\layouts;

use frontend\shared\security\HashFront;
use src\layouts\LayoutInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Layout clásico (árbol UDM + barra de grupos).
 *
 * Implementa {@see LayoutInterface}; firma de enlaces vía {@see HashFront} en capa frontend.
 */
class LegacyLayout implements LayoutInterface
{
    public function generateMenuHtml(array $params): array
    {
        $MenusDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
        $MetaMenuRepository = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);

        $htmlComponents = [];
        $li_submenus = "";
        $indice_old = 1;
        $id_grupmenu = $params['id_grupmenu'] ?? '1';
        $oPermisoMenu = $params['oPermisoMenu'];
        $oUsuario = $params['oUsuario'];
        $gm = $params['gm'];

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_grupmenu'] = "^1$|^$id_grupmenu$";
        $aOperador['id_grupmenu'] = "~";
        $aWhere['_ordre'] = 'orden';
        $cMenuDbs = $MenusDbRepository->getMenuDbs($aWhere, $aOperador);

        $indice = 1;
        $num_menu_1 = 0;
        $m = 0;
        $raiz_pral = '';

        $menuData = [];

        foreach ($cMenuDbs as $oMenuDb) {
            $m++;
            $orden = $oMenuDb->getOrden();
            $menu = $oMenuDb->getMenu();
            $parametros = $oMenuDb->getParametros();
            $id_metamenu = $oMenuDb->getId_metamenu();
            $menu_perm = $oMenuDb->getMenu_perm();
            $id_grupmenu = $oMenuDb->getId_grupmenu();

            if (!empty($id_metamenu)) {
                $oMetamenu = $MetaMenuRepository->findById($id_metamenu);
                if ($oMetamenu === null) {
                    echo sprintf(_("Este metamenu no existe (id): %s"), $id_metamenu);
                    echo "<br>";
                    continue;
                }
                $url = $oMetamenu->getUrl();
                $id_mod = $oMetamenu->getId_Mod();
            } else {
                $url = '';
                $id_mod = '';
            }
            if (!empty($id_mod) && !ConfigGlobal::is_mod_installed($id_mod)) {
                continue;
            }
            if (!empty($url)) {
                $matches = [];
                $rta = preg_match('@apps/(.+?)/@', $url, $matches);
                if ($rta === false) {
                    echo _("error no hay menu");
                } else {
                    if ($rta === 1) {
                        $url_app = $matches[1];
                        if (!ConfigGlobal::is_app_installed($url_app)) {
                            continue;
                        }
                    }
                }
            }
            if (!empty($orden)) {
                $raiz = $orden[0];
                if (count($orden) === 1) {
                    $raiz_pral = $raiz;
                }
                if ($raiz != $raiz_pral) {
                    continue;
                }
            }

            $full_url = '';
            if (!empty($url)) {
                $full_url = ConfigGlobal::getWeb() . '/' . $url;
            }
            $parametros = HashFront::add_hash($parametros, $full_url);
            $indice = count($orden);
            if ($orden[0] === $num_menu_1) {
                // continue;
            }
            if ($indice == 1 && !$oPermisoMenu->visible($menu_perm)) {
                $num_menu_1 = $orden[0];
                continue;
            }

            $num_menu_1 = 0;
            if (!$oPermisoMenu->visible($menu_perm)) {
                continue;
            }

            $menuData[] = [
                'indice' => $indice,
                'menu' => $menu,
                'url' => $url,
                'full_url' => $full_url,
                'parametros' => $parametros,
                'menu_perm' => $menu_perm
            ];

            $indice_old = $indice;
        }

        $indice_old = 1;

        foreach ($menuData as $menuItem) {
            $indice = $menuItem['indice'];
            $menu = $menuItem['menu'];
            $url = $menuItem['url'] ?? null;
            $full_url = $menuItem['full_url'] ?? '';
            $parametros = $menuItem['parametros'] ?? '';
            $menu_perm = $menuItem['menu_perm'] ?? '';

            if (!$oPermisoMenu->visible($menu_perm)) {
                continue;
            }

            if ($indice == $indice_old) {
                if (!empty($full_url)) {
                    if (!is_null($url) && str_contains($url, 'fnjs')) {
                        $li_submenus .= "<li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . "</a>";
                    } else {
                        $li_submenus .= "<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
                    }
                } else {
                    $li_submenus .= "<li><a class=\"nohref dropdown\" >" . _($menu) . "</a>";
                }
            } elseif ($indice > $indice_old) {
                if (!is_null($url) && str_contains($url, 'fnjs')) {
                    $li_submenus .= "<ul><li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . "</a>";
                } else {
                    $li_submenus .= "<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
                }
            } else {
                for ($n = $indice; $n < $indice_old; $n++) {
                    $li_submenus .= "</li></ul>";
                }
                if (!is_null($url) && str_contains($url, 'fnjs')) {
                    $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"$url;\"  >" . _($menu) . "</a>";
                } else {
                    $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
                }
            }

            $indice_old = $indice;
        }

        for ($n = 1; $n < $indice_old; $n++) {
            $li_submenus .= "</li></ul>";
        }
        $li_submenus .= "</li>";

        if ($gm < 2) {
            $html_exit = "<li><a class=\"nohref\" onclick=\"fnjs_logout();\" >| " . ucfirst(_("salir")) . "</a></li>";
            $html_exit .= "<li><a class=\"nohref\"> (login as: " . $oUsuario->getUsuarioAsString() . '[' . ConfigGlobal::mi_region_dl() . "])</a></li>";

            $li_submenus .= $html_exit;
        }
        $li_submenus .= "</ul>";

        $html_barra = "<ul id=\"menu\" class=\"menu\">";
        if (isset($params['grupMenuData'])) {
            ksort($params['grupMenuData']);

            foreach ($params['grupMenuData'] as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $clase = $grupMenuItem['clase'];
                $html_barra .= "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
            }
        }

        $html_exit = "<li onclick=\"fnjs_logout();\" >" . ucfirst(_("salir"));
        $html_exit .= " (login as: " . $oUsuario->getUsuarioAsString() . '[' . ConfigGlobal::mi_region_dl() . "])</li>";
        $html_barra .= $html_exit;
        $html_barra .= "</ul>";

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
              href="<?= ConfigGlobal::getWeb_scripts() ?>/udm4-php/udm-resources/udm-style.php"
              media="screen, projection"/>
        <?php
        switch ($tipo_menu) {
            case "horizontal":
                include_once(ConfigGlobal::$dir_estilos . '/menu_horizontal.css.php');
                break;
            case "vertical":
                include_once(ConfigGlobal::$dir_estilos . '/menu_vertical.css.php');
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
                require_once(ConfigGlobal::$dir_scripts . "/udm4-php/udm-resources/udm-dom.php");
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
