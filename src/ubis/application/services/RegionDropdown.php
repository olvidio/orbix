<?php

namespace src\ubis\application\services;

use src\ubis\application\repositories\RegionRepository;
use web\Desplegable;

/**
 * Helper para construir desplegables (select) de Regiones
 * replicando el comportamiento legacy de getListaRegiones().
 *
 * - Muestra solo regiones activas (status = true)
 * - Ordenadas por nombre_region
 * - value = region (sigla), label = nombre_region
 */
final class RegionDropdown
{
    /**
     * Devuelve un Desplegable con las regiones activas ordenadas por nombre.
     *
     * @param string $nombreCampo Nombre del campo select (por defecto 'region')
     * @param bool $conBlanco Si debe incluir opción en blanco (por defecto true)
     */
    public static function activasOrdenNombre(string $nombreCampo = 'region', bool $conBlanco = true): Desplegable
    {
        $repo = new RegionRepository();
        $regiones = $repo->getRegiones(['status' => true, '_ordre' => 'nombre_region']);

        $opciones = [];
        foreach ($regiones as $region) {
            // value = sigla (region), label = nombre
            $opciones[$region->getRegionVo()?->value() ?? ''] = $region->getNombre_region();
        }

        $despl = new Desplegable();
        $despl->setNombre($nombreCampo);
        if ($conBlanco) {
            $despl->setBlanco(true);
        }
        $despl->setOpciones($opciones);

        return $despl;
    }
}
