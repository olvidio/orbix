<?php

namespace Tests\integration\inventario\infrastructure\persistence\postgresql;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\entity\Coleccion;
use Tests\myTest;

class PgColeccionRepositoryTest extends myTest
{
    private ColeccionRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ColeccionRepositoryInterface::class);
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

    public function test_ciclo_guardar_actualizar_y_eliminar(): void
    {
        $o = new Coleccion();
        $o->setId_coleccion($this->repository->getNewId());
        $o->setNom_coleccion('zz coleccion test');
        $o->setAgrupar(true);
        $this->assertTrue($this->repository->Guardar($o));
        try {
            $leido = $this->repository->findById($o->getId_coleccion());
            $this->assertNotNull($leido);
            $this->assertSame(['zz coleccion test', true], [$leido->getNom_coleccion(), $leido->isAgrupar()]);
            $this->assertSame('zz coleccion test', $this->repository->getArrayColecciones()[$o->getId_coleccion()] ?? null);

            $leido->setNom_coleccion('zz coleccion cambiada');
            $leido->setAgrupar(false);
            $this->assertTrue($this->repository->Guardar($leido));
            $releido = $this->repository->findById($o->getId_coleccion());
            $this->assertSame(['zz coleccion cambiada', false], [$releido?->getNom_coleccion(), $releido?->isAgrupar()]);

            $this->assertTrue($this->repository->Eliminar($leido));
            $this->assertNull($this->repository->findById($o->getId_coleccion()));
        } finally {
            $this->repository->Eliminar($o);
        }
    }
}
