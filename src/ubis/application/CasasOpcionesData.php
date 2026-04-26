<?php

namespace src\ubis\application;

use src\ubis\application\services\CasasDropdown;

/**
 * Devuelve el payload (solo datos) para poblar el <select> de casas en
 * `frontend\shared\web\CasasQue`. La vista/componente frontend es quien
 * construye el HTML del desplegable; aquí solo se exponen las opciones.
 *
 * Sustituye el acceso directo desde `CasasQue` al repositorio
 * `CasaDlRepositoryInterface` (separación frontend ↔ backend, ver `refactor.md`).
 */
final class CasasOpcionesData
{
    /**
     * @param array<string, mixed> $filtro Filtro whitelisted; ver {@see CasasDropdown::opciones()}
     * @return array{opciones: array<int, string>}
     */
    public static function execute(array $filtro = []): array
    {
        return [
            'opciones' => CasasDropdown::opciones($filtro),
        ];
    }
}
