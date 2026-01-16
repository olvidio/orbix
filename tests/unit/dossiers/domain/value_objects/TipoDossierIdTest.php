<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\TipoDossierId;
use Tests\myTest;

class TipoDossierIdTest extends myTest
{
    public function test_create_valid_tipoDossierId()
    {
        $tipoDossierId = new TipoDossierId(123);
        $this->assertEquals(123, $tipoDossierId->value());
    }

    public function test_equals_returns_true_for_same_tipoDossierId()
    {
        $tipoDossierId1 = new TipoDossierId(123);
        $tipoDossierId2 = new TipoDossierId(123);
        $this->assertTrue($tipoDossierId1->equals($tipoDossierId2));
    }

    public function test_equals_returns_false_for_different_tipoDossierId()
    {
        $tipoDossierId1 = new TipoDossierId(123);
        $tipoDossierId2 = new TipoDossierId(456);
        $this->assertFalse($tipoDossierId1->equals($tipoDossierId2));
    }

    public function test_to_string_returns_tipoDossierId_value()
    {
        $tipoDossierId = new TipoDossierId(123);
        $this->assertEquals(123, (string)$tipoDossierId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoDossierId = TipoDossierId::fromNullableInt(123);
        $this->assertInstanceOf(TipoDossierId::class, $tipoDossierId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoDossierId = TipoDossierId::fromNullableInt(null);
        $this->assertNull($tipoDossierId);
    }

}
