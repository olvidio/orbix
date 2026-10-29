<?php

namespace src\notas\application;

use src\asignaturas\domain\criteria\AsignaturaField as AF;
use src\notas\domain\criteria\PersonaNotaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
        $cOpcionales = $AsignaturaRepository->matching(
            Criteria::create()
                ->where(PlanEstudiosFilter::filter($plan))
                ->andWhere(Filter::eq(AF::Active, true))
                ->andWhere(Filter::between(AF::IdNivel, 3000, 5000))
                ->orderBy(AF::NombreCorto)
        );

        $aSuperadas = NotaSituacion::getArraySuperadas();
        $PersonaNotaRepository = $this->personaNotaRepository;
        $cOpSuperadas = $PersonaNotaRepository->matching(
            Criteria::create()
                ->where(Filter::in(F::IdSituacion, $aSuperadas))
                ->andWhere(Filter::eq(F::IdNom, $id_nom))
                ->andWhere(Filter::gt(F::IdAsignatura, 3000))
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
