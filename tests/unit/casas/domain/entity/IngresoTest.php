<?php

namespace Tests\unit\casas\domain\entity;

use src\casas\domain\entity\Ingreso;
use src\casas\domain\value_objects\IngresoImporte;
use src\casas\domain\value_objects\IngresoNumAsistentes;
use src\casas\domain\value_objects\IngresoObserv;
use Tests\myTest;

class IngresoTest extends myTest
{
    private Ingreso $Ingreso;

    public function setUp(): void
    {
        parent::setUp();
        $this->Ingreso = new Ingreso();
        $this->Ingreso->setId_activ(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->Ingreso->setId_activ(1);
        $this->assertEquals(1, $this->Ingreso->getId_activ());
    }

    public function test_set_and_get_ingresos()
    {
        $ingresosVo = new IngresoImporte(1);
        $this->Ingreso->setIngresosVo($ingresosVo);
        $this->assertInstanceOf(IngresoImporte::class, $this->Ingreso->getIngresosVo());
        $this->assertEquals(1, $this->Ingreso->getIngresosVo()->value());
    }

    public function test_set_and_get_num_asistentes()
    {
        $num_asistentesVo = new IngresoNumAsistentes(1);
        $this->Ingreso->setNumAsistentesVo($num_asistentesVo);
        $this->assertInstanceOf(IngresoNumAsistentes::class, $this->Ingreso->getNumAsistentesVo());
        $this->assertEquals(1, $this->Ingreso->getNumAsistentesVo()->value());
    }

    public function test_set_and_get_ingresos_previstos()
    {
        $ingresos_previstosVo = new IngresoImporte(1);
        $this->Ingreso->setIngresosPrevistosVo($ingresos_previstosVo);
        $this->assertInstanceOf(IngresoImporte::class, $this->Ingreso->getIngresosPrevistosVo());
        $this->assertEquals(1, $this->Ingreso->getIngresosPrevistosVo()->value());
    }

    public function test_set_and_get_num_asistentes_previstos()
    {
        $num_asistentes_previstosVo = new IngresoNumAsistentes(1);
        $this->Ingreso->setNumAsistentesPrevistosVo($num_asistentes_previstosVo);
        $this->assertInstanceOf(IngresoNumAsistentes::class, $this->Ingreso->getNumAsistentesPrevistosVo());
        $this->assertEquals(1, $this->Ingreso->getNumAsistentesPrevistosVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new IngresoObserv('test');
        $this->Ingreso->setObservVo($observVo);
        $this->assertInstanceOf(IngresoObserv::class, $this->Ingreso->getObservVo());
        $this->assertEquals('test', $this->Ingreso->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $ingreso = new Ingreso();
        $attributes = [
            'id_activ' => 1,
            'ingresos' => new IngresoImporte(1),
            'num_asistentes' => new IngresoNumAsistentes(1),
            'ingresos_previstos' => new IngresoImporte(1),
            'num_asistentes_previstos' => new IngresoNumAsistentes(1),
            'observ' => new IngresoObserv('test'),
        ];
        $ingreso->setAllAttributes($attributes);

        $this->assertEquals(1, $ingreso->getId_activ());
        $this->assertEquals(1, $ingreso->getIngresosVo()->value());
        $this->assertEquals(1, $ingreso->getNumAsistentesVo()->value());
        $this->assertEquals(1, $ingreso->getIngresosPrevistosVo()->value());
        $this->assertEquals(1, $ingreso->getNumAsistentesPrevistosVo()->value());
        $this->assertEquals('test', $ingreso->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $ingreso = new Ingreso();
        $attributes = [
            'id_activ' => 1,
            'ingresos' => 1,
            'num_asistentes' => 1,
            'ingresos_previstos' => 1,
            'num_asistentes_previstos' => 1,
            'observ' => 'test',
        ];
        $ingreso->setAllAttributes($attributes);

        $this->assertEquals(1, $ingreso->getId_activ());
        $this->assertEquals(1, $ingreso->getIngresosVo()->value());
        $this->assertEquals(1, $ingreso->getNumAsistentesVo()->value());
        $this->assertEquals(1, $ingreso->getIngresosPrevistosVo()->value());
        $this->assertEquals(1, $ingreso->getNumAsistentesPrevistosVo()->value());
        $this->assertEquals('test', $ingreso->getObservVo()->value());
    }
}
