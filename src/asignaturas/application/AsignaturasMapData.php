<?php

declare(strict_types=1);

namespace src\asignaturas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

/**
 * Mapa id_asignatura => nombre_corto para pantallas que no deben usar el contenedor en frontend.
 *
 * @return array<int|string, string|null>
 */
final class AsignaturasMapData
{
    /**
     * @return array{a_asignaturas: array<int|string, string|null>}
     */
    public static function execute(): array
    {
        $repo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

        return ['a_asignaturas' => $repo->getArrayAsignaturas()];
    }
}
