<?php

namespace Tests\factories\asignaturas;

use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\asignaturas\domain\value_objects\DepartamentoName;

/**
 * Factory para crear instancias de Departamento para tests
 */
class DepartamentoFactory
{
    /**
     * Crea una instancia de Departamento con un ID obtenido de la secuencia de la BD.
     */
    public function createSimple(?int $id = null): Departamento
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oDepartamento = new Departamento();
        $oDepartamento->setId_departamento($id);
        $oDepartamento->setNombreDepartamentoVo(new DepartamentoName('test_departamento_' . $id));

        return $oDepartamento;
    }
}
