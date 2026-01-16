<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoTextClave;
use Tests\myTest;

class EncargoTextClaveTest extends myTest
{
    public function test_create_valid_encargoTextClave()
    {
        $encargoTextClave = new EncargoTextClave('test value');
        $this->assertEquals('test value', $encargoTextClave->value());
    }

    public function test_equals_returns_true_for_same_encargoTextClave()
    {
        $encargoTextClave1 = new EncargoTextClave('test value');
        $encargoTextClave2 = new EncargoTextClave('test value');
        $this->assertTrue($encargoTextClave1->equals($encargoTextClave2));
    }

    public function test_equals_returns_false_for_different_encargoTextClave()
    {
        $encargoTextClave1 = new EncargoTextClave('test value');
        $encargoTextClave2 = new EncargoTextClave('alternative value');
        $this->assertFalse($encargoTextClave1->equals($encargoTextClave2));
    }

    public function test_to_string_returns_encargoTextClave_value()
    {
        $encargoTextClave = new EncargoTextClave('test value');
        $this->assertEquals('test value', (string)$encargoTextClave);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $encargoTextClave = EncargoTextClave::fromNullableString('test value');
        $this->assertInstanceOf(EncargoTextClave::class, $encargoTextClave);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $encargoTextClave = EncargoTextClave::fromNullableString(null);
        $this->assertNull($encargoTextClave);
    }

}
