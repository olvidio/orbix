<?php

namespace Tests\unit\procesos\domain\value_objects;

use src\procesos\domain\value_objects\TareaObserv;
use Tests\myTest;

class TareaObservTest extends myTest
{
    public function test_create_valid_tareaObserv()
    {
        $tareaObserv = new TareaObserv('test value');
        $this->assertEquals('test value', $tareaObserv->value());
    }

    public function test_equals_returns_true_for_same_tareaObserv()
    {
        $tareaObserv1 = new TareaObserv('test value');
        $tareaObserv2 = new TareaObserv('test value');
        $this->assertTrue($tareaObserv1->equals($tareaObserv2));
    }

    public function test_equals_returns_false_for_different_tareaObserv()
    {
        $tareaObserv1 = new TareaObserv('test value');
        $tareaObserv2 = new TareaObserv('alternative value');
        $this->assertFalse($tareaObserv1->equals($tareaObserv2));
    }

    public function test_to_string_returns_tareaObserv_value()
    {
        $tareaObserv = new TareaObserv('test value');
        $this->assertEquals('test value', (string)$tareaObserv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tareaObserv = TareaObserv::fromNullableString('test value');
        $this->assertInstanceOf(TareaObserv::class, $tareaObserv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tareaObserv = TareaObserv::fromNullableString(null);
        $this->assertNull($tareaObserv);
    }

}
