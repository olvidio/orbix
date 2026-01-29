<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\value_objects\EncargoModoId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class EncargoSacdTest extends myTest
{
    private EncargoSacd $EncargoSacd;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoSacd = new EncargoSacd();
        $this->EncargoSacd->setId_item(1);
        $this->EncargoSacd->setId_enc(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->EncargoSacd->setId_item(1);
        $this->assertEquals(1, $this->EncargoSacd->getId_item());
    }

    public function test_set_and_get_id_enc()
    {
        $this->EncargoSacd->setId_enc(1);
        $this->assertEquals(1, $this->EncargoSacd->getId_enc());
    }

    public function test_set_and_get_id_nom()
    {
        $this->EncargoSacd->setId_nom(1);
        $this->assertEquals(1, $this->EncargoSacd->getId_nom());
    }

    public function test_set_and_get_modo()
    {
        $modoVo = new EncargoModoId(1);
        $this->EncargoSacd->setModoVo($modoVo);
        $this->assertInstanceOf(EncargoModoId::class, $this->EncargoSacd->getModoVo());
        $this->assertEquals(1, $this->EncargoSacd->getModoVo()->value());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->EncargoSacd->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->EncargoSacd->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->EncargoSacd->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->EncargoSacd->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->EncargoSacd->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->EncargoSacd->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $encargoSacd = new EncargoSacd();
        $attributes = [
            'id_item' => 1,
            'id_enc' => 1,
            'id_nom' => 1,
            'modo' => new EncargoModoId(1),
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $encargoSacd->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoSacd->getId_item());
        $this->assertEquals(1, $encargoSacd->getId_enc());
        $this->assertEquals(1, $encargoSacd->getId_nom());
        $this->assertEquals(1, $encargoSacd->getModoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacd->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacd->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoSacd = new EncargoSacd();
        $attributes = [
            'id_item' => 1,
            'id_enc' => 1,
            'id_nom' => 1,
            'modo' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $encargoSacd->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoSacd->getId_item());
        $this->assertEquals(1, $encargoSacd->getId_enc());
        $this->assertEquals(1, $encargoSacd->getId_nom());
        $this->assertEquals(1, $encargoSacd->getModoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacd->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacd->getF_fin()->format('Y-m-d H:i:s'));
    }
}
