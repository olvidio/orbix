<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\NumSacerdotes;
use Tests\myTest;

class NumSacerdotesTest extends myTest
{
    public function test_create_valid_numSacerdotes()
    {
        $numSacerdotes = new NumSacerdotes(123);
        $this->assertEquals(123, $numSacerdotes->value());
    }

    public function test_equals_returns_true_for_same_numSacerdotes()
    {
        $numSacerdotes1 = new NumSacerdotes(123);
        $numSacerdotes2 = new NumSacerdotes(123);
        $this->assertTrue($numSacerdotes1->equals($numSacerdotes2));
    }

    public function test_equals_returns_false_for_different_numSacerdotes()
    {
        $numSacerdotes1 = new NumSacerdotes(123);
        $numSacerdotes2 = new NumSacerdotes(456);
        $this->assertFalse($numSacerdotes1->equals($numSacerdotes2));
    }

    public function test_to_string_returns_numSacerdotes_value()
    {
        $numSacerdotes = new NumSacerdotes(123);
        $this->assertEquals(123, (string)$numSacerdotes);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $numSacerdotes = NumSacerdotes::fromNullableInt(123);
        $this->assertInstanceOf(NumSacerdotes::class, $numSacerdotes);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $numSacerdotes = NumSacerdotes::fromNullableInt(null);
        $this->assertNull($numSacerdotes);
    }

}
