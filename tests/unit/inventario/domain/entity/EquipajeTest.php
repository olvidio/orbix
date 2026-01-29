<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\value_objects\EquipajeCabecera;
use src\inventario\domain\value_objects\EquipajeCabecerab;
use src\inventario\domain\value_objects\EquipajeId;
use src\inventario\domain\value_objects\EquipajeIdsActiv;
use src\inventario\domain\value_objects\EquipajeLugar;
use src\inventario\domain\value_objects\EquipajeNom;
use src\inventario\domain\value_objects\EquipajePie;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class EquipajeTest extends myTest
{
    private Equipaje $Equipaje;

    public function setUp(): void
    {
        parent::setUp();
        $this->Equipaje = new Equipaje();
        $this->Equipaje->setId_equipaje(1);
    }

    public function test_get_id_equipaje()
    {
        $this->assertEquals(1, $this->Equipaje->getId_equipaje());
    }

    public function test_set_and_get_ids_activ()
    {
        $ids_activVo = new EquipajeIdsActiv(1);
        $this->Equipaje->setIdsActivVo($ids_activVo);
        $this->assertInstanceOf(EquipajeIdsActiv::class, $this->Equipaje->getIdsActivVo());
        $this->assertEquals(1, $this->Equipaje->getIdsActivVo()->value());
    }

    public function test_set_and_get_lugar()
    {
        $lugarVo = new EquipajeLugar('test');
        $this->Equipaje->setLugarVo($lugarVo);
        $this->assertInstanceOf(EquipajeLugar::class, $this->Equipaje->getLugarVo());
        $this->assertEquals('test', $this->Equipaje->getLugarVo()->value());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Equipaje->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Equipaje->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->Equipaje->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Equipaje->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Equipaje->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->Equipaje->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_id_ubi_activ()
    {
        $this->Equipaje->setId_ubi_activ(1);
        $this->assertEquals(1, $this->Equipaje->getId_ubi_activ());
    }

    public function test_set_and_get_nom_equipaje()
    {
        $nom_equipajeVo = new EquipajeNom('test');
        $this->Equipaje->setNomEquipajeVo($nom_equipajeVo);
        $this->assertInstanceOf(EquipajeNom::class, $this->Equipaje->getNomEquipajeVo());
        $this->assertEquals('test', $this->Equipaje->getNomEquipajeVo()->value());
    }

    public function test_set_and_get_cabecera()
    {
        $cabeceraVo = new EquipajeCabecera('test');
        $this->Equipaje->setCabeceraVo($cabeceraVo);
        $this->assertInstanceOf(EquipajeCabecera::class, $this->Equipaje->getCabeceraVo());
        $this->assertEquals('test', $this->Equipaje->getCabeceraVo()->value());
    }

    public function test_set_and_get_pie()
    {
        $pieVo = new EquipajePie('test');
        $this->Equipaje->setPieVo($pieVo);
        $this->assertInstanceOf(EquipajePie::class, $this->Equipaje->getPieVo());
        $this->assertEquals('test', $this->Equipaje->getPieVo()->value());
    }

    public function test_set_and_get_cabecerab()
    {
        $cabecerabVo = new EquipajeCabecerab('test');
        $this->Equipaje->setCabecerabVo($cabecerabVo);
        $this->assertInstanceOf(EquipajeCabecerab::class, $this->Equipaje->getCabecerabVo());
        $this->assertEquals('test', $this->Equipaje->getCabecerabVo()->value());
    }

    public function test_set_all_attributes()
    {
        $equipaje = new Equipaje();
        $attributes = [
            'id_equipaje' => new EquipajeId(1),
            'ids_activ' => new EquipajeIdsActiv(1),
            'lugar' => new EquipajeLugar('test'),
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'id_ubi_activ' => 1,
            'nom_equipaje' => new EquipajeNom('test'),
            'cabecera' => new EquipajeCabecera('test'),
            'pie' => new EquipajePie('test'),
            'cabecerab' => new EquipajeCabecerab('test'),
        ];
        $equipaje->setAllAttributes($attributes);

        $this->assertEquals(1, $equipaje->getIdEquipajeVo()->value());
        $this->assertEquals(1, $equipaje->getIdsActivVo()->value());
        $this->assertEquals('test', $equipaje->getLugarVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $equipaje->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $equipaje->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $equipaje->getId_ubi_activ());
        $this->assertEquals('test', $equipaje->getNomEquipajeVo()->value());
        $this->assertEquals('test', $equipaje->getCabeceraVo()->value());
        $this->assertEquals('test', $equipaje->getPieVo()->value());
        $this->assertEquals('test', $equipaje->getCabecerabVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $equipaje = new Equipaje();
        $attributes = [
            'id_equipaje' => 1,
            'ids_activ' => 1,
            'lugar' => 'test',
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'id_ubi_activ' => 1,
            'nom_equipaje' => 'test',
            'cabecera' => 'test',
            'pie' => 'test',
            'cabecerab' => 'test',
        ];
        $equipaje->setAllAttributes($attributes);

        $this->assertEquals(1, $equipaje->getIdEquipajeVo()->value());
        $this->assertEquals(1, $equipaje->getIdsActivVo()->value());
        $this->assertEquals('test', $equipaje->getLugarVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $equipaje->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $equipaje->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $equipaje->getId_ubi_activ());
        $this->assertEquals('test', $equipaje->getNomEquipajeVo()->value());
        $this->assertEquals('test', $equipaje->getCabeceraVo()->value());
        $this->assertEquals('test', $equipaje->getPieVo()->value());
        $this->assertEquals('test', $equipaje->getCabecerabVo()->value());
    }
}
