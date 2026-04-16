<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\RegionRepositoryInterface;

/**
 * Opciones para select de regiones (value = sigla, label = nombre).
 */
final class RegionDropdown
{
    /**
     * @return array<string, string>
     */
    public static function activasOrdenNombre(): array
    {
        $repo = $GLOBALS['container']->get(RegionRepositoryInterface::class);
        $regiones = $repo->getRegiones(['active' => true, '_ordre' => 'nombre_region']);

        $opciones = [];
        foreach ($regiones as $region) {
            $opciones[$region->getRegionVo()?->value() ?? ''] = $region->getNombreRegionVo()->value();
        }

        return $opciones;
    }
}
