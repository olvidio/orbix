<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Egm;
use Tests\factories\inventario\EquipajeFactory;
use Tests\myTest;

class PgEgmRepositoryTest extends myTest
{
    private EgmRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }

    public function test_get_new_id()
    {
        $nid = $this->repository->getNewId();
        $this->assertTrue(is_numeric($nid));
    }

    private function egm(int $idEquipaje, int $grupo, int $lugar): Egm
    {
        $o = new Egm();
        $o->setId_item($this->repository->getNewId());
        $o->setId_equipaje($idEquipaje);
        $o->setId_grupo($grupo);
        $o->setId_lugar($lugar);
        $o->setTexto('texto ' . $grupo);

        return $o;
    }

    public function test_ciclo_guardar_actualizar_y_eliminar_sin_tocar_la_otra_maleta(): void
    {
        $equipajeRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
        $oEquipaje = (new EquipajeFactory())->createSimple();
        $idEquipaje = $oEquipaje->getId_equipaje();
        $this->assertTrue($equipajeRepository->Guardar($oEquipaje));
        try {
            $this->assertSame(0, $this->repository->getUltimoGrupo($idEquipaje));

            $a = $this->egm($idEquipaje, 1, 999999901);
            $b = $this->egm($idEquipaje, 2, 999999902);
            $this->assertNull($this->repository->findById($a->getId_item()));
            $this->assertTrue($this->repository->Guardar($a));
            $this->assertTrue($this->repository->Guardar($b));

            $leido = $this->repository->findById($a->getId_item());
            $this->assertNotNull($leido);
            $this->assertSame([$idEquipaje, 1, 999999901, 'texto 1'], [$leido->getId_equipaje(), $leido->getId_grupo(), $leido->getId_lugar(), $leido->getTexto()]);
            $this->assertSame(2, $this->repository->getUltimoGrupo($idEquipaje));
            // Ordena solo por equipaje: dentro de uno, el orden no está fijado (se usa para saber si un id está).
            $this->assertEqualsCanonicalizing([(string) $a->getId_item(), (string) $b->getId_item()], $this->repository->getArrayIdFromIdEquipajes([$idEquipaje]));
            $this->assertEqualsCanonicalizing(['999999901', '999999902'], $this->repository->getArrayIdFromIdEquipajes([$idEquipaje], 'lugar'));
            $this->assertSame([], $this->repository->getArrayIdFromIdEquipajes([-1]));

            $leido->setTexto('cambiado');
            $leido->setId_lugar(999999903);
            $this->assertTrue($this->repository->Guardar($leido));
            $this->assertSame('cambiado', $this->repository->findById($a->getId_item())?->getTexto());
            $this->assertSame(999999903, $this->repository->findById($a->getId_item())?->getId_lugar());
            $this->assertSame('texto 2', $this->repository->findById($b->getId_item())?->getTexto(), 'la otra maleta no cambia');

            $this->assertTrue($this->repository->Eliminar($leido));
            $this->assertNull($this->repository->findById($a->getId_item()));
            $this->assertNotNull($this->repository->findById($b->getId_item()), 'la otra maleta sigue');
        } finally {
            $equipajeRepository->Eliminar($oEquipaje);
        }
        $this->assertSame([], $this->repository->getArrayIdFromIdEquipajes([$idEquipaje]), 'borrar el equipaje borra sus maletas');
    }
}
