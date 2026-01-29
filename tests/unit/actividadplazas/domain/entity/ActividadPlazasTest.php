<?php

namespace Tests\unit\actividadplazas\domain\entity;

use src\actividadplazas\domain\entity\ActividadPlazas;
use src\actividadplazas\domain\value_objects\DelegacionTablaCode;
use src\actividadplazas\domain\value_objects\PlazaClCode;
use src\actividadplazas\domain\value_objects\PlazasNumero;
use Tests\myTest;

class ActividadPlazasTest extends myTest
{
    private ActividadPlazas $ActividadPlazas;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadPlazas = new ActividadPlazas();
        $this->ActividadPlazas->setId_activ(1);
        $this->ActividadPlazas->setId_dl(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->ActividadPlazas->setId_activ(1);
        $this->assertEquals(1, $this->ActividadPlazas->getId_activ());
    }

    public function test_set_and_get_id_dl()
    {
        $this->ActividadPlazas->setId_dl(1);
        $this->assertEquals(1, $this->ActividadPlazas->getId_dl());
    }

    public function test_set_and_get_plazas()
    {
        $plazasVo = new PlazasNumero(1);
        $this->ActividadPlazas->setPlazasVo($plazasVo);
        $this->assertInstanceOf(PlazasNumero::class, $this->ActividadPlazas->getPlazasVo());
        $this->assertEquals(1, $this->ActividadPlazas->getPlazasVo()->value());
    }

    public function test_set_and_get_cl()
    {
        $clVo = new PlazaClCode('Test value');
        $this->ActividadPlazas->setClVo($clVo);
        $this->assertInstanceOf(PlazaClCode::class, $this->ActividadPlazas->getClVo());
        $this->assertEquals('Test value', $this->ActividadPlazas->getClVo()->value());
    }

    public function test_set_and_get_dl_tabla()
    {
        $dl_tablaVo = new DelegacionTablaCode('Test');
        $this->ActividadPlazas->setDlTablaVo($dl_tablaVo);
        $this->assertInstanceOf(DelegacionTablaCode::class, $this->ActividadPlazas->getDlTablaVo());
        $this->assertEquals('Test', $this->ActividadPlazas->getDlTablaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $actividadPlazas = new ActividadPlazas();
        $attributes = [
            'id_activ' => 1,
            'id_dl' => 1,
            'plazas' => new PlazasNumero(1),
            'cl' => new PlazaClCode('Test value'),
            'dl_tabla' => new DelegacionTablaCode('Test'),
        ];
        $actividadPlazas->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadPlazas->getId_activ());
        $this->assertEquals(1, $actividadPlazas->getId_dl());
        $this->assertEquals(1, $actividadPlazas->getPlazasVo()->value());
        $this->assertEquals('Test value', $actividadPlazas->getClVo()->value());
        $this->assertEquals('Test', $actividadPlazas->getDlTablaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadPlazas = new ActividadPlazas();
        $attributes = [
            'id_activ' => 1,
            'id_dl' => 1,
            'plazas' => 1,
            'cl' => 'Test value',
            'dl_tabla' => 'Test',
        ];
        $actividadPlazas->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadPlazas->getId_activ());
        $this->assertEquals(1, $actividadPlazas->getId_dl());
        $this->assertEquals(1, $actividadPlazas->getPlazasVo()->value());
        $this->assertEquals('Test value', $actividadPlazas->getClVo()->value());
        $this->assertEquals('Test', $actividadPlazas->getDlTablaVo()->value());
    }
}
