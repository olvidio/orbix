<?php

namespace src\layouts;

use core\ConfigGlobal;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use web\Hash;

/**
 * New layout implementation
 *
 * This class implements the LayoutInterface for the new layout with grupmenus in a column on the left.
 */
class BurgerLayout implements LayoutInterface
{

    private string|false $jsonMenuConfig;
    private mixed $oPermisoMenu;
    /**
     * @var array|mixed
     */
    private array $listaGrupMenu;

    /**
     * Convierte un array de menú PHP plano en una estructura jerárquica de menú,
     * agrupada por el valor 'id_grupmenu'.
     *
     * @param array $menus El array de menú de entrada con 'smenu', 'a_orden' y 'id_grupmenu'.
     * @return array La estructura de menú jerárquica agrupada.
     */
    function buildMenuStructure(array $menus): array
    {
        $MetaMenuReposiroty = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);
        // Almacena todos los nodos del menú, indexados por una clave que combina 'id_grupmenu' y 'a_orden'.
        // Esto permite un acceso rápido a cualquier nodo por su "ruta" única.
        $indexedNodes = [];
        foreach ($menus as $key => $itemObject) {
            $pathKey = $itemObject->getId_grupmenu() . '_' . implode('_', $itemObject->getOrden());
            $orden = $itemObject->getOrden();
            if (empty($orden)) {
                continue;
            }
            $id_metamenu = $itemObject->getId_metamenu();
            if (!empty($id_metamenu)) {
                $oMetamenu = $MetaMenuReposiroty->findById($id_metamenu);
                if ($oMetamenu === null) {
                    //echo sprintf(_("Este metamenu no existe (id): %s"), $id_metamenu);
                    //echo "<br>";
                    unset($menus[$key]);
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
                // lo quito de la lista:
                unset($menus[$key]);
                continue;
            }

            // hago las rutas absolutas, en vez de relativas:
            $full_url = '';
            $onClick = '';
            if (!empty($url)) $full_url = ConfigGlobal::getWeb() . '/' . $url;
            $parametros = $itemObject->getParametros();
            $parametros = Hash::add_hash($parametros, $full_url);
            if (!empty($full_url)) {
                if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                    $onClick = "\"$url;\"";
                } else {
                    $onClick = "fnjs_link_submenu('$full_url','$parametros');";
                }
            }

            $indexedNodes[$pathKey] = [
                'name' => $itemObject->getMenu(),
                'submenu' => [], // Todos los nodos se inicializan con un submenú vacío.
                'onClick' => $onClick,
            ];
        }

        // Construir la jerarquía del menú utilizando referencias.
        // `groupedRootNodes` almacenará los nodos de nivel superior, agrupados por `id_grupmenu`.
        $groupedRootNodes = [];
        foreach ($menus as $itemObject) {
            $currentGroup = $itemObject->getId_grupmenu();
            $currentOrder = $itemObject->getOrden();
            $currentPathKey = $currentGroup . '_' . implode('_', $currentOrder);

            // Obtener una referencia al nodo actual de `$indexedNodes`.
            // Las modificaciones a `$currentNode` se reflejarán en `$indexedNodes`.
            $currentNode = &$indexedNodes[$currentPathKey];

            // Si la longitud de 'a_orden' es 1, es un elemento de nivel superior para su grupo.
            if (count($currentOrder) === 1) {
                if (!isset($groupedRootNodes[$currentGroup])) {
                    $groupedRootNodes[$currentGroup] = [];
                }
                // Añadir la referencia al nodo actual al array de nodos raíz de su grupo.
                $groupedRootNodes[$currentGroup][] = &$currentNode;
            } else {
                // Si es un subelemento, calcular la clave del padre.
                $parentOrder = array_slice($currentOrder, 0, -1);
                $parentPathKey = $currentGroup . '_' . implode('_', $parentOrder);

                // Si el padre existe, adjuntar el nodo actual al submenú de su padre.
                if (isset($indexedNodes[$parentPathKey])) {
                    $indexedNodes[$parentPathKey]['submenu'][] = &$currentNode;
                }
            }
        }

        // Ensamblar el array final del menú, agrupado por 'id_grupmenu'.
        $finalMenuConfig = [];
        foreach ($groupedRootNodes as $groupKey => $rootNodesForGroup) {
            // Asignar el menú del grupo al índice 'id_grupmenu' correspondiente en el array final.
            if (!empty($this->listaGrupMenu[$groupKey])) {
                $groupName = $this->listaGrupMenu[$groupKey];
                $finalMenuConfig[$groupName] = $rootNodesForGroup;
            }
        }

