<?php

namespace Tests\unit\menus\domain\entity;

use src\menus\domain\entity\TemplateMenu;
use src\menus\domain\value_objects\TemplateMenuName;
use Tests\myTest;

class TemplateMenuTest extends myTest
{
    private TemplateMenu $TemplateMenu;

    public function setUp(): void
    {
        parent::setUp();
        $this->TemplateMenu = new TemplateMenu();
        $this->TemplateMenu->setId_template_menu(1);
    }

    public function test_set_and_get_id_template_menu()
    {
        $this->TemplateMenu->setId_template_menu(1);
        $this->assertEquals(1, $this->TemplateMenu->getId_template_menu());
    }

    public function test_set_and_get_nombre()
    {
        $nombreVo = new TemplateMenuName('Test Name');
        $this->TemplateMenu->setNombreVo($nombreVo);
        $this->assertInstanceOf(TemplateMenuName::class, $this->TemplateMenu->getNombreVo());
        $this->assertEquals('Test Name', $this->TemplateMenu->getNombreVo()->value());
    }

    public function test_set_all_attributes()
    {
        $templateMenu = new TemplateMenu();
        $attributes = [
            'id_template_menu' => 1,
            'nombre' => new TemplateMenuName('Test Name'),
        ];
        $templateMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $templateMenu->getId_template_menu());
        $this->assertEquals('Test Name', $templateMenu->getNombreVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $templateMenu = new TemplateMenu();
        $attributes = [
            'id_template_menu' => 1,
            'nombre' => 'Test Name',
        ];
        $templateMenu->setAllAttributes($attributes);

        $this->assertEquals(1, $templateMenu->getId_template_menu());
        $this->assertEquals('Test Name', $templateMenu->getNombreVo()->value());
    }
}
