<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\factories\ubis\CentroDlFactory;

class PgCentroDlRepositoryTest extends myTest
{
    private CentroDlRepositoryInterface $repository;
    private CentroDlFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $this->factory = new CentroDlFactory();
    }

    public function test_guardar_nuevo_centroDl()
    {
        // Crear instancia usando factory
        $oCentroDl = $this->factory->createSimple();
        $id = $oCentroDl->getId_ubi();

        // Guardar
        $result = $this->repository->Guardar($oCentroDl);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCentroDlGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCentroDlGuardado);
        $this->assertEquals($id, $oCentroDlGuardado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroDlGuardado);
    }

    public function test_actualizar_centroDl_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroDl = $this->factory->createSimple();
        $id = $oCentroDl->getId_ubi();
        $this->repository->Guardar($oCentroDl);

        // Crear otra instancia con datos diferentes para actualizar
        $oCentroDlUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCentroDlUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCentroDlActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCentroDlActualizado);

        // Limpiar
        $this->repository->Eliminar($oCentroDlActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroDl = $this->factory->createSimple();
        $id = $oCentroDl->getId_ubi();
        $this->repository->Guardar($oCentroDl);

        // Buscar por ID
        $oCentroDlEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCentroDlEncontrado);
        $this->assertInstanceOf(CentroDl::class, $oCentroDlEncontrado);
        $this->assertEquals($id, $oCentroDlEncontrado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroDlEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCentroDl = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCentroDl);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroDl = $this->factory->createSimple();
        $id = $oCentroDl->getId_ubi();
        $this->repository->Guardar($oCentroDl);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_ubi', $aDatos);
        $this->assertEquals($id, $aDatos['id_ubi']);

        // Limpiar
        $oCentroDlParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCentroDlParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_centroDl()
    {
        // Crear y guardar instancia usando factory
        $oCentroDl = $this->factory->createSimple();
        $id = $oCentroDl->getId_ubi();
        $this->repository->Guardar($oCentroDl);

        // Verificar que existe
        $oCentroDlExiste = $this->repository->findById($id);
        $this->assertNotNull($oCentroDlExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCentroDlExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCentroDlEliminado = $this->repository->findById($id);
        $this->assertNull($oCentroDlEliminado);
    }

    public function test_get_array_centros_sin_filtros()
    {
        $result = $this->repository->getArrayCentros();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_centros_sin_filtros()
    {
        $result = $this->repository->getCentros();
        
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
