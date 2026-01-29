<?php

namespace Tests\integration\ubis\infrastructure\repositories;

use src\ubis\domain\contracts\DireccionRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use Tests\myTest;
use Tests\factories\ubis\DireccionFactory;

class PgDireccionRepositoryTest extends myTest
{
    private DireccionRepositoryInterface $repository;
    private DireccionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DireccionRepositoryInterface::class);
        $this->factory = new DireccionFactory();
    }

    public function test_guardar_nuevo_direccion()
    {
        // Crear instancia usando factory
        $oDireccion = $this->factory->createSimple();
        $id = $oDireccion->getId_direccion();

        // Guardar
        $result = $this->repository->Guardar($oDireccion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oDireccionGuardado = $this->repository->findById($id);
        $this->assertNotNull($oDireccionGuardado);
        $this->assertEquals($id, $oDireccionGuardado->getId_direccion());

        // Limpiar
        $this->repository->Eliminar($oDireccionGuardado);
    }

    public function test_actualizar_direccion_existente()
    {
        // Crear y guardar instancia usando factory
        $oDireccion = $this->factory->createSimple();
        $id = $oDireccion->getId_direccion();
        $this->repository->Guardar($oDireccion);

        // Crear otra instancia con datos diferentes para actualizar
        $oDireccionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oDireccionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oDireccionActualizado = $this->repository->findById($id);
        $this->assertNotNull($oDireccionActualizado);

        // Limpiar
        $this->repository->Eliminar($oDireccionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDireccion = $this->factory->createSimple();
        $id = $oDireccion->getId_direccion();
        $this->repository->Guardar($oDireccion);

        // Buscar por ID
        $oDireccionEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oDireccionEncontrado);
        $this->assertInstanceOf(Direccion::class, $oDireccionEncontrado);
        $this->assertEquals($id, $oDireccionEncontrado->getId_direccion());

        // Limpiar
        $this->repository->Eliminar($oDireccionEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oDireccion = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oDireccion);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oDireccion = $this->factory->createSimple();
        $id = $oDireccion->getId_direccion();
        $this->repository->Guardar($oDireccion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_direccion', $aDatos);
        $this->assertEquals($id, $aDatos['id_direccion']);

        // Limpiar
        $oDireccionParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oDireccionParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_direccion()
    {
        // Crear y guardar instancia usando factory
        $oDireccion = $this->factory->createSimple();
        $id = $oDireccion->getId_direccion();
        $this->repository->Guardar($oDireccion);

        // Verificar que existe
        $oDireccionExiste = $this->repository->findById($id);
        $this->assertNotNull($oDireccionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oDireccionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oDireccionEliminado = $this->repository->findById($id);
        $this->assertNull($oDireccionEliminado);
    }

    public function test_get_array_poblaciones_sin_filtros()
    {
        $result = $this->repository->getArrayPoblaciones();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_array_paises_sin_filtros()
    {
        $result = $this->repository->getArrayPaises();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_direcciones_sin_filtros()
    {
        $result = $this->repository->getDirecciones();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
