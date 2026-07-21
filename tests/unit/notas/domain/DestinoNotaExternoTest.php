<?php

namespace Tests\unit\notas\domain;

use PHPUnit\Framework\TestCase;
use src\notas\domain\DestinoNotaExterno;

class DestinoNotaExternoTest extends TestCase
{
    public function test_id_nom_negativo_es_externo(): void
    {
        $this->assertTrue(DestinoNotaExterno::esExterno(-1001));
    }

    public function test_esquema_resto_es_externo(): void
    {
        $this->assertTrue(DestinoNotaExterno::esExterno(1001, 'restov'));
        $this->assertTrue(DestinoNotaExterno::esExterno(1001, 'restof'));
    }

    public function test_alumno_orbix_no_es_externo(): void
    {
        $this->assertFalse(DestinoNotaExterno::esExterno(100111832, 'H-dlbv'));
        $this->assertFalse(DestinoNotaExterno::esExterno(1036156, 'Galbel-crGalbelv'));
    }
}
