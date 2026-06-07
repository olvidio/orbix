<?php

namespace src\ubis\application\services;

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
        $regiones = $this->regionRepository->getRegiones(['active' => true, '_ordre' => 'nombre_region']);

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
