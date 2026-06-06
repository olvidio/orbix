<?php

namespace src\actividadtarifas\application\services;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

/**
 * Helper compartido que devuelve las opciones `id_tarifa => letra`
 * filtradas por seccion (`sfsv`) para montar el `<select>` de tarifas
 * en `frontend/actividadtarifas`.
 */
final class TipoTarifaDropdown
{
    public function __construct(
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function opciones(int $sfsv = 0): array
    {
        $opciones = $this->tipoTarifaRepository->getArrayTipoTarifas($sfsv === 0 ? '' : $sfsv);

        return $opciones;
    }
}
