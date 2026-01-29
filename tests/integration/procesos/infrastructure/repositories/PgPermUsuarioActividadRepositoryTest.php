<?php

namespace Tests\integration\procesos\infrastructure\repositories;

use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\entity\PermUsuarioActividad;
use Tests\myTest;
use Tests\factories\procesos\PermUsuarioActividadFactory;

class PgPermUsuarioActividadRepositoryTest extends myTest
{
    private PermUsuarioActividadRepositoryInterface $repository;
    private PermUsuarioActividadFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
        $this->factory = new PermUsuarioActividadFactory();
    }

    public function test_guardar_nuevo_permUsuarioActividad()
    {
        // Crear instancia usando factory
        $oPermUsuarioActividad = $this->factory->createSimple();
        $id = $oPermUsuarioActividad->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oPermUsuarioActividad);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oPermUsuarioActividadGuardado = $this->repository->findById($id);
        $this->assertNotNull($oPermUsuarioActividadGuardado);
        $this->assertEquals($id, $oPermUsuarioActividadGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oPermUsuarioActividadGuardado);
    }

    public function test_actualizar_permUsuarioActividad_existente()
    {
        // Crear y guardar instancia usando factory
        $oPermUsuarioActividad = $this->factory->createSimple();
        $id = $oPermUsuarioActividad->getId_item();
        $this->repository->Guardar($oPermUsuarioActividad);

        // Crear otra instancia con datos diferentes para actualizar
        $oPermUsuarioActividadUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oPermUsuarioActividadUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oPermUsuarioActividadActualizado = $this->repository->findById($id);
        $this->assertNotNull($oPermUsuarioActividadActualizado);

        // Limpiar
        $this->repository->Eliminar($oPermUsuarioActividadActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPermUsuarioActividad = $this->factory->createSimple();
        $id = $oPermUsuarioActividad->getId_item();
        $this->repository->Guardar($oPermUsuarioActividad);

        // Buscar por ID
        $oPermUsuarioActividadEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oPermUsuarioActividadEncontrado);
        $this->assertInstanceOf(PermUsuarioActividad::class, $oPermUsuarioActividadEncontrado);
        $this->assertEquals($id, $oPermUsuarioActividadEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oPermUsuarioActividadEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPermUsuarioActividad = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oPermUsuarioActividad);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oPermUsuarioActividad = $this->factory->createSimple();
        $id = $oPermUsuarioActividad->getId_item();
        $this->repository->Guardar($oPermUsuarioActividad);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oPermUsuarioActividadParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oPermUsuarioActividadParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_permUsuarioActividad()
    {
        // Crear y guardar instancia usando factory
        $oPermUsuarioActividad = $this->factory->createSimple();
        $id = $oPermUsuarioActividad->getId_item();
        $this->repository->Guardar($oPermUsuarioActividad);

        // Verificar que existe
        $oPermUsuarioActividadExiste = $this->repository->findById($id);
        $this->assertNotNull($oPermUsuarioActividadExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPermUsuarioActividadExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPermUsuarioActividadEliminado = $this->repository->findById($id);
        $this->assertNull($oPermUsuarioActividadEliminado);
    }

    public function test_get_perm_usuario_actividades_sin_filtros()
    {
        $result = $this->repository->getPermUsuarioActividades();
        
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
