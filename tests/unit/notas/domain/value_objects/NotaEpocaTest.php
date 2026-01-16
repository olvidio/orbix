<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\NotaEpoca;
use Tests\myTest;

class NotaEpocaTest extends myTest
{
    public function test_create_valid_notaEpoca()
    {
        $notaEpoca = new NotaEpoca(NotaEpoca::EPOCA_CA);
        $this->assertEquals(1, $notaEpoca->value());
    }

    public function test_to_string_returns_notaEpoca_value()
    {
        $notaEpoca = new NotaEpoca(NotaEpoca::EPOCA_CA);
        $this->assertEquals(NotaEpoca::EPOCA_CA, (string)$notaEpoca);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $notaEpoca = NotaEpoca::fromNullableInt(NotaEpoca::EPOCA_CA);
        $this->assertInstanceOf(NotaEpoca::class, $notaEpoca);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $notaEpoca = NotaEpoca::fromNullableInt(null);
        $this->assertNull($notaEpoca);
    }

}
