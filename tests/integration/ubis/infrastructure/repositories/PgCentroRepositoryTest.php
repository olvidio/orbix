<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;
use Tests\myTest;
use Tests\factories\ubis\CentroFactory;

class PgCentroRepositoryTest extends myTest
{
    private CentroRepositoryInterface $repository;
    private CentroFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $this->factory = new CentroFactory();
    }

    public function test_guardar_nuevo_centro()
    {
        // Crear instancia usando factory
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();

        // Guardar
        $result = $this->repository->Guardar($oCentro);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCentroGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCentroGuardado);
        $this->assertEquals($id, $oCentroGuardado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroGuardado);
    }

    public function test_actualizar_centro_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();
        $this->repository->Guardar($oCentro);

        // Crear otra instancia con datos diferentes para actualizar
        $oCentroUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCentroUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCentroActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCentroActualizado);

        // Limpiar
        $this->repository->Eliminar($oCentroActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();
        $this->repository->Guardar($oCentro);

        // Buscar por ID
        $oCentroEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEncontrado);
        $this->assertInstanceOf(Centro::class, $oCentroEncontrado);
        $this->assertEquals($id, $oCentroEncontrado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCentro = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCentro);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();
        $this->repository->Guardar($oCentro);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_ubi', $aDatos);
        $this->assertEquals($id, $aDatos['id_ubi']);

        // Limpiar
        $oCentroParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCentroParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_centro()
    {
        // Crear y guardar instancia usando factory
        $oCentro = $this->factory->createSimple();
        $id = $oCentro->getId_ubi();
        $this->repository->Guardar($oCentro);

        // Verificar que existe
        $oCentroExiste = $this->repository->findById($id);
        $this->assertNotNull($oCentroExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCentroExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCentroEliminado = $this->repository->findById($id);
        $this->assertNull($oCentroEliminado);
    }

    public function test_get_array_centros_cdc_sin_filtros()
    {
        $result = $this->repository->getArrayCentrosCdc();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
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

}
