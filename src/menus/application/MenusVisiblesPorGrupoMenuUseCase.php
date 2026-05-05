<?php

namespace src\menus\application;

use frontend\shared\security\HashFront;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\PermisoMenu;
use src\shared\config\ConfigGlobal;

/**
 * Entradas de menú lateral visibles para un id_grupmenu, equivalente al filtro
 * aplicado en LegacyLayout::generateMenuHtml (permisos Bit, módulos y apps instalados).
 */
final class MenusVisiblesPorGrupoMenuUseCase
{
    /**
     * @return list<array{id_menu:int,indice:int,menu:?string,url:string,full_url:string,parametros:?string,orden:list<int>}>
     */
    public function __invoke(int $id_grupmenu): array
    {
        $MenusDbRepository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
        $MetaMenuRepository = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);

        $oPermisoMenu = new PermisoMenu();

        $aWhere = [
            'id_grupmenu' => $id_grupmenu,
            '_ordre' => 'orden',
        ];
        $cMenuDbs = $MenusDbRepository->getMenuDbs($aWhere);
        if ($cMenuDbs === false || !is_iterable($cMenuDbs)) {
            return [];
        }

        $menuData = [];
        $num_menu_1 = 0;
        $raiz_pral = '';

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
                $oMetamenu = $MetaMenuRepository->findById($id_metamenu);
                if ($oMetamenu === null) {
                    continue;
                }
                $url = $oMetamenu->getUrl();
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
            if ($raiz !== $raiz_pral) {
                continue;
            }

            $full_url = '';
            if (!empty($url)) {
                $full_url = ConfigGlobal::getWeb() . '/' . $url;
            }
            $parametros = HashFront::add_hash($parametros, $full_url);
            $indice = count($orden);
            if ($indice == 1 && !$oPermisoMenu->visible($menu_perm)) {
                $num_menu_1 = $orden[0];

                continue;
            }

            $num_menu_1 = 0;
            if (!$oPermisoMenu->visible($menu_perm)) {
                continue;
            }

            $menuData[] = [
                'id_menu' => (int)$oMenuDb->getId_menu(),
                'indice' => $indice,
                'menu' => $menu,
                'url' => (string)($url ?? ''),
                'full_url' => $full_url,
                'parametros' => $parametros,
                'orden' => array_map(static fn ($v): int => (int)$v, $orden),
            ];
        }

        return $menuData;
    }
}
