<?php

namespace Tests\integration\actividades\infrastructure\persistence\postgresql;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use Tests\factories\actividades\ActividadAllFactory;
use Tests\myTest;

class PgActividadAllRepositoryTest extends myTest
{
    private ActividadAllRepositoryInterface $repository;
    private ActividadAllFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $this->factory = new ActividadAllFactory();
    }

    public function test_guardar_nuevo_actividad()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_activ();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertSame($id, $oGuardado->getId_activ());

        $this->repository->Eliminar($oGuardado);
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }

    public function test_datos_by_id_no_existente()
    {
        $this->assertFalse($this->repository->datosById(999999981));
    }
}
