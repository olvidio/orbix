<?php

namespace Tests\integration\personas\infrastructure\repositories;

use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use Tests\myTest;
use Tests\factories\personas\TrasladoFactory;

class PgTrasladoRepositoryTest extends myTest
{
    private TrasladoRepositoryInterface $repository;
    private TrasladoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TrasladoRepositoryInterface::class);
        $this->factory = new TrasladoFactory();
    }

    public function test_guardar_nuevo_traslado()
    {
        // Crear instancia usando factory
        $oTraslado = $this->factory->createSimple();
        $id = $oTraslado->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oTraslado);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTrasladoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTrasladoGuardado);
        $this->assertEquals($id, $oTrasladoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTrasladoGuardado);
    }

    public function test_actualizar_traslado_existente()
    {
        // Crear y guardar instancia usando factory
        $oTraslado = $this->factory->createSimple();
        $id = $oTraslado->getId_item();
        $this->repository->Guardar($oTraslado);

        // Crear otra instancia con datos diferentes para actualizar
        $oTrasladoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTrasladoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTrasladoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTrasladoActualizado);

        // Limpiar
        $this->repository->Eliminar($oTrasladoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTraslado = $this->factory->createSimple();
        $id = $oTraslado->getId_item();
        $this->repository->Guardar($oTraslado);

        // Buscar por ID
        $oTrasladoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTrasladoEncontrado);
        $this->assertInstanceOf(Traslado::class, $oTrasladoEncontrado);
        $this->assertEquals($id, $oTrasladoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oTrasladoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTraslado = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTraslado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTraslado = $this->factory->createSimple();
        $id = $oTraslado->getId_item();
        $this->repository->Guardar($oTraslado);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oTrasladoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTrasladoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_traslado()
    {
        // Crear y guardar instancia usando factory
        $oTraslado = $this->factory->createSimple();
        $id = $oTraslado->getId_item();
        $this->repository->Guardar($oTraslado);

        // Verificar que existe
        $oTrasladoExiste = $this->repository->findById($id);
        $this->assertNotNull($oTrasladoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTrasladoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTrasladoEliminado = $this->repository->findById($id);
        $this->assertNull($oTrasladoEliminado);
    }

    public function test_get_traslados_sin_filtros()
    {
        $result = $this->repository->getTraslados();
        
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
