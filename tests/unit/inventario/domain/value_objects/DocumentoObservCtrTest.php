<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoObservCtr;
use Tests\myTest;

class DocumentoObservCtrTest extends myTest
{
    public function test_create_valid_documentoObservCtr()
    {
        $documentoObservCtr = new DocumentoObservCtr('test value');
        $this->assertEquals('test value', $documentoObservCtr->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DocumentoObservCtr(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_documentoObservCtr()
    {
        $documentoObservCtr1 = new DocumentoObservCtr('test value');
        $documentoObservCtr2 = new DocumentoObservCtr('test value');
        $this->assertTrue($documentoObservCtr1->equals($documentoObservCtr2));
    }

    public function test_equals_returns_false_for_different_documentoObservCtr()
    {
        $documentoObservCtr1 = new DocumentoObservCtr('test value');
        $documentoObservCtr2 = new DocumentoObservCtr('alternative value');
        $this->assertFalse($documentoObservCtr1->equals($documentoObservCtr2));
    }

    public function test_to_string_returns_documentoObservCtr_value()
    {
        $documentoObservCtr = new DocumentoObservCtr('test value');
        $this->assertEquals('test value', (string)$documentoObservCtr);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $documentoObservCtr = DocumentoObservCtr::fromNullableString('test value');
        $this->assertInstanceOf(DocumentoObservCtr::class, $documentoObservCtr);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $documentoObservCtr = DocumentoObservCtr::fromNullableString(null);
        $this->assertNull($documentoObservCtr);
    }

}
