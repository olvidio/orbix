<?php

namespace src\planning\application;

use src\personas\domain\criteria\PersonaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Actividades por persona (vista plana) para `planning_persona_ver`.
 */
final class PlanningPersonaVerData
{
    public function __construct(
        private PlanningPersonaRepositoryPicker $personaRepositoryPicker,
        private ActividadesDePersonaService $actividadesDePersonaService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @param list<string> $aid_nom
     * @return array{a_actividades: array<int|string, mixed>}
     */
    public function execute(array $input, array $aid_nom, DateTimeLocal $oIniPlanning, string $inicio_local, string $fin_iso, string $inicio_iso): array
    {
        $Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'obj_pau');
        $PersonaRepository = $this->personaRepositoryPicker->getSafe($Qobj_pau);
        $cPersonas = $PersonaRepository->matching(
            Criteria::create()->where(Filter::in(PersonaField::IdNom, array_map('intval', $aid_nom)))
        );

        $a_actividades = $this->actividadesDePersonaService->actividadesPorPersona(
            $cPersonas,
            $fin_iso,
            $inicio_iso,
            $oIniPlanning,
            $inicio_local,
            agruparPorCentro: false
        );

        return ['a_actividades' => $a_actividades];
    }
}
