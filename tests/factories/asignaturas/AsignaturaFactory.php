<?php

namespace Tests\factories\asignaturas;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\asignaturas\domain\value_objects\AsignaturaName;
use src\asignaturas\domain\value_objects\NivelId;
use src\asignaturas\domain\value_objects\PlanEstudios;

/**
 * Factory para crear instancias de Asignatura para tests
 */
class AsignaturaFactory
{
    /** Intentos gastando secuencia antes de barrer 1000..3999. */
    private const NEW_ID_SAMPLES = 64;

    /**
     * Crea una instancia de Asignatura con un ID obtenido de la secuencia de la BD.
     */
    public function createSimple(?int $id = null): Asignatura
    {
        if ($id === null) {
            $id = $this->nextFreeDomainConstrainedId();
        } else {
            new AsignaturaId($id);
        }

        $oAsignatura = new Asignatura();
        $oAsignatura->setId_asignatura($id);
        $oAsignatura->setIdNivelVo(new NivelId(1000));
        $oAsignatura->setNombreAsignaturaVo(new AsignaturaName('test_asignatura_' . $id));
        $oAsignatura->setPlan_estudios([PlanEstudios::PLAN_1997]);
        $oAsignatura->setActive(false);

        return $oAsignatura;
    }

    /**
     * La secuencia de BD puede devolver valores fuera del contrato {@see AsignaturaId}.
     */
    private function nextFreeDomainConstrainedId(): int
    {
        $repository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

        for ($n = 0; $n < self::NEW_ID_SAMPLES; $n++) {
            $candidate = (int) $repository->getNewId();
            if ($this->isDomainConstrainedAsignaturaId($candidate)
                && $repository->findById($candidate) === null
            ) {
                return $candidate;
            }
        }

        for ($candidate = 1000; $candidate <= 3999; $candidate++) {
            if ($repository->findById($candidate) === null) {
                return $candidate;
            }
        }

        throw new \RuntimeException(
            'AsignaturaFactory: no hay id_asignatura libre en el rango permitido por AsignaturaId (1000–3999, 9998/9999).'
        );
    }

    private function isDomainConstrainedAsignaturaId(int $value): bool
    {
        return ($value >= 1000 && $value <= 3999)
            || $value === 9998
            || $value === 9999;
    }
}
