<?php

namespace frontend\shared\layouts;

use frontend\shared\security\HashFront;
use src\layouts\LayoutInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Layout hamburguesa (grupmenús en columna).
 *
 * HTML de menú y firma de enlaces: vive en `frontend/`; implementa {@see LayoutInterface} desde `src`.
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
     * @param array $menus El array de menú de entrada con 'smenu', 'a_orden' y 'id_grupmenu'.
     * @return array La estructura de menú jerárquica agrupada.
     */
    function buildMenuStructure(array $menus): array
    {
        $MetaMenuReposiroty = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);
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
                    unset($menus[$key]);
                    continue;
                }
                $url = $oMetamenu->getUrl();
                $id_mod = $oMetamenu->getId_Mod();
            } else {
                $url = '';
                $id_mod = '';
            }
            if (!empty($id_mod) && !ConfigGlobal::is_mod_installed($id_mod)) {
                unset($menus[$key]);
                continue;
            }

            $full_url = '';
            $onClick = '';
            if (!empty($url)) {
                $full_url = ConfigGlobal::getWeb() . '/' . $url;
            }
            $parametros = $itemObject->getParametros();
            $parametros = HashFront::add_hash($parametros, $full_url);
            if (!empty($full_url)) {
                if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                    $onClick = "\"$url;\"";
                } else {
                    $onClick = "fnjs_link_submenu('$full_url','$parametros');";
                }
            }

            $indexedNodes[$pathKey] = [
                'name' => $itemObject->getMenu(),
                'submenu' => [],
                'onClick' => $onClick,
            ];
        }

        $groupedRootNodes = [];
        foreach ($menus as $itemObject) {
            $currentGroup = $itemObject->getId_grupmenu();
            $currentOrder = $itemObject->getOrden();
            $currentPathKey = $currentGroup . '_' . implode('_', $currentOrder);

            $currentNode = &$indexedNodes[$currentPathKey];

            if (count($currentOrder) === 1) {
                if (!isset($groupedRootNodes[$currentGroup])) {
                    $groupedRootNodes[$currentGroup] = [];
                }
                $groupedRootNodes[$currentGroup][] = &$currentNode;
            } else {
                $parentOrder = array_slice($currentOrder, 0, -1);
                $parentPathKey = $currentGroup . '_' . implode('_', $parentOrder);

                if (isset($indexedNodes[$parentPathKey])) {
                    $indexedNodes[$parentPathKey]['submenu'][] = &$currentNode;
                }
            }
        }

        $finalMenuConfig = [];
        foreach ($groupedRootNodes as $groupKey => $rootNodesForGroup) {
            if (!empty($this->listaGrupMenu[$groupKey])) {
                $groupName = $this->listaGrupMenu[$groupKey];
                $finalMenuConfig[$groupName] = $rootNodesForGroup;
            }
        }

        return $finalMenuConfig;
    }

    public function generateMenuHtml(array $params): array
    {
        $MenusDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);

        $this->oPermisoMenu = $params['oPermisoMenu'];
        $id_grupmenu = $params['id_grupmenu'] ?? '1';
        $this->listaGrupMenu = $params['listaGrupMenu'] ?? [];
        $oUsuario = $params['oUsuario'];
        $gm = $params['gm'];

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_grupmenu'] = 1;
        $aWhere['_ordre'] = 'id_grupmenu,orden';
        $cMenusUtilidades = $MenusDbRepository->getMenuDbs($aWhere, $aOperador);

        $user_menus = $this->buildUserMenus($cMenusUtilidades);

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_grupmenu'] = 1;
        $aOperador['id_grupmenu'] = "!=";
        $aWhere['_ordre'] = 'id_grupmenu,orden';
        $cMenuDbs = $MenusDbRepository->getMenuDbs($aWhere, $aOperador);

        $menuConfig = $this->buildMenuStructure($cMenuDbs);

        $this->jsonMenuConfig = json_encode($menuConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

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
            ksort($params['grupMenuData']);

            foreach ($params['grupMenuData'] as $grupMenuItem) {
                $id_gm = $grupMenuItem['id_gm'];
                $grup_menu = $grupMenuItem['grup_menu'];
                $clase = $grupMenuItem['clase'];
                $html_aside .= "<li><a href='#' onclick=\"setActiveGroup(this, '$grup_menu');\" >$grup_menu</a></li>";
            }
        }

        $html_exit = "<hr><li><a href=\"#\" onclick=\"fnjs_logout();\" >" . ucfirst(_("salir"))
            . ' (' . ConfigGlobal::mi_usuario() . '[' . ConfigGlobal::mi_region_dl() . '])'
            . "</a></li>";
        $html_aside .= $html_exit;

        $html_aside .= "</ul>";
        $html_aside .= "</nav>";
        $html_aside .= "</aside>";

        $li_submenus = '';
        $htmlComponents['li_submenus'] = $li_submenus;
        $htmlComponents['html_aside'] = $html_aside;
        $htmlComponents['user_menus'] = $user_menus;

        return $htmlComponents;
    }

    public function includeCss(array $params): string
    {
        ob_start();

        include_once(ConfigGlobal::$dir_estilos . '/layout_hamburguesa.css.php');

        return ob_get_clean();
    }

    public function includeJs(array $params): string
    {
        $defaultGrupMenu = (empty($params['id_grupmenu'])) ? '' : $this->listaGrupMenu[$params['id_grupmenu']];
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

    public function renderHtml(array $htmlComponents, array $params): string
    {
        $li_submenus = $htmlComponents['li_submenus'] ?? '';
        $html_aside = $htmlComponents['html_aside'] ?? '';
        $user_menus = $htmlComponents['user_menus'] ?? '';

        ob_start();

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

    private function buildUserMenus(array|bool $cMenusUtilidades)
    {
        $MetaMenuReposiroty = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);

        $indexedNodes = [];
        foreach ($cMenusUtilidades as $itemObject) {
            $orden = $itemObject->getOrden();
            $id_grupmenu = $itemObject->getId_grupmenu();
            $pathKey = $id_grupmenu . '_' . implode('_', $orden);
            if (empty($orden)) {
                continue;
            }
            $id_metamenu = $itemObject->getId_metamenu();

            if (count($orden) === 1) {
                continue;
            }

            $url = '';
            if (!empty($id_metamenu)) {
                $oMetamenu = $MetaMenuReposiroty->findById($id_metamenu);
                if ($oMetamenu === null) {
                    continue;
                }
                $url = $oMetamenu->getUrl();
            }
            $full_url = '';
            $onClick = '';
            if (!empty($url)) {
                $full_url = ConfigGlobal::getWeb() . '/' . $url;
            }
            $parametros = $itemObject->getParametros();
            $parametros = HashFront::add_hash($parametros, $full_url);
            if (!empty($full_url)) {
                if (!is_null($url) && strstr($url, 'fnjs') !== false) {
                    $onClick = "$url;";
                } else {
                    $onClick = "fnjs_link_submenu('$full_url','$parametros');";
                }
            }

            $indexedNodes[$pathKey] = [
                'name' => $itemObject->getMenu(),
                'submenu' => [],
                'onClick' => $onClick,
            ];
        }

        $groupedRootNodes = [];
        foreach ($cMenusUtilidades as $itemObject) {
            $currentGroup = $itemObject->getId_grupmenu();
            $currentOrder = $itemObject->getOrden();
            $currentPathKey = $currentGroup . '_' . implode('_', $currentOrder);

            $currentNode = &$indexedNodes[$currentPathKey];

            if (count($currentOrder) === 1) {
                if (!isset($groupedRootNodes[$currentGroup])) {
                    $groupedRootNodes[$currentGroup] = [];
                }
                $groupedRootNodes[$currentGroup][] = &$currentNode;
            } else {
                $parentOrder = array_slice($currentOrder, 0, -1);
                $parentPathKey = $currentGroup . '_' . implode('_', $parentOrder);

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
