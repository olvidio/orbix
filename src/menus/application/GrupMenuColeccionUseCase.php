<?php

namespace src\menus\application;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;

/**
 * Grupmenus visibles para el usuario actual, mismo criterio que el menú lateral en {@see \index.php}:
 * `aux_grupmenu_rol` para `session_auth.id_role`, al menos un ítem en menú para ese grupmenu, y `orden` &gt;= 1.
 */
class GrupMenuColeccionUseCase
{
    /** @return array<int, \src\menus\domain\entity\GrupMenu> */
    public function __invoke(): array
    {
        $id_role = (int)($_SESSION['session_auth']['id_role'] ?? 0);
        if ($id_role < 1) {
            return [];
        }

        $container = $GLOBALS['container'];

        /** @var GrupMenuRoleRepositoryInterface $GrupMenuRoleRepository */
        $GrupMenuRoleRepository = $container->get(GrupMenuRoleRepositoryInterface::class);
        /** @var GrupMenuRepositoryInterface $GrupMenuRepository */
        $GrupMenuRepository = $container->get(GrupMenuRepositoryInterface::class);
        /** @var MenuDbRepositoryInterface $MenusDbRepository */
        $MenusDbRepository = $container->get(MenuDbRepositoryInterface::class);

        $cGrupMenuRoles = $GrupMenuRoleRepository->getGrupMenuRoles(['id_role' => $id_role]);
        if ($cGrupMenuRoles === false || !is_iterable($cGrupMenuRoles)) {
            return [];
        }

        $out = [];
        foreach ($cGrupMenuRoles as $oGrupMenuRole) {
            $id_gm = $oGrupMenuRole->getId_grupmenu();
            if ($id_gm === null || $id_gm < 1) {
                continue;
            }

            $cMenuDbs = $MenusDbRepository->getMenuDbs(['id_grupmenu' => $id_gm]);
            if (!is_array($cMenuDbs) || count($cMenuDbs) < 1) {
                continue;
            }

            $oGrupMenu = $GrupMenuRepository->findById($id_gm);
            if ($oGrupMenu === null) {
                continue;
            }

            $iorden = $oGrupMenu->getOrden() ?? 0;
            if ($iorden < 1) {
                continue;
            }

            $out[] = $oGrupMenu;
        }

        usort(
            $out,
            static fn ($a, $b): int => ($a->getOrden() ?? 999) <=> ($b->getOrden() ?? 999)
        );

        return $out;
    }
}
