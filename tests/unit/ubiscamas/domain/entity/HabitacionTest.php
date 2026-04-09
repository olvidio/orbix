<?php

namespace Tests\unit\ubiscamas\domain\entity;

use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use src\ubiscamas\domain\value_objects\HabitacionObservText;
use Tests\myTest;

class HabitacionTest extends myTest
{
    private Habitacion $habitacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->habitacion = new Habitacion();
        $this->habitacion->setIdUbiVo(100);
        $this->habitacion->setIdHabitacionVo(new HabitacionId('hab-001'));
        $this->habitacion->setOrdenVo(new HabitacionOrden(1));
    }

    public function test_set_and_get_id_ubi()
    {
        $this->habitacion->setIdUbiVo(200);
        $this->assertEquals(200, $this->habitacion->getIdUbiVo());
    }

    public function test_set_and_get_id_habitacion()
    {
        $id = new HabitacionId('hab-002');
        $this->habitacion->setIdHabitacionVo($id);
        $this->assertInstanceOf(HabitacionId::class, $this->habitacion->getIdHabitacionVo());
        $this->assertEquals('hab-002', $this->habitacion->getIdHabitacionVo()->value());
    }

    public function test_set_and_get_orden()
    {
        $orden = new HabitacionOrden(5);
        $this->habitacion->setOrdenVo($orden);
        $this->assertInstanceOf(HabitacionOrden::class, $this->habitacion->getOrdenVo());
        $this->assertEquals(5, $this->habitacion->getOrdenVo()->value());
    }

    public function test_set_and_get_orden_from_int()
    {
        $this->habitacion->setOrdenVo(3);
        $this->assertEquals(3, $this->habitacion->getOrdenVo()->value());
    }

    public function test_set_and_get_nombre()
    {
        $nombre = new HabitacionNombre('Hab. Principal');
        $this->habitacion->setNombreVo($nombre);
        $this->assertInstanceOf(HabitacionNombre::class, $this->habitacion->getNombreVo());
        $this->assertEquals('Hab. Principal', $this->habitacion->getNombreVo()->value());
    }

    public function test_nombre_is_nullable()
    {
        $this->habitacion->setNombreVo(null);
        $this->assertNull($this->habitacion->getNombreVo());
    }

    public function test_set_and_get_numero_camas()
    {
        $num = new NumeroCamas(3);
        $this->habitacion->setNumeroCamasVo($num);
        $this->assertInstanceOf(NumeroCamas::class, $this->habitacion->getNumeroCamasVo());
        $this->assertEquals(3, $this->habitacion->getNumeroCamasVo()->value());
    }

    public function test_set_and_get_numero_camas_vip()
    {
        $num = new NumeroCamas(1);
        $this->habitacion->setNumeroCamasVipVo($num);
        $this->assertInstanceOf(NumeroCamas::class, $this->habitacion->getNumeroCamasVipVo());
        $this->assertEquals(1, $this->habitacion->getNumeroCamasVipVo()->value());
    }

    public function test_set_and_get_planta()
    {
        $planta = new PlantaText('Baja');
        $this->habitacion->setPlantaVo($planta);
        $this->assertInstanceOf(PlantaText::class, $this->habitacion->getPlantaVo());
        $this->assertEquals('Baja', $this->habitacion->getPlantaVo()->value());
    }

    public function test_set_and_get_sillon()
    {
        $this->habitacion->setSillon(true);
        $this->assertTrue($this->habitacion->isSillon());

        $this->habitacion->setSillon(false);
        $this->assertFalse($this->habitacion->isSillon());
    }

    public function test_set_and_get_adaptada()
    {
        $this->habitacion->setAdaptada(true);
        $this->assertTrue($this->habitacion->isAdaptada());
    }

    public function test_set_and_get_observaciones()
    {
        $this->habitacion->setObservacionesVo('Habitación con vistas');
        $this->assertInstanceOf(HabitacionObservText::class, $this->habitacion->getObservacionesVo());
        $this->assertEquals('Habitación con vistas', $this->habitacion->getObservacionesVo()->value());
    }

    public function test_observaciones_is_nullable()
    {
        $this->habitacion->setObservacionesVo(null);
        $this->assertNull($this->habitacion->getObservacionesVo());
    }

    public function test_set_and_get_tipo_lavabo()
    {
        $tipo = new TipoLavabo(2);
        $this->habitacion->setTipoLavaboVo($tipo);
        $this->assertInstanceOf(TipoLavabo::class, $this->habitacion->getTipoLavaboVo());
        $this->assertEquals(2, $this->habitacion->getTipoLavaboVo()->value());
    }

    public function test_set_and_get_tipo_lavabo_from_int()
    {
        $this->habitacion->setTipoLavaboVo(1);
        $this->assertEquals(1, $this->habitacion->getTipoLavaboVo()->value());
    }

    public function test_tipo_lavabo_is_nullable()
    {
        $this->habitacion->setTipoLavaboVo(null);
        $this->assertNull($this->habitacion->getTipoLavaboVo());
    }

    public function test_set_and_get_despacho()
    {
        $this->habitacion->setDespacho(true);
        $this->assertTrue($this->habitacion->isDespacho());
    }

    public function test_set_and_get_id_schema()
    {
        $this->habitacion->setId_schema(5);
        $this->assertEquals(5, $this->habitacion->getId_schema());
    }
}
