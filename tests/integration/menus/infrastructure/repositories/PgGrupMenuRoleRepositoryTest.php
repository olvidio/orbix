<?php

namespace Tests\integration\menus\infrastructure\repositories;

use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\menus\domain\entity\GrupMenuRole;
use Tests\myTest;
use Tests\factories\menus\GrupMenuRoleFactory;

class PgGrupMenuRoleRepositoryTest extends myTest
{
    private GrupMenuRoleRepositoryInterface $repository;
    private GrupMenuRoleFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(GrupMenuRoleRepositoryInterface::class);
        $this->factory = new GrupMenuRoleFactory();
    }

    public function test_guardar_nuevo_grupMenuRole()
    {
        // Crear instancia usando factory
        $oGrupMenuRole = $this->factory->createSimple();
        $id = $oGrupMenuRole->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oGrupMenuRole);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oGrupMenuRoleGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuRoleGuardado);
        $this->assertEquals($id, $oGrupMenuRoleGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oGrupMenuRoleGuardado);
    }

    public function test_actualizar_grupMenuRole_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenuRole = $this->factory->createSimple();
        $id = $oGrupMenuRole->getId_item();
        $this->repository->Guardar($oGrupMenuRole);

        // Crear otra instancia con datos diferentes para actualizar
        $oGrupMenuRoleUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oGrupMenuRoleUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oGrupMenuRoleActualizado = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuRoleActualizado);

        // Limpiar
        $this->repository->Eliminar($oGrupMenuRoleActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenuRole = $this->factory->createSimple();
        $id = $oGrupMenuRole->getId_item();
        $this->repository->Guardar($oGrupMenuRole);

        // Buscar por ID
        $oGrupMenuRoleEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuRoleEncontrado);
        $this->assertInstanceOf(GrupMenuRole::class, $oGrupMenuRoleEncontrado);
        $this->assertEquals($id, $oGrupMenuRoleEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oGrupMenuRoleEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oGrupMenuRole = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oGrupMenuRole);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenuRole = $this->factory->createSimple();
        $id = $oGrupMenuRole->getId_item();
        $this->repository->Guardar($oGrupMenuRole);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oGrupMenuRoleParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oGrupMenuRoleParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_grupMenuRole()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenuRole = $this->factory->createSimple();
        $id = $oGrupMenuRole->getId_item();
        $this->repository->Guardar($oGrupMenuRole);

        // Verificar que existe
        $oGrupMenuRoleExiste = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuRoleExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oGrupMenuRoleExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oGrupMenuRoleEliminado = $this->repository->findById($id);
        $this->assertNull($oGrupMenuRoleEliminado);
    }

    public function test_get_grup_menu_roles_sin_filtros()
    {
        $result = $this->repository->getGrupMenuRoles();
        
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
