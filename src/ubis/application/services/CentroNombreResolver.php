<?php

namespace src\ubis\application\services;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;

/**
 * Nombre de un centro a partir de su id_ubi.
 *
 * En DMZ no existe u_centros_dl: los centros se leen de comun (ellos para sv, ellas para sf).
 */
final class CentroNombreResolver
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllosRepositoryInterface $centroEllosRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    public function nombre(int $idUbi): string
    {
        if (ConfigGlobal::is_dmz()) {
            $oCentro = (int) substr((string) $idUbi, 0, 1) === 2
                ? $this->centroEllasRepository->findById($idUbi)
                : $this->centroEllosRepository->findById($idUbi);
        } else {
            $oCentro = $this->centroDlRepository->findById($idUbi);
        }

        return $oCentro?->getNombre_ubi() ?? '';
    }
}
