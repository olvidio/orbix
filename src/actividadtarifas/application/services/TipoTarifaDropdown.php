<?php

namespace src\actividadtarifas\application\services;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

/**
 * Helper compartido que devuelve las opciones `id_tarifa => letra`
 * filtradas por seccion (`sfsv`) para montar el `<select>` de tarifas
 * en `frontend/actividadtarifas`.
 *
 * Sigue la convencion `refactor.md`: `src/` solo devuelve `array
 * value => label`. El componente `frontend\shared\web\Desplegable` se instancia en la
 * vista / controlador frontend.
 */
final class TipoTarifaDropdown
{
    /**
     * @return array<int,string>
     */
    public static function opciones(int $sfsv = 0): array
    {
        $repo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $opciones = $repo->getArrayTipoTarifas($sfsv === 0 ? '' : $sfsv);

        return is_array($opciones) ? $opciones : [];
    }
}
