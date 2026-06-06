<?php

namespace src\zonassacd\application;

use src\permisos\domain\XPermisos;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class ZonaCtrPage
{
    public function __construct(
        private ZonaRepositoryInterface $zonaRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, string>, perm_des: bool}
     */
    public function getData(): array
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return [
            'a_opciones' => $this->zonaRepository->getArrayZonas(),
            'perm_des' => $oPerm instanceof XPermisos
                && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd')),
        ];
    }
}
