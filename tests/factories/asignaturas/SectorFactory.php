<?php

namespace Tests\factories\asignaturas;

use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\entity\Sector;
use src\asignaturas\domain\value_objects\SectorId;
use src\asignaturas\domain\value_objects\SectorName;

/**
 * Factory para crear instancias de Sector para tests
 */
class SectorFactory
{
    /**
     * Crea una instancia de Sector con un ID obtenido de la secuencia de la BD.
     */
    public function createSimple(?int $id = null): Sector
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(SectorRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oSector = new Sector();
        $oSector->setId_sector($id);
        $oSector->setSector('test_sector_' . $id);

        return $oSector;
    }
}
