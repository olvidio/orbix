<?php

namespace Tests\factories\inventario;

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;

/**
 * Factory para crear instancias de UbiInventario para tests
 */
class UbiInventarioFactory
{
    public function createSimple(?int $id = null): UbiInventario
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oUbiInventario = new UbiInventario();
        $oUbiInventario->setId_ubi($id);
        $oUbiInventario->setNom_ubi('test_ubi_' . $id);

        return $oUbiInventario;
    }
}
