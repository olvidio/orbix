<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoObserv;
use Tests\myTest;

class DocumentoObservTest extends myTest
{
    public function test_create_valid_documentoObserv()
    {
        $documentoObserv = new DocumentoObserv('test value');
        $this->assertEquals('test value', $documentoObserv->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DocumentoObserv(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_documentoObserv()
    {
        $documentoObserv1 = new DocumentoObserv('test value');
        $documentoObserv2 = new DocumentoObserv('test value');
        $this->assertTrue($documentoObserv1->equals($documentoObserv2));
    }

    public function test_equals_returns_false_for_different_documentoObserv()
    {
        $documentoObserv1 = new DocumentoObserv('test value');
        $documentoObserv2 = new DocumentoObserv('alternative value');
        $this->assertFalse($documentoObserv1->equals($documentoObserv2));
    }

    public function test_to_string_returns_documentoObserv_value()
    {
        $documentoObserv = new DocumentoObserv('test value');
        $this->assertEquals('test value', (string)$documentoObserv);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $documentoObserv = DocumentoObserv::fromNullableString('test value');
        $this->assertInstanceOf(DocumentoObserv::class, $documentoObserv);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $documentoObserv = DocumentoObserv::fromNullableString(null);
        $this->assertNull($documentoObserv);
    }

}
