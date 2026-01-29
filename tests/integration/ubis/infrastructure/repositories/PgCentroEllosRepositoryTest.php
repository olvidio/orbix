<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroEllos;
use Tests\myTest;
use Tests\factories\ubis\CentroEllosFactory;

class PgCentroEllosRepositoryTest extends myTest
{
    private CentroEllosRepositoryInterface $repository;
    private CentroEllosFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);
        $this->factory = new CentroEllosFactory();
    }

    public function test_guardar_nuevo_centroEllos()
    {
        // Crear instancia usando factory
        $oCentroEllos = $this->factory->createSimple();
        $id = $oCentroEllos->getId_ubi();

        // Guardar
        $result = $this->repository->Guardar($oCentroEllos);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCentroEllosGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllosGuardado);
        $this->assertEquals($id, $oCentroEllosGuardado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroEllosGuardado);
    }

    public function test_actualizar_centroEllos_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllos = $this->factory->createSimple();
        $id = $oCentroEllos->getId_ubi();
        $this->repository->Guardar($oCentroEllos);

        // Crear otra instancia con datos diferentes para actualizar
        $oCentroEllosUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCentroEllosUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCentroEllosActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllosActualizado);

        // Limpiar
        $this->repository->Eliminar($oCentroEllosActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllos = $this->factory->createSimple();
        $id = $oCentroEllos->getId_ubi();
        $this->repository->Guardar($oCentroEllos);

        // Buscar por ID
        $oCentroEllosEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllosEncontrado);
        $this->assertInstanceOf(CentroEllos::class, $oCentroEllosEncontrado);
        $this->assertEquals($id, $oCentroEllosEncontrado->getId_ubi());

        // Limpiar
        $this->repository->Eliminar($oCentroEllosEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCentroEllos = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oCentroEllos);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllos = $this->factory->createSimple();
        $id = $oCentroEllos->getId_ubi();
        $this->repository->Guardar($oCentroEllos);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_ubi', $aDatos);
        $this->assertEquals($id, $aDatos['id_ubi']);

        // Limpiar
        $oCentroEllosParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCentroEllosParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_centroEllos()
    {
        // Crear y guardar instancia usando factory
        $oCentroEllos = $this->factory->createSimple();
        $id = $oCentroEllos->getId_ubi();
        $this->repository->Guardar($oCentroEllos);

        // Verificar que existe
        $oCentroEllosExiste = $this->repository->findById($id);
        $this->assertNotNull($oCentroEllosExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCentroEllosExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCentroEllosEliminado = $this->repository->findById($id);
        $this->assertNull($oCentroEllosEliminado);
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
