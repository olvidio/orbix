<?php

namespace Tests\integration\menus\infrastructure\repositories;

use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\menus\domain\entity\TemplateMenu;
use Tests\myTest;
use Tests\factories\menus\TemplateMenuFactory;

class PgTemplateMenuRepositoryTest extends myTest
{
    private TemplateMenuRepositoryInterface $repository;
    private TemplateMenuFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(TemplateMenuRepositoryInterface::class);
        $this->factory = new TemplateMenuFactory();
    }

    public function test_guardar_nuevo_templateMenu()
    {
        // Crear instancia usando factory
        $oTemplateMenu = $this->factory->createSimple();
        $id = $oTemplateMenu->getId_template_menu();

        // Guardar
        $result = $this->repository->Guardar($oTemplateMenu);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oTemplateMenuGuardado = $this->repository->findById($id);
        $this->assertNotNull($oTemplateMenuGuardado);
        $this->assertEquals($id, $oTemplateMenuGuardado->getId_template_menu());

        // Limpiar
        $this->repository->Eliminar($oTemplateMenuGuardado);
    }

    public function test_actualizar_templateMenu_existente()
    {
        // Crear y guardar instancia usando factory
        $oTemplateMenu = $this->factory->createSimple();
        $id = $oTemplateMenu->getId_template_menu();
        $this->repository->Guardar($oTemplateMenu);

        // Crear otra instancia con datos diferentes para actualizar
        $oTemplateMenuUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oTemplateMenuUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oTemplateMenuActualizado = $this->repository->findById($id);
        $this->assertNotNull($oTemplateMenuActualizado);

        // Limpiar
        $this->repository->Eliminar($oTemplateMenuActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTemplateMenu = $this->factory->createSimple();
        $id = $oTemplateMenu->getId_template_menu();
        $this->repository->Guardar($oTemplateMenu);

        // Buscar por ID
        $oTemplateMenuEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oTemplateMenuEncontrado);
        $this->assertInstanceOf(TemplateMenu::class, $oTemplateMenuEncontrado);
        $this->assertEquals($id, $oTemplateMenuEncontrado->getId_template_menu());

        // Limpiar
        $this->repository->Eliminar($oTemplateMenuEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oTemplateMenu = $this->repository->findById($id_inexistente);
        
        $this->assertNull($oTemplateMenu);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oTemplateMenu = $this->factory->createSimple();
        $id = $oTemplateMenu->getId_template_menu();
        $this->repository->Guardar($oTemplateMenu);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_template_menu', $aDatos);
        $this->assertEquals($id, $aDatos['id_template_menu']);

        // Limpiar
        $oTemplateMenuParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oTemplateMenuParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);
        
        $this->assertFalse($aDatos);
    }

    public function test_eliminar_templateMenu()
    {
        // Crear y guardar instancia usando factory
        $oTemplateMenu = $this->factory->createSimple();
        $id = $oTemplateMenu->getId_template_menu();
        $this->repository->Guardar($oTemplateMenu);

        // Verificar que existe
        $oTemplateMenuExiste = $this->repository->findById($id);
        $this->assertNotNull($oTemplateMenuExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oTemplateMenuExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oTemplateMenuEliminado = $this->repository->findById($id);
        $this->assertNull($oTemplateMenuEliminado);
    }

    public function test_get_array_templates_sin_filtros()
    {
        $result = $this->repository->getArrayTemplates();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    public function test_get_templates_menus_sin_filtros()
    {
        $result = $this->repository->getTemplatesMenus();
        
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
