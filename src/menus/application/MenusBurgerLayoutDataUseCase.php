<?php

namespace src\menus\application;

use frontend\shared\security\HashFront;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\shared\config\ConfigGlobal;

/**
 * Datos para {@see \frontend\shared\layouts\BurgerLayout} y {@see \frontend\shared\layouts\PillsLayout}:
 * "Utilidades", sin acceso a repos desde `frontend/`.
 */
final class MenusBurgerLayoutDataUseCase
{
    public function __construct(
        private MenuDbRepositoryInterface $menuDbRepository,
        private MetaMenuRepositoryInterface $metaMenuRepository,
    ) {
    }

    /**
     * @param array<int|string, string> $listaGrupMenu id_grupmenu => etiqueta (como en index.php)
     * @return array{menu_config: array<string, mixed>, user_menus_html: string}
     */
    public function __invoke(array $listaGrupMenu): array
    {

        $aWhere = [
            'id_grupmenu' => 1,
            '_ordre' => 'id_grupmenu,orden',
        ];
        $cMenusUtilidades = $this->menuDbRepository->getMenuDbs($aWhere, []);

        $aWhere = [
            'id_grupmenu' => 1,
            '_ordre' => 'id_grupmenu,orden',
        ];
        $aOperador = ['id_grupmenu' => '!='];
        $cMenuDbs = $this->menuDbRepository->getMenuDbs($aWhere, $aOperador);

        $userMenusHtml = $this->buildUserMenus($cMenusUtilidades);
        $menuConfig = $this->buildMenuStructure($cMenuDbs, $listaGrupMenu);

        return [
            'menu_config' => $menuConfig,
            'user_menus_html' => $userMenusHtml,
        ];
    }

    /**
     * @param list<MenuDb> $menus
     * @param array<int|string, string> $listaGrupMenu
     * @return array<string, mixed>
     */
    private function buildMenuStructure(array $menus, array $listaGrupMenu): array
    {
        $indexedNodes = [];
        foreach ($menus as $key => $itemObject) {
            $pathKey = $itemObject->getId_grupmenu() . '_' . implode('_', $itemObject->getOrden() ?? []);
            $orden = $itemObject->getOrden() ?? [];
            if (empty($orden)) {
                continue;
            }
            $id_metamenu = $itemObject->getId_metamenu();
            if (!empty($id_metamenu)) {
                $oMetamenu = $this->metaMenuRepository->findById($id_metamenu);
                if ($oMetamenu === null) {
                    unset($menus[$key]);
                    continue;
                }
                $url = $oMetamenu->getUrl() ?? '';
                $id_mod = $oMetamenu->getId_mod();
            } else {
                $url = '';
                $id_mod = null;
            }
            if (!empty($id_mod) && !ConfigGlobal::is_mod_installed((int)$id_mod)) {
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
                if (strstr($url, 'fnjs') !== false) {
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
            $currentOrder = $itemObject->getOrden() ?? [];
            if ($currentOrder === []) {
                continue;
            }
            $currentPathKey = $currentGroup . '_' . implode('_', $currentOrder);

            if (!isset($indexedNodes[$currentPathKey])) {
                continue;
            }
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
            if (!empty($listaGrupMenu[$groupKey])) {
                $groupName = $listaGrupMenu[$groupKey];
                $finalMenuConfig[$groupName] = $rootNodesForGroup;
            }
        }

        return $finalMenuConfig;
    }

    /**
     * @param list<MenuDb> $cMenusUtilidades
     */
    private function buildUserMenus(array $cMenusUtilidades): string
    {

        $indexedNodes = [];
        foreach ($cMenusUtilidades as $itemObject) {
            $orden = $itemObject->getOrden() ?? [];
            $id_grupmenu = $itemObject->getId_grupmenu();
            $pathKey = $id_grupmenu . '_' . implode('_', $orden);
            if ($orden === []) {
                continue;
            }
            $id_metamenu = $itemObject->getId_metamenu();

            if (count($orden) === 1) {
                continue;
            }

            $url = '';
            if (!empty($id_metamenu)) {
                $oMetamenu = $this->metaMenuRepository->findById($id_metamenu);
                if ($oMetamenu === null) {
                    continue;
                }
                $url = $oMetamenu->getUrl() ?? '';
            }
            $full_url = '';
            $onClick = '';
            if (!empty($url)) {
                $full_url = ConfigGlobal::getWeb() . '/' . $url;
            }
            $parametros = $itemObject->getParametros();
            $parametros = HashFront::add_hash($parametros, $full_url);
            if (!empty($full_url)) {
                if (strstr($url, 'fnjs') !== false) {
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
            $currentOrder = $itemObject->getOrden() ?? [];
            if ($currentOrder === []) {
                continue;
            }
            $currentPathKey = $currentGroup . '_' . implode('_', $currentOrder);

            if (!isset($indexedNodes[$currentPathKey])) {
                continue;
            }
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
            $indice = substr_count((string)$key, '_');
            if (empty($node['submenu'])) {
                if ($indice_old > $indice) {
                    $li_submenus .= '</ul></div></li>';
                }
                $li_submenus .= "<li><a href='#' onclick=\"" . $node['onClick'] . '"  >' . $node['name'] . '</a></li>';
            } else {
                $li_submenus .= "<li><a href='#'  class=\"has-submenu\" onclick=\"\"  >" . $node['name'] . '</a>';
                $li_submenus .= '<div class="user-dropdown"> <ul>';
            }
            $indice_old = $indice;
        }

        return $li_submenus;
    }
}
