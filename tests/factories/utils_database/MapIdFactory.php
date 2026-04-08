<?php

namespace Tests\factories\utils_database;

use src\utils_database\domain\entity\MapId;
use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;
use src\utils_database\domain\value_objects\MapObjectCode;

/**
 * Factory para crear instancias de MapId para tests.
 * MapId tiene clave compuesta (objeto + id_resto).
 */
class MapIdFactory
{
    public function createSimple(?string $objeto = null, ?int $idResto = null): MapId
    {
        $objeto = $objeto ?? 'tst';
        $idResto = $idResto ?? rand(90000, 99999);

        $oMapId = new MapId();
        $oMapId->setObjeto($objeto);
        $oMapId->setId_resto($idResto);
        $oMapId->setId_dl(1);

        return $oMapId;
    }
}
