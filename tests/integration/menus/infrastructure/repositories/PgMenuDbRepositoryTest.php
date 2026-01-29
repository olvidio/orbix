<?php

namespace Tests\integration\menus\infrastructure\repositories;

use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use Tests\myTest;
use Tests\factories\menus\MenuDbFactory;

class PgMenuDbRepositoryTest extends myTest
{
    private MenuDbRepositoryInterface $repository;
    private MenuDbFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MenuDbRepositoryInterface::class);
        $this->factory = new MenuDbFactory();
    }

    public function test_guardar_nuevo_menuDb()
    {
        // Crear instancia usando factory
        $oMenuDb = $this->factory->createSimple();
        $id = $oMenuDb->getId_menu();

        // Guardar
        $result = $this->repository->Guardar($oMenuDb);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oMenuDbGuardado = $this->repository->findById($id);
        $this->assertNotNull($oMenuDbGuardado);
        $this->assertEquals($id, $oMenuDbGuardado->getId_menu());

        // Limpiar
        $this->repository->Eliminar($oMenuDbGuardado);
    }

    public function test_actualizar_menuDb_existente()
    {
        // Crear y guardar instancia usando factory
        $oMenuDb = $this->factory->createSimple();
        $id = $oMenuDb->getId_menu();
        $this->repository->Guardar($oMenuDb);

        // Crear otra instancia con datos diferentes para actualizar
        $oMenuDbUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oMenuDbUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oMenuDbActualizado = $this->repository->findById($id);
        $this->assertNotNull($oMenuDbActualizado);

        // Limpiar
        $this->repository->Eliminar($oMenuDbActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oMenuDb = $this->factory->createSimple();
        $id = $oMenuDb->getId_menu();
        $this->repository->Guardar($oMenuDb);

        // Buscar por ID
        $oMenuDbEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oMenuDbEncontrado);
        $this->assertInstanceOf(MenuDb::class, $oMenuDbEncontrado);
        $this->assertEquals($id, $oMenuDbEncontrado->getId_menu());

        // Limpiar
        $this->repository->Eliminar($oMenuDbEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oMenuDb = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oMenuDb);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oMenuDb = $this->factory->createSimple();
        $id = $oMenuDb->getId_menu();
        $this->repository->Guardar($oMenuDb);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_menu', $aDatos);
        $this->assertEquals($id, $aDatos['id_menu']);

        // Limpiar
        $oMenuDbParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oMenuDbParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_menuDb()
    {
        // Crear y guardar instancia usando factory
        $oMenuDb = $this->factory->createSimple();
        $id = $oMenuDb->getId_menu();
        $this->repository->Guardar($oMenuDb);

        // Verificar que existe
        $oMenuDbExiste = $this->repository->findById($id);
        $this->assertNotNull($oMenuDbExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oMenuDbExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oMenuDbEliminado = $this->repository->findById($id);
        $this->assertNull($oMenuDbEliminado);
    }

    public function test_get_menu_dbs_sin_filtros()
    {
        $result = $this->repository->getMenuDbs();
        
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
