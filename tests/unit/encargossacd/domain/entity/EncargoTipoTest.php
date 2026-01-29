<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoTipo;
use src\encargossacd\domain\value_objects\EncargoModHorarioId;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\EncargoTipoText;
use Tests\myTest;

class EncargoTipoTest extends myTest
{
    private EncargoTipo $EncargoTipo;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoTipo = new EncargoTipo();
        $this->EncargoTipo->setId_tipo_enc(1);
        $this->EncargoTipo->setTipoEncVo(new EncargoTipoText('Test'));
    }

    public function test_set_and_get_id_tipo_enc()
    {
        $this->EncargoTipo->setId_tipo_enc(1);
        $this->assertEquals(1, $this->EncargoTipo->getId_tipo_enc());
    }

    public function test_set_and_get_tipo_enc()
    {
        $tipo_encVo = new EncargoTipoText('Test');
        $this->EncargoTipo->setTipoEncVo($tipo_encVo);
        $this->assertInstanceOf(EncargoTipoText::class, $this->EncargoTipo->getTipoEncVo());
        $this->assertEquals('Test', $this->EncargoTipo->getTipoEncVo()->value());
    }

    public function test_set_and_get_mod_horario()
    {
        $mod_horarioVo = new EncargoModHorarioId(1);
        $this->EncargoTipo->setModHorarioVo($mod_horarioVo);
        $this->assertInstanceOf(EncargoModHorarioId::class, $this->EncargoTipo->getModHorarioVo());
        $this->assertEquals(1, $this->EncargoTipo->getModHorarioVo()->value());
    }

    public function test_set_all_attributes()
    {
        $encargoTipo = new EncargoTipo();
        $attributes = [
            'id_tipo_enc' => 1,
            'tipo_enc' => new EncargoTipoText('Test'),
            'mod_horario' => new EncargoModHorarioId(1),
        ];
        $encargoTipo->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoTipo->getId_tipo_enc());
        $this->assertEquals('Test', $encargoTipo->getTipoEncVo()->value());
        $this->assertEquals(1, $encargoTipo->getModHorarioVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoTipo = new EncargoTipo();
        $attributes = [
            'id_tipo_enc' => 1,
            'tipo_enc' => 'Test',
            'mod_horario' => 1,
        ];
        $encargoTipo->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoTipo->getId_tipo_enc());
        $this->assertEquals('Test', $encargoTipo->getTipoEncVo()->value());
        $this->assertEquals(1, $encargoTipo->getModHorarioVo()->value());
    }
}
