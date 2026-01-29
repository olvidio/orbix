<?php

namespace Tests\unit\zonassacd\domain\entity;

use src\zonassacd\domain\entity\ZonaGrupo;
use src\zonassacd\domain\value_objects\NombreGrupoZona;
use Tests\myTest;

class ZonaGrupoTest extends myTest
{
    private ZonaGrupo $ZonaGrupo;

    public function setUp(): void
    {
        parent::setUp();
        $this->ZonaGrupo = new ZonaGrupo();
        $this->ZonaGrupo->setId_grupo(1);
    }

    public function test_set_and_get_id_grupo()
    {
        $this->ZonaGrupo->setId_grupo(1);
        $this->assertEquals(1, $this->ZonaGrupo->getId_grupo());
    }

    public function test_set_and_get_nombre_grupo()
    {
        $nombre_grupoVo = new NombreGrupoZona('test value');
        $this->ZonaGrupo->setNombreGrupoVo($nombre_grupoVo);
        $this->assertInstanceOf(NombreGrupoZona::class, $this->ZonaGrupo->getNombreGrupoVo());
        $this->assertEquals('test value', $this->ZonaGrupo->getNombreGrupoVo()->value());
    }

    public function test_set_and_get_orden()
    {
        $this->ZonaGrupo->setOrden(1);
        $this->assertEquals(1, $this->ZonaGrupo->getOrden());
    }

    public function test_set_all_attributes()
    {
        $zonaGrupo = new ZonaGrupo();
        $attributes = [
            'id_grupo' => 1,
            'nombre_grupo' => new NombreGrupoZona('test value'),
            'orden' => 1,
        ];
        $zonaGrupo->setAllAttributes($attributes);

        $this->assertEquals(1, $zonaGrupo->getId_grupo());
        $this->assertEquals('test value', $zonaGrupo->getNombreGrupoVo()->value());
        $this->assertEquals(1, $zonaGrupo->getOrden());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $zonaGrupo = new ZonaGrupo();
        $attributes = [
            'id_grupo' => 1,
            'nombre_grupo' => 'test value',
            'orden' => 1,
        ];
        $zonaGrupo->setAllAttributes($attributes);

        $this->assertEquals(1, $zonaGrupo->getId_grupo());
        $this->assertEquals('test value', $zonaGrupo->getNombreGrupoVo()->value());
        $this->assertEquals(1, $zonaGrupo->getOrden());
    }
}
