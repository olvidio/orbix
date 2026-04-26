<?php

namespace src\ubis\application;

use src\ubis\application\services\CentrosDropdown;

/**
 * Devuelve el payload (solo datos) para poblar el <select> de centros en
 * `frontend\shared\web\CentrosQue`.
 *
 * Sustituye el acceso directo desde `CentrosQue` al repositorio
 * `CentroDlRepositoryInterface` (separación frontend ↔ backend, ver `refactor.md`).
 */
final class CentrosOpcionesData
{
    /**
     * @param array<string, mixed> $filtro Filtro whitelisted; ver {@see CentrosDropdown::opciones()}
     * @return array{opciones: array<int, string>}
     */
    public static function execute(array $filtro = []): array
    {
        return [
            'opciones' => CentrosDropdown::opciones($filtro),
        ];
    }
}
