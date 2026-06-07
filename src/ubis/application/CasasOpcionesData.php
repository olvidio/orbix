<?php

namespace src\ubis\application;

use src\ubis\application\services\CasasDropdown;

/**
 * Devuelve el payload (solo datos) para poblar el <select> de casas en
 * `frontend\shared\web\CasasQue`.
 */
final class CasasOpcionesData
{
    public function __construct(
        private CasasDropdown $casasDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $filtro
     * @return array{opciones: array<int|string, string>}
     */
    public function execute(array $filtro = []): array
    {
        return [
            'opciones' => $this->casasDropdown->opciones($filtro),
        ];
    }
}
