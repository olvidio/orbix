<?php

namespace Tests\unit\casas\domain\value_objects;

use src\casas\domain\value_objects\IngresoObserv;
use Tests\myTest;

class IngresoObservTest extends myTest
{
    public function test_create_valid_ingresoObserv()
    {
        $ingresoObserv = new IngresoObserv('test value');
        $this->assertEquals('test value', $ingresoObserv->value());
    }

    public function test_equals_returns_true_for_same_ingresoObserv()
    {
        $ingresoObserv1 = new IngresoObserv('test value');
        $ingresoObserv2 = new IngresoObserv('test value');
        $this->assertTrue($ingresoObserv1->equals($ingresoObserv2));
    }

    public function test_equals_returns_false_for_different_ingresoObserv()
    {
        $ingresoObserv1 = new IngresoObserv('test value');
        $ingresoObserv2 = new IngresoObserv('alternative value');
        $this->assertFalse($ingresoObserv1->equals($ingresoObserv2));
    }

    public function test_to_string_returns_ingresoObserv_value()
    {
        $ingresoObserv = new IngresoObserv('test value');
        $this->assertEquals('test value', (string)$ingresoObserv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $ingresoObserv = IngresoObserv::fromNullableString('test value');
        $this->assertInstanceOf(IngresoObserv::class, $ingresoObserv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $ingresoObserv = IngresoObserv::fromNullableString(null);
        $this->assertNull($ingresoObserv);
    }

}
