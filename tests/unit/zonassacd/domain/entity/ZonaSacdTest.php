<?php

namespace Tests\unit\zonassacd\domain\entity;

use src\zonassacd\domain\entity\ZonaSacd;
use Tests\myTest;

class ZonaSacdTest extends myTest
{
    private ZonaSacd $ZonaSacd;

    public function setUp(): void
    {
        parent::setUp();
        $this->ZonaSacd = new ZonaSacd();
        $this->ZonaSacd->setId_item(1);
        $this->ZonaSacd->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ZonaSacd->setId_item(1);
        $this->assertEquals(1, $this->ZonaSacd->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ZonaSacd->setId_nom(1);
        $this->assertEquals(1, $this->ZonaSacd->getId_nom());
    }

    public function test_set_and_get_id_zona()
    {
        $this->ZonaSacd->setId_zona(1);
        $this->assertEquals(1, $this->ZonaSacd->getId_zona());
    }

    public function test_set_and_get_propia()
    {
        $this->ZonaSacd->setPropia(true);
        $this->assertTrue($this->ZonaSacd->isPropia());
    }

    public function test_set_all_attributes()
    {
        $zonaSacd = new ZonaSacd();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_zona' => 1,
            'propia' => true,
        ];
        $zonaSacd->setAllAttributes($attributes);

        $this->assertEquals(1, $zonaSacd->getId_item());
        $this->assertEquals(1, $zonaSacd->getId_nom());
        $this->assertEquals(1, $zonaSacd->getId_zona());
        $this->assertTrue($zonaSacd->isPropia());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $zonaSacd = new ZonaSacd();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_zona' => 1,
            'propia' => true,
        ];
        $zonaSacd->setAllAttributes($attributes);

        $this->assertEquals(1, $zonaSacd->getId_item());
        $this->assertEquals(1, $zonaSacd->getId_nom());
        $this->assertEquals(1, $zonaSacd->getId_zona());
        $this->assertTrue($zonaSacd->isPropia());
    }
}
