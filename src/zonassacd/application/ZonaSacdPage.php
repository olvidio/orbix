<?php

namespace src\zonassacd\application;

use src\permisos\domain\XPermisos;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\permisos\domain\MenuDlPermissionBits;

final class ZonaSacdPage
{
    public function __construct(
        private ZonaRepositoryInterface $zonaRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, string>, perm_pffe: bool}
     */
    public function getData(): array
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return [
            'a_opciones' => $this->zonaRepository->getArrayZonas(),
            'perm_pffe' => $oPerm instanceof XPermisos
                && ($oPerm->have_perm_oficina(MenuDlPermissionBits::PFFE) || $oPerm->have_perm_oficina(MenuDlPermissionBits::VCSD)),
        ];
    }
}
