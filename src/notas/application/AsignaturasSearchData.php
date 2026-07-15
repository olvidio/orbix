<?php

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\PlanEstudiosDePersona;

/**
 * Autocomplete de asignaturas por nombre. Devuelve el JSON (como
 * cadena) que el repositorio prepara para jQuery-UI autocomplete.
 */
final class AsignaturasSearchData
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $search = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'search');
        $plan = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plan_estudios');
        if ($plan <= 0) {
            $idNom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
            if ($idNom <= 0) {
                $idNom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
            }
            $plan = $idNom > 0
                ? $this->planEstudiosDePersona->resolve($idNom)
                : PlanEstudios::PLAN_2026;
        }
        $repo = $this->asignaturaRepository;
        return (string) $repo->getJsonAsignaturas([
            'nombre_asignatura' => $search,
            'plan_estudios' => $plan,
        ]);
    }
}
