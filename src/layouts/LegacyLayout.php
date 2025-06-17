<?php

namespace src\layouts;

use core\ConfigGlobal;
use src\menus\application\repositories\MenuDbRepository;
use src\menus\application\repositories\MetaMenuRepository;
use web\Hash;

/**
 * Legacy layout implementation
 * 
 * This class implements the LayoutInterface for the legacy layout.
 */
class LegacyLayout implements LayoutInterface
{
    /**
     * Generate the HTML for the menus
     * 
     * @param array $cMenuDbs Array of MenuDb objects
     * @param array $params Additional parameters needed for rendering
     * @return array Associative array with HTML components
     */
    public function generateMenuHtml(array $params): array
    {
        $MenusDbRepository = new MenuDbRepository();

        $htmlComponents = [];
        $li_submenus = "";
        $indice_old = 1;
        $id_grupmenu = $params['id_grupmenu'] ?? '1';
        $oPermisoMenu = $params['oPermisoMenu'];
        $oUsuario = $params['oUsuario'];
        $gm = $params['gm'];

        // El grupmenu 'Utilidades' es el 1, lo pongo siempre.
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
        $MetaMenuReposiroty = new MetaMenuRepository();

        // Process MenuDb objects to generate menu data
        $menuData = [];

        foreach ($cMenuDbs as $oMenuDb) {
            $m++;
            $orden = $oMenuDb->getOrden();
            $menu = $oMenuDb->getMenu();
            $parametros = $oMenuDb->getParametros();
            $id_metamenu = $oMenuDb->getId_metamenu();
            $menu_perm = $oMenuDb->getMenu_perm();
            $id_grupmenu = $oMenuDb->getId_grupmenu();
            //$ok = $oMenuDb->getOk ();

            if (!empty($id_metamenu)) {
                $oMetamenu = $MetaMenuReposiroty->findById($id_metamenu);
                if ($oMetamenu === null) {
                    echo sprintf(_("Este metamenu no existe (id): %s"), $id_metamenu);
                    echo "<br>";
                    continue;
                }
                $url = $oMetamenu->getUrl();
                //echo "m: $perm_menu,l: $perm_login, ".visible($perm_menu,$perm_login) ;
                // primero si el módulo està instalado:
                $id_mod = $oMetamenu->getId_Mod();
            } else {
                $url = '';
                $id_mod = '';
            }
            if (!empty($id_mod) && !ConfigGlobal::is_mod_installed($id_mod)) {
                continue;
            }
            // primero si la app de la ruta está instalada:
            if (!empty($url)) {
                $matches = [];
                $rta = preg_match('@apps/(.+?)/@', $url, $matches);
                if ($rta === false) {
                    echo _("error no hay menu");
                } else {
                    if ($rta === 1) {
                        $url_app = $matches[1];
                        if (!ConfigGlobal::is_app_installed($url_app)) continue;
                    } else {
                        //echo " | ". _("url invàlida en $menu");
                    }
                }
            }
            // compruebo que el menu raíz exista:
            if (!empty($orden)) {
                $raiz = $orden[0];
                if (count($orden) === 1) {
                    $raiz_pral = $raiz;
                }
                if ($raiz != $raiz_pral) {
                    continue;
                }
            }

            // hago las rutas absolutas, en vez de relativas:
            $full_url = '';
            if (!empty($url)) $full_url = ConfigGlobal::getWeb() . '/' . $url;
            //$parametros = Hash::param($full_url,$parametros);
            $parametros = Hash::add_hash($parametros, $full_url);
            // quito las llaves "{}"
            $indice = count($orden);
            if ($orden[0] === $num_menu_1) {
                // continue;
            }
            if ($indice == 1 && !$oPermisoMenu->visible($menu_perm)) {
                $num_menu_1 = $orden[0];
                continue;
            } else {
                $num_menu_1 = 0;
                if (!$oPermisoMenu->visible($menu_perm)) {
                    continue;
                }
            }

            // Add menu item to the menu data array
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

        // Reset indice_old for HTML generation
        $indice_old = 1;

        // Process menu data to generate HTML
        foreach ($menuData as $menuItem) {
            $indice = $menuItem['indice'];
            $menu = $menuItem['menu'];
            $url = $menuItem['url'] ?? null;
            $full_url = $menuItem['full_url'] ?? '';
            $parametros = $menuItem['parametros'] ?? '';
            $menu_perm = $menuItem['menu_perm'] ?? '';

            // Skip if not visible
            if (!$oPermisoMenu->visible($menu_perm)) {
                continue;
            }

            if ($indice == $indice_old) {
                if (!empty($full_url)) {
                    if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                        $li_submenus .= "<li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . "</a>";
                    } else {
                        $li_submenus .= "<li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
                    }
                } else {
                    $li_submenus .= "<li><a class=\"nohref dropdown\" >" . _($menu) . "</a>";
                }
            } elseif ($indice > $indice_old) {
                if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                    $li_submenus .= "<ul><li><a class=\"nohref dropdown\" onclick=\"$url;\"  >" . _($menu) . "</a>";
                } else {
                    $li_submenus .= "<ul><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
                }
            } else {
                for ($n = $indice; $n < $indice_old; $n++) {
                    $li_submenus .= "</li></ul>";
                }
                if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                    $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"$url;\"  >" . _($menu) . "</a>";
                } else {
                    $li_submenus .= "</li><li><a class=\"nohref\" onclick=\"fnjs_link_submenu('$full_url','$parametros');\"  >" . _($menu) . "</a>";
                }
            }

