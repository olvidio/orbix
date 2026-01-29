<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\CambioUsuario;
use Tests\myTest;
use Tests\factories\cambios\CambioUsuarioFactory;

class PgCambioUsuarioRepositoryTest extends myTest
{
    private CambioUsuarioRepositoryInterface $repository;
    private CambioUsuarioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);
        $this->factory = new CambioUsuarioFactory();
    }

    public function test_guardar_nuevo_cambioUsuario()
    {
        // Crear instancia usando factory
        $oCambioUsuario = $this->factory->createSimple();
        $id = $oCambioUsuario->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCambioUsuario);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCambioUsuarioGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioGuardado);
        $this->assertEquals($id, $oCambioUsuarioGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioGuardado);
    }

    public function test_actualizar_cambioUsuario_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuario = $this->factory->createSimple();
        $id = $oCambioUsuario->getId_item();
        $this->repository->Guardar($oCambioUsuario);

        // Crear otra instancia con datos diferentes para actualizar
        $oCambioUsuarioUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCambioUsuarioUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCambioUsuarioActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioActualizado);

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuario = $this->factory->createSimple();
        $id = $oCambioUsuario->getId_item();
        $this->repository->Guardar($oCambioUsuario);

        // Buscar por ID
        $oCambioUsuarioEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioEncontrado);
        $this->assertInstanceOf(CambioUsuario::class, $oCambioUsuarioEncontrado);
        $this->assertEquals($id, $oCambioUsuarioEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCambioUsuario = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCambioUsuario);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuario = $this->factory->createSimple();
        $id = $oCambioUsuario->getId_item();
        $this->repository->Guardar($oCambioUsuario);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCambioUsuarioParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCambioUsuarioParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_cambioUsuario()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuario = $this->factory->createSimple();
        $id = $oCambioUsuario->getId_item();
        $this->repository->Guardar($oCambioUsuario);

        // Verificar que existe
        $oCambioUsuarioExiste = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCambioUsuarioExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCambioUsuarioEliminado = $this->repository->findById($id);
        $this->assertNull($oCambioUsuarioEliminado);
    }

    public function test_get_cambios_usuario_sin_filtros()
    {
        $result = $this->repository->getCambiosUsuario();
        
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
