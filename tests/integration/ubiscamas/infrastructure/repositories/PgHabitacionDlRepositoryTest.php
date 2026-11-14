<?php

namespace Tests\integration\ubiscamas\infrastructure\persistence\postgresql;

use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\entity\Habitacion;
use Tests\factories\ubiscamas\HabitacionFactory;
use Tests\myTest;
use Tests\support\UbiscamasEnDlInstalada;

class PgHabitacionDlRepositoryTest extends myTest
{
    use UbiscamasEnDlInstalada;

    private HabitacionDlRepositoryInterface $repository;
    private HabitacionFactory $factory;

    public function tearDown(): void
    {
        $this->dejarUbiscamasComoEstaba();
        parent::tearDown();
    }

    public function setUp(): void
    {
        parent::setUp();
        // La dl de la sesión de los tests no tiene el módulo de habitaciones: se usa la que lo tiene.
        $this->usarUbiscamasDeDlInstalada();
        $this->repository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
        $this->factory = new HabitacionFactory();
    }

    public function test_guardar_eliminar_habitacion()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_habitacion();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Habitacion::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById('00000000-0000-4000-8000-000000009999'));
    }
}
