<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\TipoCentroRepositoryInterface;

/**
 * Opciones para select de tipos de centro.
 */
final class TipoCentroDropdown
{
    public function __construct(
        private TipoCentroRepositoryInterface $tipoCentroRepository,
    ) {
    }

    /**
     * @return array<int|string, string>
     */
    public function listaTiposCentro(): array
    {
        return $this->tipoCentroRepository->getArrayTiposCentro();
    }
}
