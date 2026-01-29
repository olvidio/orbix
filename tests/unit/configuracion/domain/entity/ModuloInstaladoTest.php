<?php

namespace Tests\unit\configuracion\domain\entity;

use src\configuracion\domain\entity\ModuloInstalado;
use src\configuracion\domain\value_objects\ModuloId;
use Tests\myTest;

class ModuloInstaladoTest extends myTest
{
    private ModuloInstalado $ModuloInstalado;

    public function setUp(): void
    {
        parent::setUp();
        $this->ModuloInstalado = new ModuloInstalado();
        $this->ModuloInstalado->setIdModVo(new ModuloId(1));
    }

    public function test_set_and_get_id_mod()
    {
        $id_modVo = new ModuloId(1);
        $this->ModuloInstalado->setIdModVo($id_modVo);
        $this->assertInstanceOf(ModuloId::class, $this->ModuloInstalado->getIdModVo());
        $this->assertEquals(1, $this->ModuloInstalado->getIdModVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->ModuloInstalado->setActive(true);
        $this->assertTrue($this->ModuloInstalado->isActive());
    }

    public function test_set_all_attributes()
    {
        $moduloInstalado = new ModuloInstalado();
        $attributes = [
            'id_mod' => new ModuloId(1),
            'active' => true,
        ];
        $moduloInstalado->setAllAttributes($attributes);

        $this->assertEquals(1, $moduloInstalado->getIdModVo()->value());
        $this->assertTrue($moduloInstalado->isActive());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $moduloInstalado = new ModuloInstalado();
        $attributes = [
            'id_mod' => 1,
            'active' => true,
        ];
        $moduloInstalado->setAllAttributes($attributes);

        $this->assertEquals(1, $moduloInstalado->getIdModVo()->value());
        $this->assertTrue($moduloInstalado->isActive());
    }
}
