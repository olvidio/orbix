<?php

namespace Tests\unit\ubiscamas\domain\entity;

use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;
use Tests\myTest;

class CamaTest extends myTest
{
    private Cama $cama;

    public function setUp(): void
    {
        parent::setUp();
        $this->cama = new Cama();
        $this->cama->setIdCamaVo(new CamaId('cama-001'));
        $this->cama->setIdHabitacionVo(new HabitacionId('hab-001'));
        $this->cama->setDescripcionVo(new CamaDescripcion('Cama individual'));
    }

    public function test_set_and_get_id_cama()
    {
        $id = new CamaId('cama-002');
        $this->cama->setIdCamaVo($id);
        $this->assertInstanceOf(CamaId::class, $this->cama->getIdCamaVo());
        $this->assertEquals('cama-002', $this->cama->getIdCamaVo()->value());
    }

    public function test_set_and_get_id_habitacion()
    {
        $id = new HabitacionId('hab-002');
        $this->cama->setIdHabitacionVo($id);
        $this->assertInstanceOf(HabitacionId::class, $this->cama->getIdHabitacionVo());
        $this->assertEquals('hab-002', $this->cama->getIdHabitacionVo()->value());
    }

    public function test_set_and_get_descripcion()
    {
        $desc = new CamaDescripcion('Cama doble');
        $this->cama->setDescripcionVo($desc);
        $this->assertInstanceOf(CamaDescripcion::class, $this->cama->getDescripcionVo());
        $this->assertEquals('Cama doble', $this->cama->getDescripcionVo()->value());
    }

    public function test_set_and_get_descripcion_from_string()
    {
        $this->cama->setDescripcionVo('Cama doble');
        $this->assertEquals('Cama doble', $this->cama->getDescripcionVo()->value());
    }

    public function test_set_and_get_larga()
    {
        $this->cama->setLarga(true);
        $this->assertTrue($this->cama->isLarga());

        $this->cama->setLarga(false);
        $this->assertFalse($this->cama->isLarga());
    }

    public function test_larga_is_nullable()
    {
        $this->cama->setLarga(null);
        $this->assertNull($this->cama->isLarga());
    }

    public function test_set_and_get_vip()
    {
        $this->cama->setVip(true);
        $this->assertTrue($this->cama->isVip());
    }

    public function test_vip_is_nullable()
    {
        $this->cama->setVip(null);
        $this->assertNull($this->cama->isVip());
    }

    public function test_set_and_get_id_schema()
    {
        $this->cama->setIdSchema(3);
        $this->assertEquals(3, $this->cama->getIdSchema());
    }
}
