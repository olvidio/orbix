<?php

namespace Tests\unit\dossiers\domain\entity;

use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierTabla;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class DossierTest extends myTest
{
    private Dossier $Dossier;

    public function setUp(): void
    {
        parent::setUp();
        $this->Dossier = new Dossier();
        $this->Dossier->setTablaVo(new DossierTabla('p'));
        $this->Dossier->setId_pau(1);
    }

    public function test_set_and_get_tabla()
    {
        $tablaVo = new DossierTabla('p');
        $this->Dossier->setTablaVo($tablaVo);
        $this->assertInstanceOf(DossierTabla::class, $this->Dossier->getTablaVo());
        $this->assertEquals('p', $this->Dossier->getTablaVo()->value());
    }

    public function test_set_and_get_id_pau()
    {
        $this->Dossier->setId_pau(1);
        $this->assertEquals(1, $this->Dossier->getId_pau());
    }

    public function test_set_and_get_id_tipo_dossier()
    {
        $this->Dossier->setId_tipo_dossier(1);
        $this->assertEquals(1, $this->Dossier->getId_tipo_dossier());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Dossier->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Dossier->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->Dossier->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_camb_dossier()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Dossier->setF_camb_dossier($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Dossier->getF_camb_dossier());
        $this->assertEquals('2024-01-15 10:30:00', $this->Dossier->getF_camb_dossier()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_active()
    {
        $this->Dossier->setActive(true);
        $this->assertTrue($this->Dossier->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Dossier->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Dossier->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->Dossier->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $dossier = new Dossier();
        $attributes = [
            'tabla' => new DossierTabla('p'),
            'id_pau' => 1,
            'id_tipo_dossier' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_camb_dossier' => new DateTimeLocal('2024-01-15 10:30:00'),
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $dossier->setAllAttributes($attributes);

        $this->assertEquals('p', $dossier->getTablaVo()->value());
        $this->assertEquals(1, $dossier->getId_pau());
        $this->assertEquals(1, $dossier->getId_tipo_dossier());
        $this->assertEquals('2024-01-15 10:30:00', $dossier->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $dossier->getF_camb_dossier()->format('Y-m-d H:i:s'));
        $this->assertTrue($dossier->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $dossier->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $dossier = new Dossier();
        $attributes = [
            'tabla' => 'p',
            'id_pau' => 1,
            'id_tipo_dossier' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_camb_dossier' => new DateTimeLocal('2024-01-15 10:30:00'),
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $dossier->setAllAttributes($attributes);

        $this->assertEquals('p', $dossier->getTablaVo()->value());
        $this->assertEquals(1, $dossier->getId_pau());
        $this->assertEquals(1, $dossier->getId_tipo_dossier());
        $this->assertEquals('2024-01-15 10:30:00', $dossier->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $dossier->getF_camb_dossier()->format('Y-m-d H:i:s'));
        $this->assertTrue($dossier->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $dossier->getF_active()->format('Y-m-d H:i:s'));
    }
}
