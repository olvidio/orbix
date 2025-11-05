<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\IdPau;
use Tests\myTest;

class IdPauTest extends myTest
{
    public function test_create_valid_id_pau()
    {
        $idPau = new IdPau('12345');
        $this->assertEquals('12345', $idPau->value());
    }

    public function test_empty_id_pau_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('PAU identifier cannot be empty');
        new IdPau('');
    }

    public function test_equals_returns_true_for_same_id_pau()
    {
        $idPau1 = new IdPau('12345');
        $idPau2 = new IdPau('12345');
        $this->assertTrue($idPau1->equals($idPau2));
    }

    public function test_equals_returns_false_for_different_id_pau()
    {
        $idPau1 = new IdPau('12345');
        $idPau2 = new IdPau('67890');
        $this->assertFalse($idPau1->equals($idPau2));
    }

    public function test_to_string_returns_id_pau_value()
    {
        $idPau = new IdPau('12345');
        $this->assertEquals('12345', (string)$idPau);
    }

    public function test_create_valid_id_pau_with_comma_separated_values()
    {
        $idPau = new IdPau('12345,67890');
        $this->assertEquals('12345,67890', $idPau->value());
    }
}