<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\entity\ProcesoTipo;
use Tests\myTest;
use Tests\factories\procesos\ProcesoTipoFactory;

class PgProcesoTipoRepositoryTest extends myTest
{
    private ProcesoTipoRepositoryInterface $repository;
    private ProcesoTipoFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $this->factory = new ProcesoTipoFactory();
    }

    public function test_guardar_nuevo_procesoTipo()
    {
        // Crear instancia usando factory
        $oProcesoTipo = $this->factory->createSimple();
        $id = $oProcesoTipo->getId_tipo_proceso();

        // Guardar
        $result = $this->repository->Guardar($oProcesoTipo);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oProcesoTipoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oProcesoTipoGuardado);
        $this->assertEquals($id, $oProcesoTipoGuardado->getId_tipo_proceso());

        // Limpiar
        $this->repository->Eliminar($oProcesoTipoGuardado);
    }

    public function test_actualizar_procesoTipo_existente()
    {
        // Crear y guardar instancia usando factory
        $oProcesoTipo = $this->factory->createSimple();
        $id = $oProcesoTipo->getId_tipo_proceso();
        $this->repository->Guardar($oProcesoTipo);

        // Crear otra instancia con datos diferentes para actualizar
        $oProcesoTipoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oProcesoTipoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oProcesoTipoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oProcesoTipoActualizado);

        // Limpiar
        $this->repository->Eliminar($oProcesoTipoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProcesoTipo = $this->factory->createSimple();
        $id = $oProcesoTipo->getId_tipo_proceso();
        $this->repository->Guardar($oProcesoTipo);

        // Buscar por ID
        $oProcesoTipoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oProcesoTipoEncontrado);
        $this->assertInstanceOf(ProcesoTipo::class, $oProcesoTipoEncontrado);
        $this->assertEquals($id, $oProcesoTipoEncontrado->getId_tipo_proceso());

        // Limpiar
        $this->repository->Eliminar($oProcesoTipoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oProcesoTipo = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oProcesoTipo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oProcesoTipo = $this->factory->createSimple();
        $id = $oProcesoTipo->getId_tipo_proceso();
        $this->repository->Guardar($oProcesoTipo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_tipo_proceso', $aDatos);
        $this->assertEquals($id, $aDatos['id_tipo_proceso']);

        // Limpiar
        $oProcesoTipoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oProcesoTipoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_procesoTipo()
    {
        // Crear y guardar instancia usando factory
        $oProcesoTipo = $this->factory->createSimple();
        $id = $oProcesoTipo->getId_tipo_proceso();
        $this->repository->Guardar($oProcesoTipo);

        // Verificar que existe
        $oProcesoTipoExiste = $this->repository->findById($id);
        $this->assertNotNull($oProcesoTipoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oProcesoTipoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oProcesoTipoEliminado = $this->repository->findById($id);
        $this->assertNull($oProcesoTipoEliminado);
    }

    public function test_get_array_proceso_tipos_sin_filtros()
    {
        $result = $this->repository->getArrayProcesoTipos();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_proceso_tipos_sin_filtros()
    {
        $result = $this->repository->getProcesoTipos();
        
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
