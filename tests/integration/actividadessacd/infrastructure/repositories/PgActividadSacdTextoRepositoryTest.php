<?php

namespace Tests\integration\actividadessacd\infrastructure\repositories;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use Tests\myTest;
use Tests\factories\actividadessacd\ActividadSacdTextoFactory;

class PgActividadSacdTextoRepositoryTest extends myTest
{
    private ActividadSacdTextoRepositoryInterface $repository;
    private ActividadSacdTextoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
        $this->factory = new ActividadSacdTextoFactory();
    }

    public function test_guardar_nuevo_actividadSacdTexto()
    {
        // Crear instancia usando factory
        $oActividadSacdTexto = $this->factory->createSimple();
        $id = $oActividadSacdTexto->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oActividadSacdTexto);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oActividadSacdTextoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oActividadSacdTextoGuardado);
        $this->assertEquals($id, $oActividadSacdTextoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActividadSacdTextoGuardado);
    }

    public function test_actualizar_actividadSacdTexto_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadSacdTexto = $this->factory->createSimple();
        $id = $oActividadSacdTexto->getId_item();
        $this->repository->Guardar($oActividadSacdTexto);

        // Crear otra instancia con datos diferentes para actualizar
        $oActividadSacdTextoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oActividadSacdTextoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oActividadSacdTextoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oActividadSacdTextoActualizado);

        // Limpiar
        $this->repository->Eliminar($oActividadSacdTextoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadSacdTexto = $this->factory->createSimple();
        $id = $oActividadSacdTexto->getId_item();
        $this->repository->Guardar($oActividadSacdTexto);

        // Buscar por ID
        $oActividadSacdTextoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oActividadSacdTextoEncontrado);
        $this->assertInstanceOf(ActividadSacdTexto::class, $oActividadSacdTextoEncontrado);
        $this->assertEquals($id, $oActividadSacdTextoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oActividadSacdTextoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oActividadSacdTexto = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oActividadSacdTexto);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oActividadSacdTexto = $this->factory->createSimple();
        $id = $oActividadSacdTexto->getId_item();
        $this->repository->Guardar($oActividadSacdTexto);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oActividadSacdTextoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oActividadSacdTextoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_actividadSacdTexto()
    {
        // Crear y guardar instancia usando factory
        $oActividadSacdTexto = $this->factory->createSimple();
        $id = $oActividadSacdTexto->getId_item();
        $this->repository->Guardar($oActividadSacdTexto);

        // Verificar que existe
        $oActividadSacdTextoExiste = $this->repository->findById($id);
        $this->assertNotNull($oActividadSacdTextoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oActividadSacdTextoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oActividadSacdTextoEliminado = $this->repository->findById($id);
        $this->assertNull($oActividadSacdTextoEliminado);
    }

    public function test_get_actividad_sacd_textos_sin_filtros()
    {
        $result = $this->repository->getActividadSacdTextos();
        
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
