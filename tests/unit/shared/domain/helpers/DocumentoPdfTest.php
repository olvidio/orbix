<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\helpers;

use PHPUnit\Framework\TestCase;
use src\shared\domain\helpers\DocumentoPdf;

final class DocumentoPdfTest extends TestCase
{
    public function test_reconoce_la_cabecera_pdf(): void
    {
        $this->assertTrue(DocumentoPdf::esPdf("%PDF-1.4\n1 0 obj<<>>endobj\n%%EOF"));
        $this->assertTrue(DocumentoPdf::esPdf(str_repeat("\0", 500) . '%PDF-1.7'), 'con basura delante (hasta 1024 bytes)');
    }

    public function test_rechaza_lo_que_no_es_pdf(): void
    {
        $this->assertFalse(DocumentoPdf::esPdf('texto plano'));
        $this->assertFalse(DocumentoPdf::esPdf(''));
        $this->assertFalse(DocumentoPdf::esPdf("\xFF\xD8\xFF\xE0JFIF"), 'jpeg');
        $this->assertFalse(DocumentoPdf::esPdf(str_repeat('x', 1024) . '%PDF-1.4'), 'cabecera más allá del byte 1024');
    }
}
