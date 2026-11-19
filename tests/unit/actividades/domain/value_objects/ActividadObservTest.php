<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\ActividadObserv;
use Tests\myTest;

class ActividadObservTest extends myTest
{
    public function test_create_valid_actividadObserv()
    {
        $actividadObserv = new ActividadObserv('test value');
        $this->assertEquals('test value', $actividadObserv->value());
    }

    public function test_equals_returns_true_for_same_actividadObserv()
    {
        $actividadObserv1 = new ActividadObserv('test value');
        $actividadObserv2 = new ActividadObserv('test value');
        $this->assertTrue($actividadObserv1->equals($actividadObserv2));
    }

    public function test_equals_returns_false_for_different_actividadObserv()
    {
        $actividadObserv1 = new ActividadObserv('test value');
        $actividadObserv2 = new ActividadObserv('alternative value');
        $this->assertFalse($actividadObserv1->equals($actividadObserv2));
    }

    public function test_to_string_returns_actividadObserv_value()
    {
        $actividadObserv = new ActividadObserv('test value');
        $this->assertEquals('test value', (string)$actividadObserv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $actividadObserv = ActividadObserv::fromNullableString('test value');
        $this->assertInstanceOf(ActividadObserv::class, $actividadObserv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $actividadObserv = ActividadObserv::fromNullableString(null);
        $this->assertNull($actividadObserv);
    }

    public function test_tabuladores_interiores_se_cambian_por_un_espacio()
    {
        // Texto pegado de una hoja de cálculo: celdas separadas por tabuladores.
        $vo = new ActividadObserv("Na Baště (Praga)\t\t08.08-30.08\tRepaso\t");
        $this->assertSame('Na Baště (Praga) 08.08-30.08 Repaso', $vo->value());
    }

    public function test_fromNullableString_con_tabuladores_no_lanza_excepcion()
    {
        $vo = ActividadObserv::fromNullableString("Genova\t13.08-30.08");
        $this->assertSame('Genova 13.08-30.08', $vo?->value());
    }

    public function test_saltos_de_linea_interiores_se_cambian_por_un_espacio()
    {
        // Varias filas pegadas de una hoja de cálculo (Windows: \r\n).
        $vo = new ActividadObserv("Genova\t13.08-30.08\r\nPraga\n\n08.08-30.08\r\n");
        $this->assertSame('Genova 13.08-30.08 Praga 08.08-30.08', $vo->value());
    }

    public function test_otros_caracteres_de_control_siguen_rechazandose()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ActividadObserv("texto\x01raro");
    }
}
