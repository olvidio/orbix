<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoNumEjemplares;
use Tests\myTest;

class DocumentoNumEjemplaresTest extends myTest
{
    public function test_create_valid_documentoNumEjemplares()
    {
        $documentoNumEjemplares = new DocumentoNumEjemplares(123);
        $this->assertEquals(123, $documentoNumEjemplares->value());
    }

    public function test_equals_returns_true_for_same_documentoNumEjemplares()
    {
        $documentoNumEjemplares1 = new DocumentoNumEjemplares(123);
        $documentoNumEjemplares2 = new DocumentoNumEjemplares(123);
        $this->assertTrue($documentoNumEjemplares1->equals($documentoNumEjemplares2));
    }

    public function test_equals_returns_false_for_different_documentoNumEjemplares()
    {
        $documentoNumEjemplares1 = new DocumentoNumEjemplares(123);
        $documentoNumEjemplares2 = new DocumentoNumEjemplares(456);
        $this->assertFalse($documentoNumEjemplares1->equals($documentoNumEjemplares2));
    }

    public function test_to_string_returns_documentoNumEjemplares_value()
    {
        $documentoNumEjemplares = new DocumentoNumEjemplares(123);
        $this->assertEquals(123, (string)$documentoNumEjemplares);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $documentoNumEjemplares = DocumentoNumEjemplares::fromNullableInt(123);
        $this->assertInstanceOf(DocumentoNumEjemplares::class, $documentoNumEjemplares);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $documentoNumEjemplares = DocumentoNumEjemplares::fromNullableInt(null);
        $this->assertNull($documentoNumEjemplares);
    }

}
