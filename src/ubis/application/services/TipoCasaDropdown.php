<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\TipoCasaRepositoryInterface;

/**
 * Opciones para select de tipos de casa.
 */
final class TipoCasaDropdown
{
    /**
     * @return array<string, string>
     */
    public static function listaTiposCasa(): array
    {
        $repo = $GLOBALS['container']->get(TipoCasaRepositoryInterface::class);

        return $repo->getArrayTiposCasa();
    }
}
