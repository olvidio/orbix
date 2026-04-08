<?php

namespace Tests\factories\inventario;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;

/**
 * Factory para crear instancias de Equipaje para tests
 */
class EquipajeFactory
{
    public function createSimple(?int $id = null): Equipaje
    {
        if ($id === null) {
            $repository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
            $id = $repository->getNewId();
        }

        $oEquipaje = new Equipaje();
        $oEquipaje->setId_equipaje($id);
        $oEquipaje->setNom_equipaje('test_equipaje_' . $id);

        return $oEquipaje;
    }
}
