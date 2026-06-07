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
    public function __construct(
        private GrupMenuRoleRepositoryInterface $grupMenuRoleRepository,
        private GrupMenuRepositoryInterface $grupMenuRepository,
        private MenuDbRepositoryInterface $menuDbRepository,
    ) {
    }

    /** @return array<int, \src\menus\domain\entity\GrupMenu> */
    public function __invoke(): array
    {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        $id_role = is_array($sessionAuth) && is_numeric($sessionAuth['id_role'] ?? null)
            ? (int) $sessionAuth['id_role']
            : 0;
        if ($id_role < 1) {
            return [];
        }

        $cGrupMenuRoles = $this->grupMenuRoleRepository->getGrupMenuRoles(['id_role' => $id_role]);

        $out = [];
        foreach ($cGrupMenuRoles as $oGrupMenuRole) {
            $id_gm = $oGrupMenuRole->getId_grupmenu();
            if ($id_gm === null || $id_gm < 1) {
                continue;
            }

            $cMenuDbs = $this->menuDbRepository->getMenuDbs(['id_grupmenu' => $id_gm]);
            if (count($cMenuDbs) < 1) {
                continue;
            }

            $oGrupMenu = $this->grupMenuRepository->findById($id_gm);
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
