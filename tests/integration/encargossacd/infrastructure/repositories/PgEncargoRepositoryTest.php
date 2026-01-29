<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use Tests\myTest;
use Tests\factories\encargossacd\EncargoFactory;

class PgEncargoRepositoryTest extends myTest
{
    private EncargoRepositoryInterface $repository;
    private EncargoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $this->factory = new EncargoFactory();
    }

    public function test_guardar_nuevo_encargo()
    {
        // Crear instancia usando factory
        $oEncargo = $this->factory->createSimple();
        $id = $oEncargo->getId_enc();

        // Guardar
        $result = $this->repository->Guardar($oEncargo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oEncargoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoGuardado);
        $this->assertEquals($id, $oEncargoGuardado->getId_enc());

        // Limpiar
        $this->repository->Eliminar($oEncargoGuardado);
    }

    public function test_actualizar_encargo_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargo = $this->factory->createSimple();
        $id = $oEncargo->getId_enc();
        $this->repository->Guardar($oEncargo);

        // Crear otra instancia con datos diferentes para actualizar
        $oEncargoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oEncargoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oEncargoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoActualizado);

        // Limpiar
        $this->repository->Eliminar($oEncargoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargo = $this->factory->createSimple();
        $id = $oEncargo->getId_enc();
        $this->repository->Guardar($oEncargo);

        // Buscar por ID
        $oEncargoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oEncargoEncontrado);
        $this->assertInstanceOf(Encargo::class, $oEncargoEncontrado);
        $this->assertEquals($id, $oEncargoEncontrado->getId_enc());

        // Limpiar
        $this->repository->Eliminar($oEncargoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oEncargo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oEncargo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oEncargo = $this->factory->createSimple();
        $id = $oEncargo->getId_enc();
        $this->repository->Guardar($oEncargo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_enc', $aDatos);
        $this->assertEquals($id, $aDatos['id_enc']);

        // Limpiar
        $oEncargoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oEncargoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_encargo()
    {
        // Crear y guardar instancia usando factory
        $oEncargo = $this->factory->createSimple();
        $id = $oEncargo->getId_enc();
        $this->repository->Guardar($oEncargo);

        // Verificar que existe
        $oEncargoExiste = $this->repository->findById($id);
        $this->assertNotNull($oEncargoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oEncargoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oEncargoEliminado = $this->repository->findById($id);
        $this->assertNull($oEncargoEliminado);
    }

    public function test_get_encargos_sin_filtros()
    {
        $result = $this->repository->getEncargos();
        
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
