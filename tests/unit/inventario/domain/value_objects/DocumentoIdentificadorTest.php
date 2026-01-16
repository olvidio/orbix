<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoIdentificador;
use Tests\myTest;

class DocumentoIdentificadorTest extends myTest
{
    public function test_create_valid_documentoIdentificador()
    {
        $documentoIdentificador = new DocumentoIdentificador('test value');
        $this->assertEquals('test value', $documentoIdentificador->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DocumentoIdentificador(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_documentoIdentificador()
    {
        $documentoIdentificador1 = new DocumentoIdentificador('test value');
        $documentoIdentificador2 = new DocumentoIdentificador('test value');
        $this->assertTrue($documentoIdentificador1->equals($documentoIdentificador2));
    }

    public function test_equals_returns_false_for_different_documentoIdentificador()
    {
        $documentoIdentificador1 = new DocumentoIdentificador('test value');
        $documentoIdentificador2 = new DocumentoIdentificador('alternative value');
        $this->assertFalse($documentoIdentificador1->equals($documentoIdentificador2));
    }

    public function test_to_string_returns_documentoIdentificador_value()
    {
        $documentoIdentificador = new DocumentoIdentificador('test value');
        $this->assertEquals('test value', (string)$documentoIdentificador);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $documentoIdentificador = DocumentoIdentificador::fromNullableString('test value');
        $this->assertInstanceOf(DocumentoIdentificador::class, $documentoIdentificador);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $documentoIdentificador = DocumentoIdentificador::fromNullableString(null);
        $this->assertNull($documentoIdentificador);
    }

}
