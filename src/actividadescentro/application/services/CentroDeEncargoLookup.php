<?php

declare(strict_types=1);

namespace src\actividadescentro\application\services;

use src\actividadescentro\domain\contracts\CentroDeEncargoLookupInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;

/**
 * Centro de un encargo por su id. El primer dígito del id dice si es de sv o de sf: los de la
 * sección de la sesión están en los centros de la dl (en la DMZ, en la copia de comun) y los de la
 * otra, en su copia de comun. Mismo criterio que `PgCentroEncargadoRepository::getCentrosEncargadosActividad()`.
 */
final class CentroDeEncargoLookup implements CentroDeEncargoLookupInterface
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private CentroEllosRepositoryInterface $centroEllosRepository,
    ) {
    }

    public function findById(mixed $id): ?object
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            return null;
        }
        $id_ubi = (int) $id;
        $sfsv = (int) substr((string) $id_ubi, 0, 1);
        if (ConfigGlobal::mi_sfsv() !== $sfsv) {
            return $this->centroEllasRepository->findById($id_ubi);
        }

        return ConfigGlobal::is_dmz()
            ? $this->centroEllosRepository->findById($id_ubi)
            : $this->centroDlRepository->findById($id_ubi);
    }
}
