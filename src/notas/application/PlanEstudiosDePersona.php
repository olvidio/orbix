<?php

namespace src\notas\application;

use src\notas\domain\criteria\PersonaNotaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Determina el plan de estudios (1997 o 2026) aplicable a una persona.
 *
 * Misma regla que {@see Tesera::getPlan()}: si tiene marca de cuadrienio
 * completado (id_asignatura 9998) con f_acta anterior a 2026-03-30 → 1997;
 * en caso contrario → 2026.
 */
final class PlanEstudiosDePersona
{
    private const ID_ASIG_FIN_CUADRIENIO = 9998;
    private const FECHA_LIMITE_PLAN_2026 = '2026-03-30';

    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }

    public function resolve(int $idNom): int
    {
        $cNotas = $this->personaNotaRepository->matching(
            Criteria::create()
                ->where(Filter::eq(F::IdNom, $idNom))
                ->andWhere(Filter::eq(F::IdAsignatura, self::ID_ASIG_FIN_CUADRIENIO))
        );
        if ($cNotas === []) {
            return PlanEstudios::PLAN_2026;
        }
        $oFActa = $cNotas[0]->getF_acta();
        $oLimite = new DateTimeLocal(self::FECHA_LIMITE_PLAN_2026);
        return ($oFActa < $oLimite) ? PlanEstudios::PLAN_1997 : PlanEstudios::PLAN_2026;
    }
}
