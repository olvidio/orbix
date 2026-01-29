<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use Tests\myTest;
use Tests\factories\cambios\CambioFactory;

class PgCambioDlRepositoryTest extends myTest
{
    private CambioDlRepositoryInterface $repository;
    private CambioFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
        $this->factory = new CambioFactory();
    }

    public function test_guardar_nuevo_cambioDl()
    {
        // Crear instancia usando factory
        $oCambio = $this->factory->createSimple();
        $id = $oCambio->getId_item_cambio();

        // Guardar
        $result = $this->repository->Guardar($oCambio);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCambioGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCambioGuardado);
        $this->assertEquals($id, $oCambioGuardado->getId_item_cambio());

        // Limpiar
        $this->repository->Eliminar($oCambioGuardado);
    }

    public function test_actualizar_cambioDl_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambio = $this->factory->createSimple();
        $id = $oCambio->getId_item_cambio();
        $this->repository->Guardar($oCambio);

        // Crear otra instancia con datos diferentes para actualizar
        $oCambioUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCambioUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCambioActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCambioActualizado);

        // Limpiar
        $this->repository->Eliminar($oCambioActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambio = $this->factory->createSimple();
        $id = $oCambio->getId_item_cambio();
        $this->repository->Guardar($oCambio);

        // Buscar por ID
        $oCambioEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCambioEncontrado);
        $this->assertInstanceOf(Cambio::class, $oCambioEncontrado);
        $this->assertEquals($id, $oCambioEncontrado->getId_item_cambio());

        // Limpiar
        $this->repository->Eliminar($oCambioEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCambio = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCambio);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCambio = $this->factory->createSimple();
        $id = $oCambio->getId_item_cambio();
        $this->repository->Guardar($oCambio);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item_cambio', $aDatos);
        $this->assertEquals($id, $aDatos['id_item_cambio']);

        // Limpiar
        $oCambioParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCambioParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_cambioDl()
    {
        // Crear y guardar instancia usando factory
        $oCambio = $this->factory->createSimple();
        $id = $oCambio->getId_item_cambio();
        $this->repository->Guardar($oCambio);

        // Verificar que existe
        $oCambioExiste = $this->repository->findById($id);
        $this->assertNotNull($oCambioExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCambioExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCambioEliminado = $this->repository->findById($id);
        $this->assertNull($oCambioEliminado);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();
        
        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
    }

}
