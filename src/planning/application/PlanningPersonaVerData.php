<?php

namespace src\planning\application;

use src\planning\application\ActividadesDePersonaService;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Actividades por persona (vista plana) para `planning_persona_ver`.
 */
final class PlanningPersonaVerData
{
    /**
     * @param array<string, mixed> $post
     * @param list<string> $aid_nom
     * @return array{a_actividades: array}
     */
    public static function execute(array $post, array $aid_nom, DateTimeLocal $oIniPlanning, string $inicio_local, string $fin_iso, string $inicio_iso): array
    {
        $Qobj_pau = (string)($post['obj_pau'] ?? '');
        $aWhere = [
            'id_nom' => implode(',', $aid_nom),
        ];
        $aOperador = [
            'id_nom' => 'OR',
        ];

        $picker = new PlanningPersonaRepositoryPicker();
        $PersonaRepository = $picker->getSafe($Qobj_pau);
        $cPersonas = $PersonaRepository->getPersonas($aWhere, $aOperador);

        $a_actividades = ActividadesDePersonaService::actividadesPorPersona(
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