            $indice_old = $indice;
        }

        // Close any open tags
        for ($n = 1; $n < $indice_old; $n++) {
            $li_submenus .= "</li></ul>";
        }
        $li_submenus .= "</li>";

        // Add exit link if needed
        if ($gm < 2) {
            $html_exit = "<li><a class=\"nohref\" onclick=\"fnjs_logout();\" >| " . ucfirst(_("salir")) . "</a></li>";
            $html_exit .= "<li><a class=\"nohref\"> (login as: " . $oUsuario->getUsuarioAsString() . '[' . ConfigGlobal::mi_region_dl() . "])</a></li>";

            $li_submenus .= $html_exit;
        }
        $li_submenus .= "</ul>";

        // Generate HTML for the top menu bar
        $html_barra = "<ul id=\"menu\" class=\"menu\">";
        if (isset($params['grupMenuData'])) {
            // Sort the group menu data by key (order)
            ksort($params['grupMenuData']);

            // Generate HTML for each group menu item
            foreach ($params['grupMenuData'] as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $clase = $grupMenuItem['clase'];
                $html_barra .= "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
            }
        }

        // Add exit link to the top menu
        $html_exit = "<li onclick=\"fnjs_logout();\" >" . ucfirst(_("salir"));
        $html_exit .= " (login as: " . $oUsuario->getUsuarioAsString() . '[' . ConfigGlobal::mi_region_dl() . "])</li>";
        $html_barra .= $html_exit;
        $html_barra .= "</ul>";

        $htmlComponents['li_submenus'] = $li_submenus;
        $htmlComponents['html_barra'] = $html_barra;

        return $htmlComponents;
    }

    /**
     * Include CSS files and inline styles
     * 
     * @param array $params Additional parameters needed for CSS inclusion
     * @return string HTML for CSS inclusion
     */
    public function includeCss(array $params): string
    {
        $tipo_menu = $params['tipo_menu'] ?? 'horizontal';

        ob_start();
        ?>
        <!-- ULTIMATE DROP DOWN MENU Version 4.5 by Brothercake -->
        <!-- http://www.udm4.com/
               -->
        <link rel="stylesheet" type="text/css"
              href="<?= ConfigGlobal::getWeb_scripts() ?>/udm4-php/udm-resources/udm-style.php?PHPSESSID=<?= session_id() ?>"
              media="screen, projection"/>
        <?php
        //include_once (ConfigGlobal::$dir_scripts.'/udm4-php/udm-resources/udm-style.php');
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

    /**
     * Include JavaScript files and inline scripts
     * 
     * @param array $params Additional parameters needed for JavaScript inclusion
     * @return string HTML for JavaScript inclusion
     */
    public function includeJs(array $params): string
    {
        return '';
    }

    /**
     * Render the final HTML structure
     * 
     * @param array $htmlComponents Associative array with HTML components
     * @param array $params Additional parameters needed for rendering
     * @return string Final HTML structure
     */
    public function renderHtml(array $htmlComponents, array $params): string
    {
        $li_submenus = $htmlComponents['li_submenus'] ?? '';
        $html_barra = $htmlComponents['html_barra'] ?? '';
        $gm = $params['gm'] ?? 0;

        ob_start();

        // Show the top menu bar if there are multiple group menus
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
