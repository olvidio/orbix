<?php

namespace Tests\unit\procesos\domain\entity;

use src\procesos\domain\entity\ProcesoTipo;
use src\procesos\domain\value_objects\ProcesoTipoId;
use Tests\myTest;

class ProcesoTipoTest extends myTest
{
    private ProcesoTipo $ProcesoTipo;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProcesoTipo = new ProcesoTipo();
        $this->ProcesoTipo->setIdTipoProcesoVo(new ProcesoTipoId(1));
    }

    public function test_set_and_get_id_tipo_proceso()
    {
        $id_tipo_procesoVo = new ProcesoTipoId(1);
        $this->ProcesoTipo->setIdTipoProcesoVo($id_tipo_procesoVo);
        $this->assertInstanceOf(ProcesoTipoId::class, $this->ProcesoTipo->getIdTipoProcesoVo());
        $this->assertEquals(1, $this->ProcesoTipo->getIdTipoProcesoVo()->value());
    }

    public function test_set_and_get_nom_proceso()
    {
        $this->ProcesoTipo->setNom_proceso('test');
        $this->assertEquals('test', $this->ProcesoTipo->getNom_proceso());
    }

    public function test_set_and_get_sfsv()
    {
        $this->ProcesoTipo->setSfsv(1);
        $this->assertEquals(1, $this->ProcesoTipo->getSfsv());
    }

    public function test_set_all_attributes()
    {
        $procesoTipo = new ProcesoTipo();
        $attributes = [
            'id_tipo_proceso' => new ProcesoTipoId(1),
            'nom_proceso' => 'test',
            'sfsv' => 1,
        ];
        $procesoTipo->setAllAttributes($attributes);

        $this->assertEquals(1, $procesoTipo->getIdTipoProcesoVo()->value());
        $this->assertEquals('test', $procesoTipo->getNom_proceso());
        $this->assertEquals(1, $procesoTipo->getSfsv());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $procesoTipo = new ProcesoTipo();
        $attributes = [
            'id_tipo_proceso' => 1,
            'nom_proceso' => 'test',
            'sfsv' => 1,
        ];
        $procesoTipo->setAllAttributes($attributes);

        $this->assertEquals(1, $procesoTipo->getIdTipoProcesoVo()->value());
        $this->assertEquals('test', $procesoTipo->getNom_proceso());
        $this->assertEquals(1, $procesoTipo->getSfsv());
    }
}
