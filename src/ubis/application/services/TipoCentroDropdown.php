<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\TipoCentroRepositoryInterface;

/**
 * Opciones para select de tipos de centro.
 */
final class TipoCentroDropdown
{
    /**
     * @return array<string, string>
     */
    public static function listaTiposCentro(): array
    {
        $repo = $GLOBALS['container']->get(TipoCentroRepositoryInterface::class);

        return $repo->getArrayTiposCentro();
    }
}
