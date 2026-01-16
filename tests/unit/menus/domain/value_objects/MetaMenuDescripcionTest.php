<?php

namespace Tests\unit\menus\domain\value_objects;

use src\menus\domain\value_objects\MetaMenuDescripcion;
use Tests\myTest;

class MetaMenuDescripcionTest extends myTest
{
    public function test_create_valid_metaMenuDescripcion()
    {
        $metaMenuDescripcion = new MetaMenuDescripcion('test value');
        $this->assertEquals('test value', $metaMenuDescripcion->value());
    }

    public function test_equals_returns_true_for_same_metaMenuDescripcion()
    {
        $metaMenuDescripcion1 = new MetaMenuDescripcion('test value');
        $metaMenuDescripcion2 = new MetaMenuDescripcion('test value');
        $this->assertTrue($metaMenuDescripcion1->equals($metaMenuDescripcion2));
    }

    public function test_equals_returns_false_for_different_metaMenuDescripcion()
    {
        $metaMenuDescripcion1 = new MetaMenuDescripcion('test value');
        $metaMenuDescripcion2 = new MetaMenuDescripcion('alternative value');
        $this->assertFalse($metaMenuDescripcion1->equals($metaMenuDescripcion2));
    }

    public function test_to_string_returns_metaMenuDescripcion_value()
    {
        $metaMenuDescripcion = new MetaMenuDescripcion('test value');
        $this->assertEquals('test value', (string)$metaMenuDescripcion);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $metaMenuDescripcion = MetaMenuDescripcion::fromNullableString('test value');
        $this->assertInstanceOf(MetaMenuDescripcion::class, $metaMenuDescripcion);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $metaMenuDescripcion = MetaMenuDescripcion::fromNullableString(null);
        $this->assertNull($metaMenuDescripcion);
    }

}
