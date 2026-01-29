<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoTexto;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoTextoFactory;

class PgEncargoTextoRepositoryTest extends myTest
{
    private EncargoTextoRepositoryInterface $repository;
    private EncargoTextoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoTextoRepositoryInterface::class);
        $this->factory = new EncargoTextoFactory();
    }

    public function test_guardar_nuevo_encargoTexto()
    {
        // Crear instancia usando factory
        $oEncargoTexto = $this->factory->createSimple();
        $id = $oEncargoTexto->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oEncargoTexto);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoTextoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTextoGuardado);
        $this->assertEquals($id, $oEncargoTextoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoTextoGuardado);
    }

    public function test_actualizar_encargoTexto_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTexto = $this->factory->createSimple();
        $id = $oEncargoTexto->getId_item();
        $this->repository->Guardar($oEncargoTexto);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoTextoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoTextoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoTextoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTextoActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoTextoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTexto = $this->factory->createSimple();
        $id = $oEncargoTexto->getId_item();
        $this->repository->Guardar($oEncargoTexto);

        // Buscar por ID
        $oEncargoTextoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTextoEncontrado);
        $this->assertInstanceOf(EncargoTexto::class, $oEncargoTextoEncontrado);
        $this->assertEquals($id, $oEncargoTextoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oEncargoTextoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargoTexto = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargoTexto);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTexto = $this->factory->createSimple();
        $id = $oEncargoTexto->getId_item();
        $this->repository->Guardar($oEncargoTexto);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oEncargoTextoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoTextoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargoTexto()
    {
        // Crear y guardar instancia usando factory
        $oEncargoTexto = $this->factory->createSimple();
        $id = $oEncargoTexto->getId_item();
        $this->repository->Guardar($oEncargoTexto);

        // Verificar que existe
        $oEncargoTextoExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoTextoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoTextoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoTextoEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoTextoEliminado);
    }

    public function test_get_encargo_textos_sin_filtros()
    {
        $result = $this->repository->getEncargoTextos();
        
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
