<?php

namespace Tests\integration\actividadcargos\infrastructure\repositories;

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;
use Tests\myTest;
use Tests\factories\actividadcargos\CargoFactory;

class PgCargoRepositoryTest extends myTest
{
    private CargoRepositoryInterface $repository;
    private CargoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $this->factory = new CargoFactory();
    }

    public function test_guardar_nuevo_cargo()
    {
        // Crear instancia usando factory
        $oCargo = $this->factory->createSimple();
        $id = $oCargo->getId_cargo();

        // Guardar
        $result = $this->repository->Guardar($oCargo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCargoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCargoGuardado);
        $this->assertEquals($id, $oCargoGuardado->getId_cargo());

        // Limpiar
        $this->repository->Eliminar($oCargoGuardado);
    }

    public function test_actualizar_cargo_existente()
    {
        // Crear y guardar instancia usando factory
        $oCargo = $this->factory->createSimple();
        $id = $oCargo->getId_cargo();
        $this->repository->Guardar($oCargo);

        // Crear otra instancia con datos diferentes para actualizar
        $oCargoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCargoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCargoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCargoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCargoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCargo = $this->factory->createSimple();
        $id = $oCargo->getId_cargo();
        $this->repository->Guardar($oCargo);

        // Buscar por ID
        $oCargoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCargoEncontrado);
        $this->assertInstanceOf(Cargo::class, $oCargoEncontrado);
        $this->assertEquals($id, $oCargoEncontrado->getId_cargo());

        // Limpiar
        $this->repository->Eliminar($oCargoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCargo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCargo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCargo = $this->factory->createSimple();
        $id = $oCargo->getId_cargo();
        $this->repository->Guardar($oCargo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_cargo', $aDatos);
        $this->assertEquals($id, $aDatos['id_cargo']);

        // Limpiar
        $oCargoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCargoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_cargo()
    {
        // Crear y guardar instancia usando factory
        $oCargo = $this->factory->createSimple();
        $id = $oCargo->getId_cargo();
        $this->repository->Guardar($oCargo);

        // Verificar que existe
        $oCargoExiste = $this->repository->findById($id);
        $this->assertNotNull($oCargoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCargoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCargoEliminado = $this->repository->findById($id);
        $this->assertNull($oCargoEliminado);
    }

    public function test_get_array_id_cargos_sacd_sin_filtros()
    {
        $result = $this->repository->getArrayIdCargosSacd();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_cargos_sin_filtros()
    {
        $result = $this->repository->getArrayCargos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_cargos_sin_filtros()
    {
        $result = $this->repository->getCargos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
