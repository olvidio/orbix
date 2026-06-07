<?php

namespace src\menus\application;

use frontend\shared\security\HashFront;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\PermisoMenu;
use src\shared\config\ConfigGlobal;

/**
 * Entradas de menú para el layout legacy (grupos 1 y el seleccionado, mismo filtro que el antiguo
 * {@see \frontend\shared\layouts\LegacyLayout} antes de mover la lectura a HTTP).
 *
 * @return list<array{indice:int,menu:?string,url:string,full_url:string,parametros:?string,menu_perm:mixed}>
 */
final class MenusLegacyLayoutItemsUseCase
{
    public function __construct(
        private MenuDbRepositoryInterface $menuDbRepository,
        private MetaMenuRepositoryInterface $metaMenuRepository,
    ) {
    }

    /** @return list<array{indice:int,menu:?string,url:string,full_url:string,parametros:?string,menu_perm:int|null}> */
    public function __invoke(string $id_grupmenu): array
    {
        $oPermisoMenu = new PermisoMenu();

        $aWhere = [
            'id_grupmenu' => '^1$|^' . $id_grupmenu . '$',
            '_ordre' => 'orden',
        ];
        $aOperador = ['id_grupmenu' => '~'];
        $cMenuDbs = $this->menuDbRepository->getMenuDbs($aWhere, $aOperador);

        $num_menu_1 = 0;
        $raiz_pral = '';
        $menuData = [];

        foreach ($cMenuDbs as $oMenuDb) {
            $orden = $oMenuDb->getOrden();
            if (empty($orden)) {
                continue;
            }

            $menu = $oMenuDb->getMenu();
            $parametros = $oMenuDb->getParametros();
            $id_metamenu = $oMenuDb->getId_metamenu();
            $menu_perm = $oMenuDb->getMenu_perm();

            if (!empty($id_metamenu)) {
                $oMetamenu = $this->metaMenuRepository->findById($id_metamenu);
                if ($oMetamenu === null) {
                    continue;
                }
                $url = $oMetamenu->getUrl() ?? '';
                $id_mod = $oMetamenu->getId_mod();
            } else {
                $url = '';
                $id_mod = null;
            }

            if (!empty($id_mod) && !ConfigGlobal::is_mod_installed((int)$id_mod)) {
                continue;
            }
            if (!empty($url)) {
                $matches = [];
                $rta = preg_match('@apps/(.+?)/@', $url, $matches);
                if ($rta === false) {
                    continue;
                }
                if ($rta === 1) {
                    $url_app = $matches[1];
                    if (!ConfigGlobal::is_app_installed($url_app)) {
                        continue;
                    }
                }
            }

            $raiz = $orden[0];
            if (count($orden) === 1) {
                $raiz_pral = $raiz;
            }
            if ($raiz != $raiz_pral) {
                continue;
            }

            $full_url = '';
            if (!empty($url)) {
                $full_url = ConfigGlobal::getWeb() . '/' . $url;
            }
            $parametros = HashFront::add_hash($parametros, $full_url);
            $indice = count($orden);

            if ($indice == 1 && !$oPermisoMenu->visible($menu_perm ?? 0)) {
                $num_menu_1 = $orden[0];
                continue;
            }

            $num_menu_1 = 0;
            if (!$oPermisoMenu->visible($menu_perm ?? 0)) {
                continue;
            }

            $menuData[] = [
                'indice' => $indice,
                'menu' => $menu,
                'url' => $url,
                'full_url' => $full_url,
                'parametros' => $parametros,
                'menu_perm' => $menu_perm,
            ];
        }

        return $menuData;
    }
}
