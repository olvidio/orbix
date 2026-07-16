<?php

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Calcula las asignaturas opcionales (3000-5000) que todavia puede
 * cursar una persona (no las tiene superadas).
 *
 * Devuelve un diccionario `[id_asignatura => nombre_corto]`.
 */
final class PosiblesOpcionalesData
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<int, string>
     */
    public function execute(array $input): array
    {
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        $plan = $this->planEstudiosDePersona->resolve($id_nom);

        $AsignaturaRepository = $this->asignaturaRepository;
        [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, [
            'active' => 't',
            'id_nivel' => '3000,5000',
            '_ordre' => 'nombre_corto',
        ], ['id_nivel' => 'BETWEEN']);

        $cOpcionales = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);

        $aSuperadas = NotaSituacion::getArraySuperadas();
        $PersonaNotaRepository = $this->personaNotaRepository;
        $cOpSuperadas = $PersonaNotaRepository->getPersonaNotas(
            [
                'id_situacion' => implode(',', $aSuperadas),
                'id_nom' => $id_nom,
                'id_asignatura' => 3000,
            ],
            [
                'id_situacion' => 'IN',
                'id_asignatura' => '>',
            ]
        );

        $aOpSuperadas = [];
        foreach ($cOpSuperadas as $oPN) {
            $aOpSuperadas[$oPN->getId_asignatura()] = $oPN->getId_asignatura();
        }

        $aFaltan = [];
        foreach ($cOpcionales as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            if (array_key_exists($id_asignatura, $aOpSuperadas)) {
                continue;
            }
            $nombreCorto = $oAsignatura->getNombre_corto();
            if ($nombreCorto === null || $nombreCorto === '') {
                continue;
            }
            $aFaltan[$id_asignatura] = $nombreCorto;
        }

        return $aFaltan;
    }
}
