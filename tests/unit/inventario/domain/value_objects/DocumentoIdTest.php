<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoId;
use Tests\myTest;

class DocumentoIdTest extends myTest
{
    public function test_create_valid_documentoId()
    {
        $documentoId = new DocumentoId(123);
        $this->assertEquals(123, $documentoId->value());
    }

    public function test_equals_returns_true_for_same_documentoId()
    {
        $documentoId1 = new DocumentoId(123);
        $documentoId2 = new DocumentoId(123);
        $this->assertTrue($documentoId1->equals($documentoId2));
    }

    public function test_equals_returns_false_for_different_documentoId()
    {
        $documentoId1 = new DocumentoId(123);
        $documentoId2 = new DocumentoId(456);
        $this->assertFalse($documentoId1->equals($documentoId2));
    }

    public function test_to_string_returns_documentoId_value()
    {
        $documentoId = new DocumentoId(123);
        $this->assertEquals(123, (string)$documentoId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $documentoId = DocumentoId::fromNullableInt(123);
        $this->assertInstanceOf(DocumentoId::class, $documentoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $documentoId = DocumentoId::fromNullableInt(null);
        $this->assertNull($documentoId);
    }

}
