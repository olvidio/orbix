<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\ActividadObservMaterial;
use Tests\myTest;

class ActividadObservMaterialTest extends myTest
{
    public function test_create_valid_actividadObservMaterial()
    {
        $actividadObservMaterial = new ActividadObservMaterial('test value');
        $this->assertEquals('test value', $actividadObservMaterial->value());
    }

    public function test_equals_returns_true_for_same_actividadObservMaterial()
    {
        $actividadObservMaterial1 = new ActividadObservMaterial('test value');
        $actividadObservMaterial2 = new ActividadObservMaterial('test value');
        $this->assertTrue($actividadObservMaterial1->equals($actividadObservMaterial2));
    }

    public function test_equals_returns_false_for_different_actividadObservMaterial()
    {
        $actividadObservMaterial1 = new ActividadObservMaterial('test value');
        $actividadObservMaterial2 = new ActividadObservMaterial('alternative value');
        $this->assertFalse($actividadObservMaterial1->equals($actividadObservMaterial2));
    }

    public function test_to_string_returns_actividadObservMaterial_value()
    {
        $actividadObservMaterial = new ActividadObservMaterial('test value');
        $this->assertEquals('test value', (string)$actividadObservMaterial);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $actividadObservMaterial = ActividadObservMaterial::fromNullableString('test value');
        $this->assertInstanceOf(ActividadObservMaterial::class, $actividadObservMaterial);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $actividadObservMaterial = ActividadObservMaterial::fromNullableString(null);
        $this->assertNull($actividadObservMaterial);
    }

    public function test_tabuladores_interiores_se_cambian_por_un_espacio()
    {
        // Texto pegado de una hoja de cálculo: celdas separadas por tabuladores.
        $vo = new ActividadObservMaterial("Na Baště (Praga)\t\t08.08-30.08\tRepaso\t");
        $this->assertSame('Na Baště (Praga) 08.08-30.08 Repaso', $vo->value());
    }

    public function test_fromNullableString_con_tabuladores_no_lanza_excepcion()
    {
        $vo = ActividadObservMaterial::fromNullableString("Genova\t13.08-30.08");
        $this->assertSame('Genova 13.08-30.08', $vo?->value());
    }

    public function test_saltos_de_linea_interiores_se_cambian_por_un_espacio()
    {
        // Varias filas pegadas de una hoja de cálculo (Windows: \r\n).
        $vo = new ActividadObservMaterial("Genova\t13.08-30.08\r\nPraga\n\n08.08-30.08\r\n");
        $this->assertSame('Genova 13.08-30.08 Praga 08.08-30.08', $vo->value());
    }

    public function test_otros_caracteres_de_control_siguen_rechazandose()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ActividadObservMaterial("texto\x01raro");
    }
}
