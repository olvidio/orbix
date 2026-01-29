<?php

namespace Tests\integration\menus\infrastructure\repositories;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use Tests\myTest;
use Tests\factories\menus\GrupMenuFactory;

class PgGrupMenuRepositoryTest extends myTest
{
    private GrupMenuRepositoryInterface $repository;
    private GrupMenuFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
        $this->factory = new GrupMenuFactory();
    }

    public function test_guardar_nuevo_grupMenu()
    {
        // Crear instancia usando factory
        $oGrupMenu = $this->factory->createSimple();
        $id = $oGrupMenu->getId_grupmenu();

        // Guardar
        $result = $this->repository->Guardar($oGrupMenu);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oGrupMenuGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuGuardado);
        $this->assertEquals($id, $oGrupMenuGuardado->getId_grupmenu());

        // Limpiar
        $this->repository->Eliminar($oGrupMenuGuardado);
    }

    public function test_actualizar_grupMenu_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenu = $this->factory->createSimple();
        $id = $oGrupMenu->getId_grupmenu();
        $this->repository->Guardar($oGrupMenu);

        // Crear otra instancia con datos diferentes para actualizar
        $oGrupMenuUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oGrupMenuUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oGrupMenuActualizado = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuActualizado);

        // Limpiar
        $this->repository->Eliminar($oGrupMenuActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenu = $this->factory->createSimple();
        $id = $oGrupMenu->getId_grupmenu();
        $this->repository->Guardar($oGrupMenu);

        // Buscar por ID
        $oGrupMenuEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuEncontrado);
        $this->assertInstanceOf(GrupMenu::class, $oGrupMenuEncontrado);
        $this->assertEquals($id, $oGrupMenuEncontrado->getId_grupmenu());

        // Limpiar
        $this->repository->Eliminar($oGrupMenuEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oGrupMenu = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oGrupMenu);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenu = $this->factory->createSimple();
        $id = $oGrupMenu->getId_grupmenu();
        $this->repository->Guardar($oGrupMenu);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_grupmenu', $aDatos);
        $this->assertEquals($id, $aDatos['id_grupmenu']);

        // Limpiar
        $oGrupMenuParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oGrupMenuParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_grupMenu()
    {
        // Crear y guardar instancia usando factory
        $oGrupMenu = $this->factory->createSimple();
        $id = $oGrupMenu->getId_grupmenu();
        $this->repository->Guardar($oGrupMenu);

        // Verificar que existe
        $oGrupMenuExiste = $this->repository->findById($id);
        $this->assertNotNull($oGrupMenuExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oGrupMenuExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oGrupMenuEliminado = $this->repository->findById($id);
        $this->assertNull($oGrupMenuEliminado);
    }

    public function test_get_array_grup_menus_sin_filtros()
    {
        $result = $this->repository->getArrayGrupMenus();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_grup_menus_sin_filtros()
    {
        $result = $this->repository->getGrupMenus();
        
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
