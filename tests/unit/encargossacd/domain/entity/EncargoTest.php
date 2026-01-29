<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use Tests\myTest;

class EncargoTest extends myTest
{
    private Encargo $Encargo;

    public function setUp(): void
    {
        parent::setUp();
        $this->Encargo = new Encargo();
        $this->Encargo->setId_enc(1);
        $this->Encargo->setId_tipo_enc(1);
    }

    public function test_set_and_get_id_enc()
    {
        $this->Encargo->setId_enc(1);
        $this->assertEquals(1, $this->Encargo->getId_enc());
    }

    public function test_set_and_get_id_tipo_enc()
    {
        $this->Encargo->setId_tipo_enc(1);
        $this->assertEquals(1, $this->Encargo->getId_tipo_enc());
    }

    public function test_set_and_get_grupo_encargo()
    {
        $grupo_encargoVo = new EncargoGrupo(1);
        $this->Encargo->setGrupoEncargoVo($grupo_encargoVo);
        $this->assertInstanceOf(EncargoGrupo::class, $this->Encargo->getGrupoEncargoVo());
        $this->assertEquals(1, $this->Encargo->getGrupoEncargoVo()->value());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->Encargo->setId_ubi(1);
        $this->assertEquals(1, $this->Encargo->getId_ubi());
    }

    public function test_set_and_get_id_zona()
    {
        $this->Encargo->setId_zona(1);
        $this->assertEquals(1, $this->Encargo->getId_zona());
    }

    public function test_set_all_attributes()
    {
        $encargo = new Encargo();
        $attributes = [
            'id_enc' => 1,
            'id_tipo_enc' => 1,
            'grupo_encargo' => new EncargoGrupo(1),
            'id_ubi' => 1,
            'id_zona' => 1,
        ];
        $encargo->setAllAttributes($attributes);

        $this->assertEquals(1, $encargo->getId_enc());
        $this->assertEquals(1, $encargo->getId_tipo_enc());
        $this->assertEquals(1, $encargo->getGrupoEncargoVo()->value());
        $this->assertEquals(1, $encargo->getId_ubi());
        $this->assertEquals(1, $encargo->getId_zona());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargo = new Encargo();
        $attributes = [
            'id_enc' => 1,
            'id_tipo_enc' => 1,
            'grupo_encargo' => 1,
            'id_ubi' => 1,
            'id_zona' => 1,
        ];
        $encargo->setAllAttributes($attributes);

        $this->assertEquals(1, $encargo->getId_enc());
        $this->assertEquals(1, $encargo->getId_tipo_enc());
        $this->assertEquals(1, $encargo->getGrupoEncargoVo()->value());
        $this->assertEquals(1, $encargo->getId_ubi());
        $this->assertEquals(1, $encargo->getId_zona());
    }
}
