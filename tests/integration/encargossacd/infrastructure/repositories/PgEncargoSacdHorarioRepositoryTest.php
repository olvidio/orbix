<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoSacdHorarioFactory;

class PgEncargoSacdHorarioRepositoryTest extends myTest
{
    private EncargoSacdHorarioRepositoryInterface $repository;
    private EncargoSacdHorarioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $this->factory = new EncargoSacdHorarioFactory();
    }

    public function test_guardar_nuevo_encargoSacdHorario()
    {
        // Crear instancia usando factory
        $oEncargoSacdHorario = $this->factory->createSimple();
        $id = $oEncargoSacdHorario->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oEncargoSacdHorario);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoSacdHorarioGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdHorarioGuardado);
        $this->assertEquals($id, $oEncargoSacdHorarioGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdHorarioGuardado);
    }

    public function test_actualizar_encargoSacdHorario_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdHorario = $this->factory->createSimple();
        $id = $oEncargoSacdHorario->getId_item();
        $this->repository->Guardar($oEncargoSacdHorario);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoSacdHorarioUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoSacdHorarioUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoSacdHorarioActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdHorarioActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdHorarioActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdHorario = $this->factory->createSimple();
        $id = $oEncargoSacdHorario->getId_item();
        $this->repository->Guardar($oEncargoSacdHorario);

        // Buscar por ID
        $oEncargoSacdHorarioEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdHorarioEncontrado);
        $this->assertInstanceOf(EncargoSacdHorario::class, $oEncargoSacdHorarioEncontrado);
        $this->assertEquals($id, $oEncargoSacdHorarioEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoSacdHorarioEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargoSacdHorario = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargoSacdHorario);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdHorario = $this->factory->createSimple();
        $id = $oEncargoSacdHorario->getId_item();
        $this->repository->Guardar($oEncargoSacdHorario);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oEncargoSacdHorarioParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoSacdHorarioParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoSacdHorario()
    {
        // Crear y guardar instancia usando factory
        $oEncargoSacdHorario = $this->factory->createSimple();
        $id = $oEncargoSacdHorario->getId_item();
        $this->repository->Guardar($oEncargoSacdHorario);

        // Verificar que existe
        $oEncargoSacdHorarioExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoSacdHorarioExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoSacdHorarioExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoSacdHorarioEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoSacdHorarioEliminado);
    }

    public function test_get_encargo_sacd_horarios_sin_filtros()
    {
        $result = $this->repository->getEncargoSacdHorarios();
        
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