        return $finalMenuConfig;
    }

    /**
     * Generate the HTML for the menus
     *
     * @param array $params Array of Params objects
     */
    public function generateMenuHtml(array $params): array
    {
        $MenusDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);

        $this->oPermisoMenu = $params['oPermisoMenu'];
        $id_grupmenu = $params['id_grupmenu'] ?? '1';
        $this->listaGrupMenu = $params['listaGrupMenu'] ?? [];
        $oUsuario = $params['oUsuario'];
        $gm = $params['gm'];

        // El grupmenu 'Utilidades' es el 1, lo pongo siempre a parte.
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_grupmenu'] = 1;
        $aWhere['_ordre'] = 'id_grupmenu,orden';
        $cMenusUtilidades = $MenusDbRepository->getMenuDbs($aWhere, $aOperador);

        $user_menus = $this->buildUserMenus($cMenusUtilidades);

        // El grupmenu 'Utilidades' es el 1, lo pongo siempre a parte.
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_grupmenu'] = 1;
        $aOperador['id_grupmenu'] = "!=";
        $aWhere['_ordre'] = 'id_grupmenu,orden';
        $cMenuDbs = $MenusDbRepository->getMenuDbs($aWhere, $aOperador);

        $menuConfig = $this->buildMenuStructure($cMenuDbs);

        // To output as JSON (similar to JavaScript object)
        $this->jsonMenuConfig = json_encode($menuConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Generate HTML for the sidebar
        $html_aside = "
          <!-- Botón toggle para móvil -->
            <button class=\"mobile-toggle\" id=\"mobileToggle\"  onclick=\"toggleSidebar()\">
            ☰
                <div class=\"sidebar-header\">
                    <h2 id =\"sidebar-header-h2\">Oficinas</h2>
                </div>
            </button>

            <!-- Overlay para móvil -->
            <div class=\"overlayMenu\" id=\"overlayMenu\" onclick=\"toggleSidebar()\"></div>

            <div class=\"main-container\" id=\"mainContent\">
                <aside class=\"sidebar\" id=\"sidebar\">
                <nav class=\"group-menu\" id=\"groupMenu\">
                    <ul>";

        if (isset($params['grupMenuData'])) {
            // Sort the group menu data by key (order)
            ksort($params['grupMenuData']);

            // Generate HTML for each group menu item
            foreach ($params['grupMenuData'] as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $clase = $grupMenuItem['clase'];
                //$html_aside .= "<li onclick=\"fnjs_link_menu('$id_gm');\" $clase >$grup_menu</li>";
                $html_aside .= "<li><a href='#' onclick=\"setActiveGroup(this, '$grup_menu');\" >$grup_menu</a></li>";
            }
        }

        // Add exit link to the sidebar
        $html_exit = "<hr><li><a href=\"#\" onclick=\"fnjs_logout();\" >" . ucfirst(_("salir"))
            . ' (' . ConfigGlobal::mi_usuario() . '[' . ConfigGlobal::mi_region_dl() . '])'
            . "</a></li>";
        $html_aside .= $html_exit;

        $html_aside .= "</ul>";
        $html_aside .= "</nav>";
        $html_aside .= "</aside>";

        // de momento:
        $li_submenus = '';
        $htmlComponents['li_submenus'] = $li_submenus;
        $htmlComponents['html_aside'] = $html_aside;
        $htmlComponents['user_menus'] = $user_menus;

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
        ob_start();

        // Include CSS files
        include_once(ConfigGlobal::$dir_estilos . '/layout_hamburguesa.css.php');

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
        $defaultGrupMenu = $this->listaGrupMenu[$params['id_grupmenu']];
        ob_start();
        ?>
        <!-- Configuración de menús por grupo -->
        <script>
            const defaultGrupMenu = '<?= $defaultGrupMenu ?>';
        </script>
        <!--  para layout hamburguesa -->
        <script>
            <?php
               include_once(ConfigGlobal::$dir_scripts . '/layout_hamburguesa.js.php');
            ?>
        </script>
        <!-- Configuración de menús por grupo -->
        <script>
            const menuConfig = <?= $this->jsonMenuConfig ?>;
        </script>
        <?php

        return ob_get_clean();
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
        $html_aside = $htmlComponents['html_aside'] ?? '';
        $user_menus = $htmlComponents['user_menus'] ?? '';

        ob_start();

        // Output the sidebar
        echo $html_aside;
        ?>
        <!-- Contenido principal -->
        <div class="main-content">
        <!-- Menú horizontal superior -->
        <header class="top-menu">
            <nav>
                <ul class="horizontal-menu" id="horizontalMenu">
                    <?= $li_submenus ?>
                </ul>
            </nav>
            <!-- Elemento "Utilidades" alineado a la derecha -->
            <nav class="menu-utilidades-derecha">
                <ul class="user-menu">
                    <li>
                        <a href="#" class="user-menu">👤 Utilidades</a>
                        <div class="user-dropdown" id="navUser">
                            <ul>
                                <?= $user_menus ?>
                                <hr>
                                <li><a href="#" onclick="fnjs_logout();"><?= ucfirst(_("salir")) ?>
                                        <?= "(" . ConfigGlobal::mi_usuario() . '[' . ConfigGlobal::mi_region_dl() . "])" ?>
                                    </a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>
        </header>
        <?php

        return ob_get_clean();
    }

    private function buildUserMenus(array|false $cMenusUtilidades)
    {
        $MetaMenuReposiroty = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);

        $indexedNodes = [];
        foreach ($cMenusUtilidades as $key => $itemObject) {
            $menu = $itemObject->getMenu();
            $orden = $itemObject->getOrden();
            $id_grupmenu = $itemObject->getId_grupmenu();
            $pathKey = $id_grupmenu . '_' . implode('_', $orden);
            if (empty($orden)) {
                continue;
            }
            $id_metamenu = $itemObject->getId_metamenu();

            // me salto el menu raíz:
            if (count($orden) === 1) continue;

            $url = '';
            if (!empty($id_metamenu)) {
                $oMetamenu = $MetaMenuReposiroty->findById($id_metamenu);
                if ($oMetamenu === null) {
                    //echo sprintf(_("Este metamenu no existe (id): %s"), $id_metamenu);
                    //echo "<br>";
                    unset($itemObject[$key]);
                    continue;
                }
                $url = $oMetamenu->getUrl();
            }
            // hago las rutas absolutas, en vez de relativas:
            $full_url = '';
            $onClick = '';
            if (!empty($url)) $full_url = ConfigGlobal::getWeb() . '/' . $url;
            $parametros = $itemObject->getParametros();
            $parametros = Hash::add_hash($parametros, $full_url);
            if (!empty($full_url)) {
                if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                    $onClick = "$url;";
                } else {
                    $onClick = "fnjs_link_submenu('$full_url','$parametros');";
                }
            }

            $indexedNodes[$pathKey] = [
                'name' => $itemObject->getMenu(),
                'submenu' => [], // Todos los nodos se inicializan con un submenú vacío.
                'onClick' => $onClick,
            ];
        }

        // Construir la jerarquía del menú utilizando referencias.
        // `groupedRootNodes` almacenará los nodos de nivel superior, agrupados por `id_grupmenu`.
        $groupedRootNodes = [];
        foreach ($cMenusUtilidades as $itemObject) {
            $currentGroup = $itemObject->getId_grupmenu();
            $currentOrder = $itemObject->getOrden();
            $currentPathKey = $currentGroup . '_' . implode('_', $currentOrder);

            // Obtener una referencia al nodo actual de `$indexedNodes`.
            // Las modificaciones a `$currentNode` se reflejarán en `$indexedNodes`.
            $currentNode = &$indexedNodes[$currentPathKey];

            // Si la longitud de 'a_orden' es 1, es un elemento de nivel superior para su grupo.
            if (count($currentOrder) === 1) {
                if (!isset($groupedRootNodes[$currentGroup])) {
                    $groupedRootNodes[$currentGroup] = [];
                }
                // Añadir la referencia al nodo actual al array de nodos raíz de su grupo.
                $groupedRootNodes[$currentGroup][] = &$currentNode;
            } else {
                // Si es un subelemento, calcular la clave del padre.
                $parentOrder = array_slice($currentOrder, 0, -1);
                $parentPathKey = $currentGroup . '_' . implode('_', $parentOrder);

                // Si el padre existe, adjuntar el nodo actual al submenú de su padre.
                if (isset($indexedNodes[$parentPathKey])) {
                    $indexedNodes[$parentPathKey]['submenu'][] = &$currentNode;
                }
            }
        }

        $li_submenus = '';
        $indice_old = 0;
        foreach ($indexedNodes as $key => $node) {
            if ($node === null) {
                continue;
            }
            $indice = substr_count($key, '_');
            if (empty($node['submenu'])) {
                if ($indice_old > $indice) {
                    $li_submenus .= "</ul></div></li>";
                }
                    $li_submenus .= "<li><a href='#' onclick=\"" . $node['onClick'] . "\"  >" . $node['name'] . "</a></li>";
            } else {
                $li_submenus .= "<li><a href='#'  class=\"has-submenu\" onclick=\"\"  >" . $node['name'] . "</a>";
                $li_submenus .= "<div class=\"user-dropdown\"> <ul>";
            }
            $indice_old = $indice;

        }
        return $li_submenus;
    }

}
