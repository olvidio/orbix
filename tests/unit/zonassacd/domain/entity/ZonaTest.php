<?php

namespace Tests\unit\zonassacd\domain\entity;

use src\zonassacd\domain\entity\Zona;
use src\zonassacd\domain\value_objects\NombreZona;
use Tests\myTest;

class ZonaTest extends myTest
{
    private Zona $Zona;

    public function setUp(): void
    {
        parent::setUp();
        $this->Zona = new Zona();
        $this->Zona->setId_zona(1);
        $this->Zona->setNombreZonaVo(new NombreZona('test value'));
    }

    public function test_get_id_zona()
    {
        $this->assertEquals(1, $this->Zona->getId_zona());
    }

    public function test_set_and_get_nombre_zona()
    {
        $nombre_zonaVo = new NombreZona('test value');
        $this->Zona->setNombreZonaVo($nombre_zonaVo);
        $this->assertInstanceOf(NombreZona::class, $this->Zona->getNombreZonaVo());
        $this->assertEquals('test value', $this->Zona->getNombreZonaVo()->value());
    }

    public function test_set_and_get_orden()
    {
        $this->Zona->setOrden(1);
        $this->assertEquals(1, $this->Zona->getOrden());
    }

    public function test_set_and_get_id_grupo()
    {
        $this->Zona->setId_grupo(1);
        $this->assertEquals(1, $this->Zona->getId_grupo());
    }

    public function test_set_and_get_id_nom()
    {
        $this->Zona->setId_nom(1);
        $this->assertEquals(1, $this->Zona->getId_nom());
    }

    public function test_set_all_attributes()
    {
        $zona = new Zona();
        $attributes = [
            'id_zona' => 1,
            'nombre_zona' => new NombreZona('test value'),
            'orden' => 1,
            'id_grupo' => 1,
            'id_nom' => 1,
        ];
        $zona->setAllAttributes($attributes);

        $this->assertEquals(1, $zona->getId_zona());
        $this->assertEquals('test value', $zona->getNombreZonaVo()->value());
        $this->assertEquals(1, $zona->getOrden());
        $this->assertEquals(1, $zona->getId_grupo());
        $this->assertEquals(1, $zona->getId_nom());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $zona = new Zona();
        $attributes = [
            'id_zona' => 1,
            'nombre_zona' => 'test value',
            'orden' => 1,
            'id_grupo' => 1,
            'id_nom' => 1,
        ];
        $zona->setAllAttributes($attributes);

        $this->assertEquals(1, $zona->getId_zona());
        $this->assertEquals('test value', $zona->getNombreZonaVo()->value());
        $this->assertEquals(1, $zona->getOrden());
        $this->assertEquals(1, $zona->getId_grupo());
        $this->assertEquals(1, $zona->getId_nom());
    }
}
