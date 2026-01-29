<?php

namespace Tests\unit\encargossacd\domain\entity;

use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\encargossacd\domain\value_objects\MesNum;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use Tests\myTest;

class EncargoHorarioTest extends myTest
{
    private EncargoHorario $EncargoHorario;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoHorario = new EncargoHorario();
        $this->EncargoHorario->setId_enc(1);
        $this->EncargoHorario->setId_item_h(1);
    }

    public function test_set_and_get_id_enc()
    {
        $this->EncargoHorario->setId_enc(1);
        $this->assertEquals(1, $this->EncargoHorario->getId_enc());
    }

    public function test_set_and_get_id_item_h()
    {
        $this->EncargoHorario->setId_item_h(1);
        $this->assertEquals(1, $this->EncargoHorario->getId_item_h());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->EncargoHorario->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->EncargoHorario->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->EncargoHorario->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->EncargoHorario->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->EncargoHorario->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->EncargoHorario->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_dia_ref()
    {
        $dia_refVo = new DiaRefCode('TST');
        $this->EncargoHorario->setDiaRefVo($dia_refVo);
        $this->assertInstanceOf(DiaRefCode::class, $this->EncargoHorario->getDiaRefVo());
        $this->assertEquals('TST', $this->EncargoHorario->getDiaRefVo()->value());
    }

    public function test_set_and_get_dia_num()
    {
        $this->EncargoHorario->setDia_num(1);
        $this->assertEquals(1, $this->EncargoHorario->getDia_num());
    }

    public function test_set_and_get_mas_menos()
    {
        $mas_menosVo = new MasMenosCode('TST');
        $this->EncargoHorario->setMasMenosVo($mas_menosVo);
        $this->assertInstanceOf(MasMenosCode::class, $this->EncargoHorario->getMasMenosVo());
        $this->assertEquals('TST', $this->EncargoHorario->getMasMenosVo()->value());
    }

    public function test_set_and_get_dia_inc()
    {
        $this->EncargoHorario->setDia_inc(1);
        $this->assertEquals(1, $this->EncargoHorario->getDia_inc());
    }

    public function test_set_and_get_h_ini()
    {
        $this->EncargoHorario->setH_ini(TimeLocal::fromString('10:30'));
        $this->assertEquals('10:30', $this->EncargoHorario->getH_ini()->format('H:i'));
    }

    public function test_set_and_get_h_fin()
    {
        $this->EncargoHorario->setH_fin(TimeLocal::fromString('13:45'));
        $this->assertEquals('13:45', $this->EncargoHorario->getH_fin()->format('H:i'));
    }

    public function test_set_and_get_n_sacd()
    {
        $this->EncargoHorario->setN_sacd(1);
        $this->assertEquals(1, $this->EncargoHorario->getN_sacd());
    }

    public function test_set_and_get_mes()
    {
        $mesVo = new MesNum(1);
        $this->EncargoHorario->setMesVo($mesVo);
        $this->assertInstanceOf(MesNum::class, $this->EncargoHorario->getMesVo());
        $this->assertEquals(1, $this->EncargoHorario->getMesVo()->value());
    }

    public function test_set_all_attributes()
    {
        $encargoHorario = new EncargoHorario();
        $attributes = [
            'id_enc' => 1,
            'id_item_h' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'dia_ref' => new DiaRefCode('TST'),
            'dia_num' => 1,
            'mas_menos' => new MasMenosCode('TST'),
            'dia_inc' => 1,
            'h_ini' => TimeLocal::fromString('10:30'),
            'h_fin' => TimeLocal::fromString('13:45'),
            'n_sacd' => 1,
            'mes' => new MesNum(1),
        ];
        $encargoHorario->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoHorario->getId_enc());
        $this->assertEquals(1, $encargoHorario->getId_item_h());
        $this->assertEquals('2024-01-15 10:30:00', $encargoHorario->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $encargoHorario->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $encargoHorario->getDiaRefVo()->value());
        $this->assertEquals(1, $encargoHorario->getDia_num());
        $this->assertEquals('TST', $encargoHorario->getMasMenosVo()->value());
        $this->assertEquals(1, $encargoHorario->getDia_inc());
        $this->assertEquals('10:30', $encargoHorario->getH_ini()->format('H:i'));
        $this->assertEquals('13:45', $encargoHorario->getH_fin()->format('H:i'));
        $this->assertEquals(1, $encargoHorario->getN_sacd());
        $this->assertEquals(1, $encargoHorario->getMesVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoHorario = new EncargoHorario();
        $attributes = [
            'id_enc' => 1,
            'id_item_h' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'dia_ref' => 'TST',
            'dia_num' => 1,
            'mas_menos' => 'TST',
            'dia_inc' => 1,
            'h_ini' => TimeLocal::fromString('10:30'),
            'h_fin' => TimeLocal::fromString('13:45'),
            'n_sacd' => 1,
            'mes' => 1,
        ];
        $encargoHorario->setAllAttributes($attributes);

        $this->assertEquals(1, $encargoHorario->getId_enc());
        $this->assertEquals(1, $encargoHorario->getId_item_h());
        $this->assertEquals('2024-01-15 10:30:00', $encargoHorario->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $encargoHorario->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $encargoHorario->getDiaRefVo()->value());
        $this->assertEquals(1, $encargoHorario->getDia_num());
        $this->assertEquals('TST', $encargoHorario->getMasMenosVo()->value());
        $this->assertEquals(1, $encargoHorario->getDia_inc());
        $this->assertEquals('10:30', $encargoHorario->getH_ini()->format('H:i'));
        $this->assertEquals('13:45', $encargoHorario->getH_fin()->format('H:i'));
        $this->assertEquals(1, $encargoHorario->getN_sacd());
        $this->assertEquals(1, $encargoHorario->getMesVo()->value());
    }
}
