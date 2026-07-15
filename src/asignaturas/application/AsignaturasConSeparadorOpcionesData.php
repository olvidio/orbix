<?php

declare(strict_types=1);

namespace src\asignaturas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\PlanEstudios;

final class AsignaturasConSeparadorOpcionesData
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, string>}
     */
    public function execute(bool $op_genericas = true, ?int $planEstudios = null): array
    {
        return [
            'a_opciones' => $this->asignaturaRepository->getArrayAsignaturasConSeparador(
                $op_genericas,
                $planEstudios ?? PlanEstudios::PLAN_2026,
            ),
        ];
    }
}
