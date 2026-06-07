<?php

namespace src\ubis\application;

use src\ubis\application\services\CentrosDropdown;

/**
 * Devuelve el payload (solo datos) para poblar el <select> de centros en
 * `frontend\shared\web\CentrosQue`.
 */
final class CentrosOpcionesData
{
    public function __construct(
        private CentrosDropdown $centrosDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $filtro
     * @return array{opciones: array<int|string, string>}
     */
    public function execute(array $filtro = []): array
    {
        return [
            'opciones' => $this->centrosDropdown->opciones($filtro),
        ];
    }
}
