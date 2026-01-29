<?php

namespace Tests\integration\actividades\infrastructure\repositories;

use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\Repeticion;
use src\actividades\domain\value_objects\RepeticionId;
use Tests\myTest;
use Tests\factories\actividades\RepeticionFactory;

class PgRepeticionRepositoryTest extends myTest
{
    private RepeticionRepositoryInterface $repository;
    private RepeticionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
        $this->factory = new RepeticionFactory();
    }

    public function test_guardar_nuevo_repeticion()
    {
        // Crear instancia usando factory
        $oRepeticion = $this->factory->createSimple();
        $id = $oRepeticion->getId_repeticionVo();

        // Guardar
        $result = $this->repository->Guardar($oRepeticion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oRepeticionGuardado = $this->repository->findById($id);
        $this->assertNotNull($oRepeticionGuardado);
        $this->assertEquals($id, $oRepeticionGuardado->getId_repeticionVo());

        // Limpiar
        $this->repository->Eliminar($oRepeticionGuardado);
    }

    public function test_actualizar_repeticion_existente()
    {
        // Crear y guardar instancia usando factory
        $oRepeticion = $this->factory->createSimple();
        $id = $oRepeticion->getId_repeticionVo();
        $this->repository->Guardar($oRepeticion);

        // Crear otra instancia con datos diferentes para actualizar
        $oRepeticionUpdated = $this->factory->createSimple($id->value());

        // Actualizar
        $result = $this->repository->Guardar($oRepeticionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oRepeticionActualizado = $this->repository->findById($id);
        $this->assertNotNull($oRepeticionActualizado);

        // Limpiar
        $this->repository->Eliminar($oRepeticionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oRepeticion = $this->factory->createSimple();
        $id = $oRepeticion->getId_repeticionVo();
        $this->repository->Guardar($oRepeticion);

        // Buscar por ID
        $oRepeticionEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oRepeticionEncontrado);
        $this->assertInstanceOf(Repeticion::class, $oRepeticionEncontrado);
        $this->assertEquals($id, $oRepeticionEncontrado->getId_repeticionVo());

        // Limpiar
        $this->repository->Eliminar($oRepeticionEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oRepeticion = $this->repository->findById(RepeticionId::fromNullableInt($id_inexistente));
        
        $this->assertNull($oRepeticion);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oRepeticion = $this->factory->createSimple();
        $id = $oRepeticion->getId_repeticionVo();
        $this->repository->Guardar($oRepeticion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_repeticion', $aDatos);
        $this->assertEquals($id->value(), $aDatos['id_repeticion']);

        // Limpiar
        $oRepeticionParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oRepeticionParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById(RepeticionId::fromNullableInt($id_inexistente));
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_repeticion()
    {
        // Crear y guardar instancia usando factory
        $oRepeticion = $this->factory->createSimple();
        $id = $oRepeticion->getId_repeticionVo();
        $this->repository->Guardar($oRepeticion);

        // Verificar que existe
        $oRepeticionExiste = $this->repository->findById($id);
        $this->assertNotNull($oRepeticionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oRepeticionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oRepeticionEliminado = $this->repository->findById($id);
        $this->assertNull($oRepeticionEliminado);
    }

    public function test_get_array_repeticion_sin_filtros()
    {
        $result = $this->repository->getArrayRepeticion();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_repeticiones_sin_filtros()
    {
        $result = $this->repository->getRepeticiones();
        
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
