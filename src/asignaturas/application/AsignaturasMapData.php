<?php

declare(strict_types=1);

namespace src\asignaturas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

/**
 * Mapa id_asignatura => nombre_corto para pantallas que no deben usar el contenedor en frontend.
 */
final class AsignaturasMapData
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @return array{a_asignaturas: array<int|string, string|null>}
     */
    public function execute(): array
    {
        return ['a_asignaturas' => $this->asignaturaRepository->getArrayAsignaturas()];
    }
}
