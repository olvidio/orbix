<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\DocumentoNumIni;
use Tests\myTest;

class DocumentoNumIniTest extends myTest
{
    public function test_create_valid_documentoNumIni()
    {
        $documentoNumIni = new DocumentoNumIni(123);
        $this->assertEquals(123, $documentoNumIni->value());
    }

    public function test_equals_returns_true_for_same_documentoNumIni()
    {
        $documentoNumIni1 = new DocumentoNumIni(123);
        $documentoNumIni2 = new DocumentoNumIni(123);
        $this->assertTrue($documentoNumIni1->equals($documentoNumIni2));
    }

    public function test_equals_returns_false_for_different_documentoNumIni()
    {
        $documentoNumIni1 = new DocumentoNumIni(123);
        $documentoNumIni2 = new DocumentoNumIni(456);
        $this->assertFalse($documentoNumIni1->equals($documentoNumIni2));
    }

    public function test_to_string_returns_documentoNumIni_value()
    {
        $documentoNumIni = new DocumentoNumIni(123);
        $this->assertEquals(123, (string)$documentoNumIni);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $documentoNumIni = DocumentoNumIni::fromNullableInt(123);
        $this->assertInstanceOf(DocumentoNumIni::class, $documentoNumIni);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $documentoNumIni = DocumentoNumIni::fromNullableInt(null);
        $this->assertNull($documentoNumIni);
    }

}
