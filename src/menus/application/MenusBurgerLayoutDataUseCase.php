<?php

namespace src\menus\application;

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
     * @return array{menu_config: array<string, mixed>, user_menu_nodes: list<array<string, mixed>>}
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

        $userMenuNodes = $this->buildUserMenuNodes($cMenusUtilidades);
        $menuConfig = $this->buildMenuStructure($cMenuDbs, $listaGrupMenu);

        return [
            'menu_config' => $menuConfig,
            'user_menu_nodes' => $userMenuNodes,
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

            $parametros = $itemObject->getParametros();

            $indexedNodes[$pathKey] = [
                'name' => _((string) ($itemObject->getMenu() ?? '')),
                'submenu' => [],
                'link_spec' => self::navigationLinkSpec($url, $parametros),
                'client_action' => str_contains($url, 'fnjs') ? $url : '',
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
                // Misma traducción que en el HTML de grupos (data-grupo / setActiveGroup).
                $groupName = _((string) $listaGrupMenu[$groupKey]);
                $finalMenuConfig[$groupName] = $rootNodesForGroup;
            }
        }

        return $finalMenuConfig;
    }

    /**
     * @param list<MenuDb> $cMenusUtilidades
     */
    private function buildUserMenuNodes(array $cMenusUtilidades): array
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
            $parametros = $itemObject->getParametros();

            $indexedNodes[$pathKey] = [
                'name' => _((string) ($itemObject->getMenu() ?? '')),
                'submenu' => [],
                'indice' => count($orden),
                'link_spec' => self::navigationLinkSpec($url, $parametros),
                'client_action' => str_contains($url, 'fnjs') ? $url : '',
            ];
        }

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
            if (count($currentOrder) > 1) {
                $parentOrder = array_slice($currentOrder, 0, -1);
                $parentPathKey = $currentGroup . '_' . implode('_', $parentOrder);
                if (isset($indexedNodes[$parentPathKey])) {
                    $indexedNodes[$parentPathKey]['submenu'][] = &$indexedNodes[$currentPathKey];
                }
            }
        }

        return array_values($indexedNodes);
    }

    /**
     * @return array{path:string,parametros:string}|null
     */
    private static function navigationLinkSpec(string $url, ?string $parametros): ?array
    {
        if ($url === '' || str_contains($url, 'fnjs')) {
            return null;
        }

        return [
            'path' => $url,
            'parametros' => $parametros ?? '',
        ];
    }
}
