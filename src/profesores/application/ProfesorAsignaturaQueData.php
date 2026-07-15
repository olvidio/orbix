<?php

namespace src\profesores\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\PlanEstudiosDePersona;

/**
 * Opciones del desplegable de asignatura en profesor_asignatura_que.
 */
final class ProfesorAsignaturaQueData
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{aOpciones: array<int|string, string>}
     */
    public function execute(array $input = []): array
    {
        $plan = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plan_estudios');
        if ($plan <= 0) {
            $idNom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
            $plan = $idNom > 0
                ? $this->planEstudiosDePersona->resolve($idNom)
                : PlanEstudios::PLAN_2026;
        }

        return [
            'aOpciones' => $this->asignaturaRepository->getArrayAsignaturasConSeparador(true, $plan),
        ];
    }
}
