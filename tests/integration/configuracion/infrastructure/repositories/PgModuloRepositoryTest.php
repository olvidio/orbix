<?php

namespace Tests\integration\configuracion\infrastructure\repositories;

use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\Modulo;
use Tests\myTest;
use Tests\factories\configuracion\ModuloFactory;

class PgModuloRepositoryTest extends myTest
{
    private ModuloRepositoryInterface $repository;
    private ModuloFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ModuloRepositoryInterface::class);
        $this->factory = new ModuloFactory();
    }

    public function test_guardar_nuevo_modulo()
    {
        // Crear instancia usando factory
        $oModulo = $this->factory->createSimple();
        $id = $oModulo->getId_mod();

        // Guardar
        $result = $this->repository->Guardar($oModulo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oModuloGuardado = $this->repository->findById($id);
        $this->assertNotNull($oModuloGuardado);
        $this->assertEquals($id, $oModuloGuardado->getId_mod());

        // Limpiar
        $this->repository->Eliminar($oModuloGuardado);
    }

    public function test_actualizar_modulo_existente()
    {
        // Crear y guardar instancia usando factory
        $oModulo = $this->factory->createSimple();
        $id = $oModulo->getId_mod();
        $this->repository->Guardar($oModulo);

        // Crear otra instancia con datos diferentes para actualizar
        $oModuloUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oModuloUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oModuloActualizado = $this->repository->findById($id);
        $this->assertNotNull($oModuloActualizado);

        // Limpiar
        $this->repository->Eliminar($oModuloActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oModulo = $this->factory->createSimple();
        $id = $oModulo->getId_mod();
        $this->repository->Guardar($oModulo);

        // Buscar por ID
        $oModuloEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oModuloEncontrado);
        $this->assertInstanceOf(Modulo::class, $oModuloEncontrado);
        $this->assertEquals($id, $oModuloEncontrado->getId_mod());

        // Limpiar
        $this->repository->Eliminar($oModuloEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oModulo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oModulo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oModulo = $this->factory->createSimple();
        $id = $oModulo->getId_mod();
        $this->repository->Guardar($oModulo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_mod', $aDatos);
        $this->assertEquals($id, $aDatos['id_mod']);

        // Limpiar
        $oModuloParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oModuloParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_modulo()
    {
        // Crear y guardar instancia usando factory
        $oModulo = $this->factory->createSimple();
        $id = $oModulo->getId_mod();
        $this->repository->Guardar($oModulo);

        // Verificar que existe
        $oModuloExiste = $this->repository->findById($id);
        $this->assertNotNull($oModuloExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oModuloExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oModuloEliminado = $this->repository->findById($id);
        $this->assertNull($oModuloEliminado);
    }

    public function test_get_array_modulos_sin_filtros()
    {
        $result = $this->repository->getArrayModulos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_modulos_sin_filtros()
    {
        $result = $this->repository->getModulos();
        
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
