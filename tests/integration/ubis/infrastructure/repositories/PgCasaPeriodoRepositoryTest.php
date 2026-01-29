<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\entity\CasaPeriodo;
use Tests\factories\ubis\CasaPeriodoFactory;
use Tests\myTest;

class PgCasaPeriodoRepositoryTest extends myTest
{
    private CasaPeriodoRepositoryInterface $repository;
    private CasaPeriodoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $this->factory = new CasaPeriodoFactory();
    }

    public function test_guardar_nuevo_casaPeriodo()
    {
        // Crear instancia usando factory
        $oCasaPeriodo = $this->factory->create();
        $id = $oCasaPeriodo->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCasaPeriodo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCasaPeriodoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCasaPeriodoGuardado);
        $this->assertEquals($id, $oCasaPeriodoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCasaPeriodoGuardado);
    }

    public function test_actualizar_casaPeriodo_existente()
    {
        // Crear y guardar instancia usando factory
        $oCasaPeriodo = $this->factory->create();
        $id = $oCasaPeriodo->getId_item();
        $this->repository->Guardar($oCasaPeriodo);

        // Crear otra instancia con datos diferentes para actualizar
        $oCasaPeriodoUpdated = $this->factory->create($id);

        // Actualizar
        $result = $this->repository->Guardar($oCasaPeriodoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCasaPeriodoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCasaPeriodoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCasaPeriodoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCasaPeriodo = $this->factory->create();
        $id = $oCasaPeriodo->getId_item();
        $this->repository->Guardar($oCasaPeriodo);

        // Buscar por ID
        $oCasaPeriodoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCasaPeriodoEncontrado);
        $this->assertInstanceOf(CasaPeriodo::class, $oCasaPeriodoEncontrado);
        $this->assertEquals($id, $oCasaPeriodoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCasaPeriodoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCasaPeriodo = $this->repository->findById($id_inexistente);

        $this->assertNull($oCasaPeriodo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCasaPeriodo = $this->factory->create();
        $id = $oCasaPeriodo->getId_item();
        $this->repository->Guardar($oCasaPeriodo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCasaPeriodoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCasaPeriodoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_casaPeriodo()
    {
        // Crear y guardar instancia usando factory
        $oCasaPeriodo = $this->factory->create();
        $id = $oCasaPeriodo->getId_item();
        $this->repository->Guardar($oCasaPeriodo);

        // Verificar que existe
        $oCasaPeriodoExiste = $this->repository->findById($id);
        $this->assertNotNull($oCasaPeriodoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCasaPeriodoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCasaPeriodoEliminado = $this->repository->findById($id);
        $this->assertNull($oCasaPeriodoEliminado);
    }

    /*
    public function test_get_array_casa_periodos_sin_filtros()
    {
        //$result = $this->repository->getArrayCasaPeriodos();

        //$this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

    public function test_get_casa_periodos_sin_filtros()
    {
        $result = $this->repository->getCasaPeriodos();

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
