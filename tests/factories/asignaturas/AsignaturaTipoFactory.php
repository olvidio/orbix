<?php

namespace Tests\factories\asignaturas;

use src\asignaturas\domain\entity\AsignaturaTipo;
use src\asignaturas\domain\value_objects\AsignaturaTipoName;
use src\asignaturas\domain\value_objects\AsignaturaTipoShortName;

/**
 * Factory para crear instancias de AsignaturaTipo para tests.
 * AsignaturaTipo usa IDs manuales (no tiene secuencia en BD).
 */
class AsignaturaTipoFactory
{
    /**
     * Crea una instancia de AsignaturaTipo con un ID alto (rango de test).
     */
    public function createSimple(?int $id = null): AsignaturaTipo
    {
        $id = $id ?? (9000 + rand(0, 999));

        $oAsignaturaTipo = new AsignaturaTipo();
        $oAsignaturaTipo->setId_tipo($id);
        $oAsignaturaTipo->setTipoAsignaturaVo(new AsignaturaTipoName('test_tipo_' . $id));
        $oAsignaturaTipo->setTipoBreveVo(new AsignaturaTipoShortName('tst'));

        return $oAsignaturaTipo;
    }
}
