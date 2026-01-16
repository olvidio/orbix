<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoNumReg;
use Tests\myTest;

class DocumentoNumRegTest extends myTest
{
    public function test_create_valid_documentoNumReg()
    {
        $documentoNumReg = new DocumentoNumReg(123);
        $this->assertEquals(123, $documentoNumReg->value());
    }

    public function test_equals_returns_true_for_same_documentoNumReg()
    {
        $documentoNumReg1 = new DocumentoNumReg(123);
        $documentoNumReg2 = new DocumentoNumReg(123);
        $this->assertTrue($documentoNumReg1->equals($documentoNumReg2));
    }

    public function test_equals_returns_false_for_different_documentoNumReg()
    {
        $documentoNumReg1 = new DocumentoNumReg(123);
        $documentoNumReg2 = new DocumentoNumReg(456);
        $this->assertFalse($documentoNumReg1->equals($documentoNumReg2));
    }

    public function test_to_string_returns_documentoNumReg_value()
    {
        $documentoNumReg = new DocumentoNumReg(123);
        $this->assertEquals(123, (string)$documentoNumReg);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $documentoNumReg = DocumentoNumReg::fromNullableInt(123);
        $this->assertInstanceOf(DocumentoNumReg::class, $documentoNumReg);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $documentoNumReg = DocumentoNumReg::fromNullableInt(null);
        $this->assertNull($documentoNumReg);
    }

}
