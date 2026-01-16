<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Pdf;
use Tests\myTest;

class PdfTest extends myTest
{
    public function test_create_valid_pdf()
    {
        $pdf = new Pdf('test value');
        $this->assertEquals('test value', $pdf->value());
    }

    public function test_to_string_returns_pdf_value()
    {
        $pdf = new Pdf('test value');
        $this->assertEquals('test value', (string)$pdf);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $pdf = Pdf::fromNullableString('test value');
        $this->assertInstanceOf(Pdf::class, $pdf);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $pdf = Pdf::fromNullableString(null);
        $this->assertNull($pdf);
    }

}
