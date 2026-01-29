<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\SfsvId;
use src\ubis\domain\entity\CasaPeriodo;
use Tests\myTest;

class CasaPeriodoTest extends myTest
{
    private CasaPeriodo $CasaPeriodo;

    public function setUp(): void
    {
        parent::setUp();
        $this->CasaPeriodo = new CasaPeriodo();
        $this->CasaPeriodo->setId_item(1);
        $this->CasaPeriodo->setId_ubi(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->CasaPeriodo->setId_item(1);
        $this->assertEquals(1, $this->CasaPeriodo->getId_item());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->CasaPeriodo->setId_ubi(1);
        $this->assertEquals(1, $this->CasaPeriodo->getId_ubi());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->CasaPeriodo->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->CasaPeriodo->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->CasaPeriodo->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->CasaPeriodo->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->CasaPeriodo->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->CasaPeriodo->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_sfsv()
    {
        $sfsvVo = new SfsvId(1);
        $this->CasaPeriodo->setSfsvVo($sfsvVo);
        $this->assertInstanceOf(SfsvId::class, $this->CasaPeriodo->getSfsvVo());
        $this->assertEquals(1, $this->CasaPeriodo->getSfsvVo()->value());
    }

    public function test_set_all_attributes()
    {
        $casaPeriodo = new CasaPeriodo();
        $attributes = [
            'id_item' => 1,
            'id_ubi' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'sfsv' => new SfsvId(1),
        ];
        $casaPeriodo->setAllAttributes($attributes);

        $this->assertEquals(1, $casaPeriodo->getId_item());
        $this->assertEquals(1, $casaPeriodo->getId_ubi());
        $this->assertEquals('2024-01-15 10:30:00', $casaPeriodo->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $casaPeriodo->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $casaPeriodo->getSfsvVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $casaPeriodo = new CasaPeriodo();
        $attributes = [
            'id_item' => 1,
            'id_ubi' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'sfsv' => 1,
        ];
        $casaPeriodo->setAllAttributes($attributes);

        $this->assertEquals(1, $casaPeriodo->getId_item());
        $this->assertEquals(1, $casaPeriodo->getId_ubi());
        $this->assertEquals('2024-01-15 10:30:00', $casaPeriodo->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $casaPeriodo->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $casaPeriodo->getSfsvVo()->value());
    }
}
