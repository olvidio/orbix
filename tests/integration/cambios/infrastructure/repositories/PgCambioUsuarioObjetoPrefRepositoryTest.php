<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use Tests\myTest;
use Tests\factories\cambios\CambioUsuarioObjetoPrefFactory;

class PgCambioUsuarioObjetoPrefRepositoryTest extends myTest
{
    private CambioUsuarioObjetoPrefRepositoryInterface $repository;
    private CambioUsuarioObjetoPrefFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $this->factory = new CambioUsuarioObjetoPrefFactory();
    }

    public function test_guardar_nuevo_cambioUsuarioObjetoPref()
    {
        // Crear instancia usando factory
        $oCambioUsuarioObjetoPref = $this->factory->createSimple();
        $id = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();

        // Guardar
        $result = $this->repository->Guardar($oCambioUsuarioObjetoPref);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCambioUsuarioObjetoPrefGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioObjetoPrefGuardado);
        $this->assertEquals($id, $oCambioUsuarioObjetoPrefGuardado->getId_item_usuario_objeto());

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioObjetoPrefGuardado);
    }

    public function test_actualizar_cambioUsuarioObjetoPref_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioObjetoPref = $this->factory->createSimple();
        $id = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $this->repository->Guardar($oCambioUsuarioObjetoPref);

        // Crear otra instancia con datos diferentes para actualizar
        $oCambioUsuarioObjetoPrefUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCambioUsuarioObjetoPrefUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCambioUsuarioObjetoPrefActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioObjetoPrefActualizado);

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioObjetoPrefActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioObjetoPref = $this->factory->createSimple();
        $id = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $this->repository->Guardar($oCambioUsuarioObjetoPref);

        // Buscar por ID
        $oCambioUsuarioObjetoPrefEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioObjetoPrefEncontrado);
        $this->assertInstanceOf(CambioUsuarioObjetoPref::class, $oCambioUsuarioObjetoPrefEncontrado);
        $this->assertEquals($id, $oCambioUsuarioObjetoPrefEncontrado->getId_item_usuario_objeto());

        // Limpiar
        $this->repository->Eliminar($oCambioUsuarioObjetoPrefEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCambioUsuarioObjetoPref = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCambioUsuarioObjetoPref);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioObjetoPref = $this->factory->createSimple();
        $id = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $this->repository->Guardar($oCambioUsuarioObjetoPref);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item_usuario_objeto', $aDatos);
        $this->assertEquals($id, $aDatos['id_item_usuario_objeto']);

        // Limpiar
        $oCambioUsuarioObjetoPrefParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCambioUsuarioObjetoPrefParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_cambioUsuarioObjetoPref()
    {
        // Crear y guardar instancia usando factory
        $oCambioUsuarioObjetoPref = $this->factory->createSimple();
        $id = $oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $this->repository->Guardar($oCambioUsuarioObjetoPref);

        // Verificar que existe
        $oCambioUsuarioObjetoPrefExiste = $this->repository->findById($id);
        $this->assertNotNull($oCambioUsuarioObjetoPrefExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCambioUsuarioObjetoPrefExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCambioUsuarioObjetoPrefEliminado = $this->repository->findById($id);
        $this->assertNull($oCambioUsuarioObjetoPrefEliminado);
    }

    public function test_get_cambio_usuario_objeto_prefs_sin_filtros()
    {
        $result = $this->repository->getCambioUsuarioObjetoPrefs();
        
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
