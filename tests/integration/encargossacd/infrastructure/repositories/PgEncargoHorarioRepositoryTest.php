<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoHorarioFactory;

class PgEncargoHorarioRepositoryTest extends myTest
{
    private EncargoHorarioRepositoryInterface $repository;
    private EncargoHorarioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $this->factory = new EncargoHorarioFactory();
    }

    public function test_guardar_nuevo_encargoHorario()
    {
        // Crear instancia usando factory
        $oEncargoHorario = $this->factory->createSimple();
        $id = $oEncargoHorario->getId_item_h();

        // Guardar
        $result = $this->repository->Guardar($oEncargoHorario);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoHorarioGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoHorarioGuardado);
        $this->assertEquals($id, $oEncargoHorarioGuardado->getId_item_h());

        // Limpiar
        $this->repository->Eliminar($oEncargoHorarioGuardado);
    }

    public function test_actualizar_encargoHorario_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoHorario = $this->factory->createSimple();
        $id = $oEncargoHorario->getId_item_h();
        $this->repository->Guardar($oEncargoHorario);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoHorarioUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoHorarioUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoHorarioActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoHorarioActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoHorarioActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoHorario = $this->factory->createSimple();
        $id = $oEncargoHorario->getId_item_h();
        $this->repository->Guardar($oEncargoHorario);

        // Buscar por ID
        $oEncargoHorarioEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoHorarioEncontrado);
        $this->assertInstanceOf(EncargoHorario::class, $oEncargoHorarioEncontrado);
        $this->assertEquals($id, $oEncargoHorarioEncontrado->getId_item_h());

        // Limpiar
        $this->repository->Eliminar($oEncargoHorarioEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargoHorario = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargoHorario);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoHorario = $this->factory->createSimple();
        $id = $oEncargoHorario->getId_item_h();
        $this->repository->Guardar($oEncargoHorario);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_enc', $aDatos);
        $this->assertEquals($id, $aDatos['id_enc']);

        // Limpiar
        $oEncargoHorarioParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoHorarioParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoHorario()
    {
        // Crear y guardar instancia usando factory
        $oEncargoHorario = $this->factory->createSimple();
        $id = $oEncargoHorario->getId_item_h();
        $this->repository->Guardar($oEncargoHorario);

        // Verificar que existe
        $oEncargoHorarioExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoHorarioExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoHorarioExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoHorarioEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoHorarioEliminado);
    }

    public function test_get_encargo_horarios_sin_filtros()
    {
        $result = $this->repository->getEncargoHorarios();
        
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
