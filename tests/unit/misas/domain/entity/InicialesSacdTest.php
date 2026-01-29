<?php

namespace Tests\unit\misas\domain\entity;

use src\misas\domain\entity\InicialesSacd;
use Tests\myTest;

class InicialesSacdTest extends myTest
{
    private InicialesSacd $InicialesSacd;

    public function setUp(): void
    {
        parent::setUp();
        $this->InicialesSacd = new InicialesSacd();
    }

    public function test_set_and_get_id_nom()
    {
        $this->InicialesSacd->setId_nom(1);
        $this->assertEquals(1, $this->InicialesSacd->getId_nom());
    }

    public function test_set_and_get_iniciales()
    {
        $this->InicialesSacd->setIniciales('test');
        $this->assertEquals('test', $this->InicialesSacd->getIniciales());
    }

    public function test_set_and_get_color()
    {
        $this->InicialesSacd->setColor('test');
        $this->assertEquals('test', $this->InicialesSacd->getColor());
    }

    public function test_set_all_attributes()
    {
        $inicialesSacd = new InicialesSacd();
        $attributes = [
            'id_nom' => 1,
            'iniciales' => 'test',
            'color' => 'test',
        ];
        $inicialesSacd->setAllAttributes($attributes);

        $this->assertEquals(1, $inicialesSacd->getId_nom());
        $this->assertEquals('test', $inicialesSacd->getIniciales());
        $this->assertEquals('test', $inicialesSacd->getColor());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $inicialesSacd = new InicialesSacd();
        $attributes = [
            'id_nom' => 1,
            'iniciales' => 'test',
            'color' => 'test',
        ];
        $inicialesSacd->setAllAttributes($attributes);

        $this->assertEquals(1, $inicialesSacd->getId_nom());
        $this->assertEquals('test', $inicialesSacd->getIniciales());
        $this->assertEquals('test', $inicialesSacd->getColor());
    }
}
