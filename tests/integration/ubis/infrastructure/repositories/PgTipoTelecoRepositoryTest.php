<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\TipoTeleco;
use Tests\factories\ubis\TipoTelecoFactory;
use Tests\myTest;

class PgTipoTelecoRepositoryTest extends myTest
{
    private TipoTelecoRepositoryInterface $repository;
    private TipoTelecoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TipoTelecoRepositoryInterface::class);
        $this->factory = new TipoTelecoFactory();
    }

    public function test_guardar_nuevo_tipoTeleco()
    {
        // Crear instancia usando factory
        $oTipoTeleco = $this->factory->createSimple();
        $id = $oTipoTeleco->getId();

        // Guardar
        $result = $this->repository->Guardar($oTipoTeleco);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTipoTelecoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTipoTelecoGuardado);
        $this->assertEquals($id, $oTipoTelecoGuardado->getId());

        // Limpiar
        $this->repository->Eliminar($oTipoTelecoGuardado);
    }

    public function test_actualizar_tipoTeleco_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoTeleco = $this->factory->createSimple();
        $id = $oTipoTeleco->getId();
        $this->repository->Guardar($oTipoTeleco);

        // Crear otra instancia con datos diferentes para actualizar
        $oTipoTelecoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTipoTelecoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTipoTelecoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTipoTelecoActualizado);

        // Limpiar
        $this->repository->Eliminar($oTipoTelecoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoTeleco = $this->factory->createSimple();
        $id = $oTipoTeleco->getId();
        $this->repository->Guardar($oTipoTeleco);

        // Buscar por ID
        $oTipoTelecoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTipoTelecoEncontrado);
        $this->assertInstanceOf(TipoTeleco::class, $oTipoTelecoEncontrado);
        $this->assertEquals($id, $oTipoTelecoEncontrado->getId());

        // Limpiar
        $this->repository->Eliminar($oTipoTelecoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTipoTeleco = $this->repository->findById($id_inexistente);

        $this->assertNull($oTipoTeleco);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTipoTeleco = $this->factory->createSimple();
        $id = $oTipoTeleco->getId();
        $this->repository->Guardar($oTipoTeleco);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id', $aDatos);
        $this->assertEquals($id, $aDatos['id']);

        // Limpiar
        $oTipoTelecoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTipoTelecoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_tipoTeleco()
    {
        // Crear y guardar instancia usando factory
        $oTipoTeleco = $this->factory->createSimple();
        $id = $oTipoTeleco->getId();
        $this->repository->Guardar($oTipoTeleco);

        // Verificar que existe
        $oTipoTelecoExiste = $this->repository->findById($id);
        $this->assertNotNull($oTipoTelecoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTipoTelecoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTipoTelecoEliminado = $this->repository->findById($id);
        $this->assertNull($oTipoTelecoEliminado);
    }

    public function test_get_tipos_teleco_sin_filtros()
    {
        $result = $this->repository->getTiposTeleco();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
