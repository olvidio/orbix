<?php

declare(strict_types=1);

namespace src\asignaturas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

final class AsignaturasConSeparadorOpcionesData
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, string>}
     */
    public function execute(bool $op_genericas = true): array
    {
        return [
            'a_opciones' => $this->asignaturaRepository->getArrayAsignaturasConSeparador($op_genericas),
        ];
    }
}
