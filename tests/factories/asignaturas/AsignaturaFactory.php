<?php

namespace Tests\factories\asignaturas;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaName;
use src\asignaturas\domain\value_objects\NivelId;

/**
 * Factory para crear instancias de Asignatura para tests
 */
class AsignaturaFactory
{
    /**
     * Crea una instancia de Asignatura con un ID obtenido de la secuencia de la BD.
     */
    public function createSimple(?int $id = null): Asignatura
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oAsignatura = new Asignatura();
        $oAsignatura->setId_asignatura($id);
        $oAsignatura->setIdNivelVo(new NivelId(1000));
        $oAsignatura->setNombreAsignaturaVo(new AsignaturaName('test_asignatura_' . $id));
        $oAsignatura->setActive(false);

        return $oAsignatura;
    }
}
