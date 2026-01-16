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

}
