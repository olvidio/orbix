<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\TipoCasaRepositoryInterface;

/**
 * Opciones para select de tipos de casa.
 */
final class TipoCasaDropdown
{
    public function __construct(
        private TipoCasaRepositoryInterface $tipoCasaRepository,
    ) {
    }

    /**
     * @return array<int|string, string>
     */
    public function listaTiposCasa(): array
    {
        return $this->tipoCasaRepository->getArrayTiposCasa();
    }
}
