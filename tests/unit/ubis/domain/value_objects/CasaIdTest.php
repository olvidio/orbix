<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\CasaId;
use Tests\myTest;

class CasaIdTest extends myTest
{
    public function test_create_valid_casaId()
    {
        $casaId = new CasaId(123);
        $this->assertEquals(123, $casaId->value());
    }

    public function test_equals_returns_true_for_same_casaId()
    {
        $casaId1 = new CasaId(123);
        $casaId2 = new CasaId(123);
        $this->assertTrue($casaId1->equals($casaId2));
    }

    public function test_equals_returns_false_for_different_casaId()
    {
        $casaId1 = new CasaId(123);
        $casaId2 = new CasaId(456);
        $this->assertFalse($casaId1->equals($casaId2));
    }

    public function test_to_string_returns_casaId_value()
    {
        $casaId = new CasaId(123);
        $this->assertEquals(123, (string)$casaId);
    }

}
