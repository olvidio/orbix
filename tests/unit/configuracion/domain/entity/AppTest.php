<?php

namespace Tests\unit\configuracion\domain\entity;

use src\configuracion\domain\entity\App;
use src\configuracion\domain\value_objects\AppId;
use src\configuracion\domain\value_objects\AppName;
use Tests\myTest;

class AppTest extends myTest
{
    private App $App;

    public function setUp(): void
    {
        parent::setUp();
        $this->App = new App();
        $this->App->setId_app(1);
        $this->App->setNomVo(new AppName('Test value'));
    }

    public function test_get_id_app()
    {
        $this->assertEquals(1, $this->App->getId_app());
    }

    public function test_set_and_get_nombre_app()
    {
        $nombre_appVo = new AppName('Test value');
        $this->App->setNomVo($nombre_appVo);
        $this->assertInstanceOf(AppName::class, $this->App->getNomVo());
        $this->assertEquals('Test value', $this->App->getNomVo()->value());
    }

    public function test_set_all_attributes()
    {
        $app = new App();
        $attributes = [
            'id_app' => new AppId(1),
            'nombre_app' => new AppName('Test value'),
        ];
        $app->setAllAttributes($attributes);

        $this->assertEquals(1, $app->getIdAppVo()->value());
        $this->assertEquals('Test value', $app->getNomVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $app = new App();
        $attributes = [
            'id_app' => 1,
            'nombre_app' => 'Test value',
        ];
        $app->setAllAttributes($attributes);

        $this->assertEquals(1, $app->getIdAppVo()->value());
        $this->assertEquals('Test value', $app->getNomVo()->value());
    }
}
