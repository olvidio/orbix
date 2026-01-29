<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroEllas;
use Tests\myTest;
use Tests\factories\ubis\CentroEllasFactory;

class PgCentroEllasRepositoryTest extends myTest
{
    private CentroEllasRepositoryInterface $repository;
    private CentroEllasFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $this->factory = new CentroEllasFactory();
    }

    public function test_guardar_nuevo_centroEllas()
    {
        // Crear instancia usando factory
        $oCentroEllas = $this->factory->createSimple();
        $id = $oCentroEllas->getId_ubi();

        // Guardar
        $result = $this->repository->Guardar($oCentroEllas);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCentroEllasGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllasGuardado);
        $this->assertEquals($id, $oCentroEllasGuardado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroEllasGuardado);
    }

    public function test_actualizar_centroEllas_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllas = $this->factory->createSimple();
        $id = $oCentroEllas->getId_ubi();
        $this->repository->Guardar($oCentroEllas);

        // Crear otra instancia con datos diferentes para actualizar
        $oCentroEllasUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCentroEllasUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCentroEllasActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllasActualizado);

        // Limpiar
        $this->repository->Eliminar($oCentroEllasActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllas = $this->factory->createSimple();
        $id = $oCentroEllas->getId_ubi();
        $this->repository->Guardar($oCentroEllas);

        // Buscar por ID
        $oCentroEllasEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllasEncontrado);
        $this->assertInstanceOf(CentroEllas::class, $oCentroEllasEncontrado);
        $this->assertEquals($id, $oCentroEllasEncontrado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroEllasEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCentroEllas = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCentroEllas);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllas = $this->factory->createSimple();
        $id = $oCentroEllas->getId_ubi();
        $this->repository->Guardar($oCentroEllas);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_ubi', $aDatos);
        $this->assertEquals($id, $aDatos['id_ubi']);

        // Limpiar
        $oCentroEllasParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCentroEllasParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_centroEllas()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllas = $this->factory->createSimple();
        $id = $oCentroEllas->getId_ubi();
        $this->repository->Guardar($oCentroEllas);

        // Verificar que existe
        $oCentroEllasExiste = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllasExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCentroEllasExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCentroEllasEliminado = $this->repository->findById($id);
        $this->assertNull($oCentroEllasEliminado);
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
