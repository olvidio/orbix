<?php

namespace Tests\integration\menus\infrastructure\repositories;

use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\entity\MetaMenu;
use Tests\myTest;
use Tests\factories\menus\MetaMenuFactory;

class PgMetaMenuRepositoryTest extends myTest
{
    private MetaMenuRepositoryInterface $repository;
    private MetaMenuFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MetaMenuRepositoryInterface::class);
        $this->factory = new MetaMenuFactory();
    }

    public function test_guardar_nuevo_metaMenu()
    {
        // Crear instancia usando factory
        $oMetaMenu = $this->factory->createSimple();
        $id = $oMetaMenu->getId_metamenu();

        // Guardar
        $result = $this->repository->Guardar($oMetaMenu);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oMetaMenuGuardado = $this->repository->findById($id);
        $this->assertNotNull($oMetaMenuGuardado);
        $this->assertEquals($id, $oMetaMenuGuardado->getId_metamenu());

        // Limpiar
        $this->repository->Eliminar($oMetaMenuGuardado);
    }

    public function test_actualizar_metaMenu_existente()
    {
        // Crear y guardar instancia usando factory
        $oMetaMenu = $this->factory->createSimple();
        $id = $oMetaMenu->getId_metamenu();
        $this->repository->Guardar($oMetaMenu);

        // Crear otra instancia con datos diferentes para actualizar
        $oMetaMenuUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oMetaMenuUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oMetaMenuActualizado = $this->repository->findById($id);
        $this->assertNotNull($oMetaMenuActualizado);

        // Limpiar
        $this->repository->Eliminar($oMetaMenuActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oMetaMenu = $this->factory->createSimple();
        $id = $oMetaMenu->getId_metamenu();
        $this->repository->Guardar($oMetaMenu);

        // Buscar por ID
        $oMetaMenuEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oMetaMenuEncontrado);
        $this->assertInstanceOf(MetaMenu::class, $oMetaMenuEncontrado);
        $this->assertEquals($id, $oMetaMenuEncontrado->getId_metamenu());

        // Limpiar
        $this->repository->Eliminar($oMetaMenuEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oMetaMenu = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oMetaMenu);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oMetaMenu = $this->factory->createSimple();
        $id = $oMetaMenu->getId_metamenu();
        $this->repository->Guardar($oMetaMenu);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_metamenu', $aDatos);
        $this->assertEquals($id, $aDatos['id_metamenu']);

        // Limpiar
        $oMetaMenuParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oMetaMenuParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_metaMenu()
    {
        // Crear y guardar instancia usando factory
        $oMetaMenu = $this->factory->createSimple();
        $id = $oMetaMenu->getId_metamenu();
        $this->repository->Guardar($oMetaMenu);

        // Verificar que existe
        $oMetaMenuExiste = $this->repository->findById($id);
        $this->assertNotNull($oMetaMenuExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oMetaMenuExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oMetaMenuEliminado = $this->repository->findById($id);
        $this->assertNull($oMetaMenuEliminado);
    }

    public function test_get_meta_menus_sin_filtros()
    {
        $result = $this->repository->getMetaMenus();
        
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
