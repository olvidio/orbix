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

    /**
     * El PDF firmado es contenido binario: se guarda byte a byte. Recortar los espacios y saltos de
     * línea de los extremos (el final habitual de un PDF es «%%EOF» + salto de línea) deja en la BD
     * un fichero distinto del que se subió, y una firma digital que cubra el fichero entero deja de
     * ser válida.
     */
    public function test_el_contenido_binario_se_conserva_byte_a_byte(): void
    {
        $contenido = "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n";

        $this->assertSame($contenido, (new Pdf($contenido))->value());
        $this->assertSame($contenido, Pdf::fromNullableString($contenido)?->value());
        $this->assertSame("\0\x01binario\0", Pdf::fromNullableString("\0\x01binario\0")?->value(), 'tampoco se quitan los bytes nulos');
    }

    public function test_fromNullableString_returns_null_for_empty_value(): void
    {
        $this->assertNull(Pdf::fromNullableString(''));
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $pdf = Pdf::fromNullableString(null);
        $this->assertNull($pdf);
    }

}
