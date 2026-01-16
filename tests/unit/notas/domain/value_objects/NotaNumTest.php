<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\NotaNum;
use Tests\myTest;

class NotaNumTest extends myTest
{
    public function test_create_valid_notaNum()
    {
        $notaNum = new NotaNum(5.5);
        $this->assertEquals(5.5, $notaNum->value());
    }

    public function test_to_string_returns_notaNum_value()
    {
        $notaNum = new NotaNum(5.5);
        $this->assertEquals(5.5, (string)$notaNum);
    }

    public function test_fromNullableFloat_returns_instance_for_valid_value()
    {
        $notaNum = NotaNum::fromNullableFloat(5.5);
        $this->assertInstanceOf(NotaNum::class, $notaNum);
    }

    public function test_fromNullableFloat_returns_null_for_null_value()
    {
        $notaNum = NotaNum::fromNullableFloat(null);
        $this->assertNull($notaNum);
    }

}
