<?php

namespace src\ubis\application\services;

use src\ubis\domain\criteria\RegionField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\RegionRepositoryInterface;

/**
 * Opciones para select de regiones (value = sigla, label = nombre).
 */
final class RegionDropdown
{
    public function __construct(
        private RegionRepositoryInterface $regionRepository,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function activasOrdenNombre(): array
    {
        $regiones = $this->regionRepository->matching(
            Criteria::create()->where(Filter::eq(F::Active, true))->orderBy(F::NombreRegion)
        );

        $opciones = [];
        foreach ($regiones as $region) {
            $regionVo = $region->getRegionVo();
            $nombreVo = $region->getNombreRegionVo();
            if ($regionVo === null || $nombreVo === null) {
                continue;
            }
            $opciones[$regionVo->value()] = $nombreVo->value();
        }

        return $opciones;
    }
}
