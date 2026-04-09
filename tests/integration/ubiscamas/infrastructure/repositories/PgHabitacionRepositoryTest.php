<?php

namespace Tests\integration\ubiscamas\infrastructure\persistence\postgresql;

use src\ubiscamas\domain\contracts\HabitacionRepositoryInterface;
use src\ubiscamas\domain\entity\Habitacion;
use Tests\myTest;
use Tests\factories\ubiscamas\HabitacionFactory;

class PgHabitacionRepositoryTest extends myTest
{
    private HabitacionRepositoryInterface $repository;
    private HabitacionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(HabitacionRepositoryInterface::class);
        $this->factory = new HabitacionFactory();
    }

    public function test_guardar_nueva_habitacion()
    {
        $oHabitacion = $this->factory->createSimple();
        $id = $oHabitacion->getId_habitacion();

        $result = $this->repository->Guardar($oHabitacion);
        $this->assertTrue($result);

        $oGuardada = $this->repository->findById($id);
        $this->assertNotNull($oGuardada);
        $this->assertEquals($id, $oGuardada->getId_habitacion());
        $this->assertEquals($oHabitacion->getObservacionesVo()?->value(), $oGuardada->getObservacionesVo()?->value());

        $this->repository->Eliminar($oGuardada);
    }

    public function test_actualizar_habitacion_existente()
    {
        $oHabitacion = $this->factory->createSimple();
        $id = $oHabitacion->getId_habitacion();
        $this->repository->Guardar($oHabitacion);

        $oActualizada = $this->factory->createSimple($id);
        $result = $this->repository->Guardar($oActualizada);
        $this->assertTrue($result);

        $oObtenida = $this->repository->findById($id);
        $this->assertNotNull($oObtenida);

        $this->repository->Eliminar($oObtenida);
    }

    public function test_find_by_id_existente()
    {
        $oHabitacion = $this->factory->createSimple();
        $id = $oHabitacion->getId_habitacion();
        $this->repository->Guardar($oHabitacion);

        $oEncontrada = $this->repository->findById($id);
        $this->assertNotNull($oEncontrada);
        $this->assertInstanceOf(Habitacion::class, $oEncontrada);
        $this->assertEquals($id, $oEncontrada->getId_habitacion());

        $this->repository->Eliminar($oEncontrada);
    }

    public function test_find_by_id_no_existente()
    {
        $oHabitacion = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($oHabitacion);
    }

    public function test_eliminar_habitacion()
    {
        $oHabitacion = $this->factory->createSimple();
        $id = $oHabitacion->getId_habitacion();
        $this->repository->Guardar($oHabitacion);

        $oGuardada = $this->repository->findById($id);
        $this->assertNotNull($oGuardada);

        $result = $this->repository->Eliminar($oGuardada);
        $this->assertTrue($result);

        $oEliminada = $this->repository->findById($id);
        $this->assertNull($oEliminada);
    }

    public function test_get_habitaciones_by_ubi()
    {
        $oHabitacion = $this->factory->createSimple();
        $id = $oHabitacion->getId_habitacion();
        $idUbi = $oHabitacion->getIdUbiVo();
        $this->repository->Guardar($oHabitacion);

        $habitaciones = $this->repository->getHabitacionesByUbi($idUbi);
        $this->assertIsArray($habitaciones);
        $this->assertNotEmpty($habitaciones);

        $this->repository->Eliminar($oHabitacion);
    }
}
