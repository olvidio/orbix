<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoNumFin;
use Tests\myTest;

class DocumentoNumFinTest extends myTest
{
    public function test_create_valid_documentoNumFin()
    {
        $documentoNumFin = new DocumentoNumFin(123);
        $this->assertEquals(123, $documentoNumFin->value());
    }

    public function test_equals_returns_true_for_same_documentoNumFin()
    {
        $documentoNumFin1 = new DocumentoNumFin(123);
        $documentoNumFin2 = new DocumentoNumFin(123);
        $this->assertTrue($documentoNumFin1->equals($documentoNumFin2));
    }

    public function test_equals_returns_false_for_different_documentoNumFin()
    {
        $documentoNumFin1 = new DocumentoNumFin(123);
        $documentoNumFin2 = new DocumentoNumFin(456);
        $this->assertFalse($documentoNumFin1->equals($documentoNumFin2));
    }

    public function test_to_string_returns_documentoNumFin_value()
    {
        $documentoNumFin = new DocumentoNumFin(123);
        $this->assertEquals(123, (string)$documentoNumFin);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $documentoNumFin = DocumentoNumFin::fromNullableInt(123);
        $this->assertInstanceOf(DocumentoNumFin::class, $documentoNumFin);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $documentoNumFin = DocumentoNumFin::fromNullableInt(null);
        $this->assertNull($documentoNumFin);
    }

}
