<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use Tests\myTest;

class EncargoSacdHorarioTest extends myTest
{
    private EncargoSacdHorario $EncargoSacdHorario;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoSacdHorario = new EncargoSacdHorario();
        $this->EncargoSacdHorario->setId_item(1);
        $this->EncargoSacdHorario->setId_enc(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->EncargoSacdHorario->setId_item(1);
        $this->assertEquals(1, $this->EncargoSacdHorario->getId_item());
    }

    public function test_set_and_get_id_enc()
    {
        $this->EncargoSacdHorario->setId_enc(1);
        $this->assertEquals(1, $this->EncargoSacdHorario->getId_enc());
    }

    public function test_set_and_get_id_nom()
    {
        $this->EncargoSacdHorario->setId_nom(1);
        $this->assertEquals(1, $this->EncargoSacdHorario->getId_nom());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->EncargoSacdHorario->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->EncargoSacdHorario->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->EncargoSacdHorario->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->EncargoSacdHorario->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->EncargoSacdHorario->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->EncargoSacdHorario->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_dia_ref()
    {
        $dia_refVo = new DiaRefCode('TST');
        $this->EncargoSacdHorario->setDiaRefVo($dia_refVo);
        $this->assertInstanceOf(DiaRefCode::class, $this->EncargoSacdHorario->getDiaRefVo());
        $this->assertEquals('TST', $this->EncargoSacdHorario->getDiaRefVo()->value());
    }

    public function test_set_and_get_dia_num()
    {
        $this->EncargoSacdHorario->setDia_num(1);
        $this->assertEquals(1, $this->EncargoSacdHorario->getDia_num());
    }

    public function test_set_and_get_mas_menos()
    {
        $mas_menosVo = new MasMenosCode('TST');
        $this->EncargoSacdHorario->setMasMenosVo($mas_menosVo);
        $this->assertInstanceOf(MasMenosCode::class, $this->EncargoSacdHorario->getMasMenosVo());
        $this->assertEquals('TST', $this->EncargoSacdHorario->getMasMenosVo()->value());
    }

    public function test_set_and_get_dia_inc()
    {
        $this->EncargoSacdHorario->setDia_inc(1);
        $this->assertEquals(1, $this->EncargoSacdHorario->getDia_inc());
    }

    public function test_set_and_get_h_ini()
    {
        $this->EncargoSacdHorario->setH_ini(TimeLocal::fromString('10:30'));
        $this->assertEquals('10:30', $this->EncargoSacdHorario->getH_ini()->format('H:i'));
    }

    public function test_set_and_get_h_fin()
    {
        $this->EncargoSacdHorario->setH_fin(TimeLocal::fromString('13:45'));
        $this->assertEquals('13:45', $this->EncargoSacdHorario->getH_fin()->format('H:i'));
    }

    public function test_set_and_get_id_item_tarea_sacd()
    {
        $this->EncargoSacdHorario->setId_item_tarea_sacd(1);
        $this->assertEquals(1, $this->EncargoSacdHorario->getId_item_tarea_sacd());
    }

    public function test_set_all_attributes()
    {
        $encargoSacdHorario = new EncargoSacdHorario();
        $attributes = [
            'id_item' => 1,
            'id_enc' => 1,
            'id_nom' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'dia_ref' => new DiaRefCode('TST'),
            'dia_num' => 1,
            'mas_menos' => new MasMenosCode('TST'),
            'dia_inc' => 1,
            'h_ini' => TimeLocal::fromString('10:30'),
            'h_fin' => TimeLocal::fromString('13:45'),
            'id_item_tarea_sacd' => 1,
        ];
        $encargoSacdHorario->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoSacdHorario->getId_item());
        $this->assertEquals(1, $encargoSacdHorario->getId_enc());
        $this->assertEquals(1, $encargoSacdHorario->getId_nom());
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacdHorario->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacdHorario->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $encargoSacdHorario->getDiaRefVo()->value());
        $this->assertEquals(1, $encargoSacdHorario->getDia_num());
        $this->assertEquals('TST', $encargoSacdHorario->getMasMenosVo()->value());
        $this->assertEquals(1, $encargoSacdHorario->getDia_inc());
        $this->assertEquals('10:30', $encargoSacdHorario->getH_ini()->format('H:i'));
        $this->assertEquals('13:45', $encargoSacdHorario->getH_fin()->format('H:i'));
        $this->assertEquals(1, $encargoSacdHorario->getId_item_tarea_sacd());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoSacdHorario = new EncargoSacdHorario();
        $attributes = [
            'id_item' => 1,
            'id_enc' => 1,
            'id_nom' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'dia_ref' => 'TST',
            'dia_num' => 1,
            'mas_menos' => 'TST',
            'dia_inc' => 1,
            'h_ini' => TimeLocal::fromString('10:30'),
            'h_fin' => TimeLocal::fromString('13:45'),
            'id_item_tarea_sacd' => 1,
        ];
        $encargoSacdHorario->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoSacdHorario->getId_item());
        $this->assertEquals(1, $encargoSacdHorario->getId_enc());
        $this->assertEquals(1, $encargoSacdHorario->getId_nom());
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacdHorario->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $encargoSacdHorario->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $encargoSacdHorario->getDiaRefVo()->value());
        $this->assertEquals(1, $encargoSacdHorario->getDia_num());
        $this->assertEquals('TST', $encargoSacdHorario->getMasMenosVo()->value());
        $this->assertEquals(1, $encargoSacdHorario->getDia_inc());
        $this->assertEquals('10:30', $encargoSacdHorario->getH_ini()->format('H:i'));
        $this->assertEquals('13:45', $encargoSacdHorario->getH_fin()->format('H:i'));
        $this->assertEquals(1, $encargoSacdHorario->getId_item_tarea_sacd());
    }
}
