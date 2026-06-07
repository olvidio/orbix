<?php

namespace src\profesores\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

/**
 * Opciones del desplegable de asignatura en profesor_asignatura_que.
 */
final class ProfesorAsignaturaQueData
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @return array{aOpciones: array<int|string, string>}
     */
    public function execute(): array
    {
        return [
            'aOpciones' => $this->asignaturaRepository->getArrayAsignaturasConSeparador(),
        ];
    }
}
