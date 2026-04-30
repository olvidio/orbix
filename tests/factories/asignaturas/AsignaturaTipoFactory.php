<?php

namespace Tests\factories\asignaturas;

use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\AsignaturaTipo;
use src\asignaturas\domain\value_objects\AsignaturaTipoId;
use src\asignaturas\domain\value_objects\AsignaturaTipoName;
use src\asignaturas\domain\value_objects\AsignaturaTipoShortName;

/**
 * Factory para crear instancias de AsignaturaTipo para tests.
 *
 * {@see AsignaturaTipoId} solo permite 1..9 (coincide con la tabla real `xa_tipo_asig`).
 */
class AsignaturaTipoFactory
{
    /**
     * Crea una instancia de AsignaturaTipo con id_tipo válido y libre en BD.
     */
    public function createSimple(?int $id = null): AsignaturaTipo
    {
        if ($id === null) {
            $id = $this->nextFreeTipoId();
        } else {
            new AsignaturaTipoId($id);
        }

        $oAsignaturaTipo = new AsignaturaTipo();
        $oAsignaturaTipo->setId_tipo($id);
        $oAsignaturaTipo->setTipoAsignaturaVo(new AsignaturaTipoName('test_tipo_' . $id));
        $oAsignaturaTipo->setTipoBreveVo(new AsignaturaTipoShortName('ts'));

        return $oAsignaturaTipo;
    }

    private function nextFreeTipoId(): int
    {
        $repository = $GLOBALS['container']->get(AsignaturaTipoRepositoryInterface::class);
        for ($candidate = 1; $candidate <= 9; $candidate++) {
            if ($repository->findById($candidate) === null) {
                return $candidate;
            }
        }

        throw new \RuntimeException(
            'AsignaturaTipoFactory: no hay id_tipo libre entre 1 y 9 (AsignaturaTipoId / xa_tipo_asig).'
        );
    }
}
