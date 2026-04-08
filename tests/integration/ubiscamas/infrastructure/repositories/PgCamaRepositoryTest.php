<?php

namespace Tests\integration\ubiscamas\infrastructure\persistence\postgresql;

use src\ubiscamas\domain\contracts\CamaRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\HabitacionId;
use Tests\myTest;
use Tests\factories\ubiscamas\CamaFactory;
use Tests\factories\ubiscamas\HabitacionFactory;

class PgCamaRepositoryTest extends myTest
{
    private CamaRepositoryInterface $repository;
    private HabitacionRepositoryInterface $habitacionRepository;
    private CamaFactory $factory;
    private HabitacionFactory $habitacionFactory;
    private string $idHabitacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CamaRepositoryInterface::class);
        $this->habitacionRepository = $GLOBALS['container']->get(HabitacionRepositoryInterface::class);
        $this->factory = new CamaFactory();
        $this->habitacionFactory = new HabitacionFactory();

        // Crear una habitación de apoyo para las camas
        $oHabitacion = $this->habitacionFactory->createSimple();
        $this->idHabitacion = $oHabitacion->getId_habitacion();
        $this->habitacionRepository->Guardar($oHabitacion);
    }

    public function tearDown(): void
    {
        // Limpiar la habitación de apoyo
        $oHabitacion = $this->habitacionRepository->findById($this->idHabitacion);
        if ($oHabitacion !== null) {
            $this->habitacionRepository->Eliminar($oHabitacion);
        }
    }

    public function test_guardar_nueva_cama()
    {
        $oCama = $this->factory->createSimple(null, $this->idHabitacion);
        $id = $oCama->getIdCama();

        $result = $this->repository->Guardar($oCama);
        $this->assertTrue($result);

        $oGuardada = $this->repository->findById($id);
        $this->assertNotNull($oGuardada);
        $this->assertEquals($id, $oGuardada->getIdCama());

        $this->repository->Eliminar($oGuardada);
    }

    public function test_actualizar_cama_existente()
    {
        $oCama = $this->factory->createSimple(null, $this->idHabitacion);
        $id = $oCama->getIdCama();
        $this->repository->Guardar($oCama);

        $oActualizada = $this->factory->createSimple($id, $this->idHabitacion);
        $result = $this->repository->Guardar($oActualizada);
        $this->assertTrue($result);

        $oObtenida = $this->repository->findById($id);
        $this->assertNotNull($oObtenida);

        $this->repository->Eliminar($oObtenida);
    }

    public function test_find_by_id_existente()
    {
        $oCama = $this->factory->createSimple(null, $this->idHabitacion);
        $id = $oCama->getIdCama();
        $this->repository->Guardar($oCama);

        $oEncontrada = $this->repository->findById($id);
        $this->assertNotNull($oEncontrada);
        $this->assertInstanceOf(Cama::class, $oEncontrada);
        $this->assertEquals($id, $oEncontrada->getIdCama());

        $this->repository->Eliminar($oEncontrada);
    }

    public function test_find_by_id_no_existente()
    {
        $oCama = $this->repository->findById('00000000-0000-0000-0000-000000000000');
        $this->assertNull($oCama);
    }

    public function test_eliminar_cama()
    {
        $oCama = $this->factory->createSimple(null, $this->idHabitacion);
        $id = $oCama->getIdCama();
        $this->repository->Guardar($oCama);

        $oGuardada = $this->repository->findById($id);
        $this->assertNotNull($oGuardada);

        $result = $this->repository->Eliminar($oGuardada);
        $this->assertTrue($result);

        $oEliminada = $this->repository->findById($id);
        $this->assertNull($oEliminada);
    }

    public function test_get_camas_by_habitacion()
    {
        $oCama = $this->factory->createSimple(null, $this->idHabitacion);
        $id = $oCama->getIdCama();
        $this->repository->Guardar($oCama);

        $camas = $this->repository->getCamasByHabitacion(new HabitacionId($this->idHabitacion));
        $this->assertIsArray($camas);
        $this->assertNotEmpty($camas);

        $this->repository->Eliminar($oCama);
    }
}
