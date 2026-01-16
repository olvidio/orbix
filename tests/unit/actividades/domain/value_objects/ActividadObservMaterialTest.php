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

}
