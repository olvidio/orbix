<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\entity\CentroEx;
use Tests\myTest;
use Tests\factories\ubis\CentroExFactory;

class PgCentroExRepositoryTest extends myTest
{
    private CentroExRepositoryInterface $repository;
    private CentroExFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        $this->factory = new CentroExFactory();
    }

    public function test_guardar_nuevo_centroEx()
    {
        // Crear instancia usando factory
        $oCentroEx = $this->factory->createSimple();
        $id = $oCentroEx->getId_ubi();

        // Guardar
        $result = $this->repository->Guardar($oCentroEx);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCentroExGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCentroExGuardado);
        $this->assertEquals($id, $oCentroExGuardado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroExGuardado);
    }

    public function test_actualizar_centroEx_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEx = $this->factory->createSimple();
        $id = $oCentroEx->getId_ubi();
        $this->repository->Guardar($oCentroEx);

        // Crear otra instancia con datos diferentes para actualizar
        $oCentroExUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCentroExUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCentroExActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCentroExActualizado);

        // Limpiar
        $this->repository->Eliminar($oCentroExActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEx = $this->factory->createSimple();
        $id = $oCentroEx->getId_ubi();
        $this->repository->Guardar($oCentroEx);

        // Buscar por ID
        $oCentroExEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCentroExEncontrado);
        $this->assertInstanceOf(CentroEx::class, $oCentroExEncontrado);
        $this->assertEquals($id, $oCentroExEncontrado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroExEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCentroEx = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCentroEx);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEx = $this->factory->createSimple();
        $id = $oCentroEx->getId_ubi();
        $this->repository->Guardar($oCentroEx);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_ubi', $aDatos);
        $this->assertEquals($id, $aDatos['id_ubi']);

        // Limpiar
        $oCentroExParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCentroExParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_centroEx()
    {
        // Crear y guardar instancia usando factory
        $oCentroEx = $this->factory->createSimple();
        $id = $oCentroEx->getId_ubi();
        $this->repository->Guardar($oCentroEx);

        // Verificar que existe
        $oCentroExExiste = $this->repository->findById($id);
        $this->assertNotNull($oCentroExExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCentroExExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCentroExEliminado = $this->repository->findById($id);
        $this->assertNull($oCentroExEliminado);
    }

    public function test_get_array_centros_sin_filtros()
    {
        $result = $this->repository->getArrayCentros();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_centros_sin_filtros()
    {
        $result = $this->repository->getCentros();
        
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
