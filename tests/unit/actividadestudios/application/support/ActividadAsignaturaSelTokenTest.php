<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application\support;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\support\ActividadAsignaturaSelToken;

final class ActividadAsignaturaSelTokenTest extends TestCase
{
    public function test_encode_incluye_id_schema(): void
    {
        $this->assertSame(
            '10#1101#true#1001',
            ActividadAsignaturaSelToken::encode(10, 1101, true, 1001),
        );
        $this->assertSame(
            '10#1101#false#2002',
            ActividadAsignaturaSelToken::encode(10, 1101, false, 2002),
        );
    }

    public function test_decode_distingue_esquemas(): void
    {
        $propia = ActividadAsignaturaSelToken::decode('10#1101#true#1001');
        $otra = ActividadAsignaturaSelToken::decode('10#1101#false#2002');

        $this->assertSame(10, $propia['id_activ']);
        $this->assertSame(1101, $propia['id_asignatura']);
        $this->assertTrue($propia['editable']);
        $this->assertSame(1001, $propia['id_schema']);

        $this->assertSame(10, $otra['id_activ']);
        $this->assertSame(1101, $otra['id_asignatura']);
        $this->assertFalse($otra['editable']);
        $this->assertSame(2002, $otra['id_schema']);
        $this->assertNotSame($propia['id_schema'], $otra['id_schema']);
    }

    public function test_decode_legacy_sin_schema(): void
    {
        $parsed = ActividadAsignaturaSelToken::decode('10#1101#true');
        $this->assertSame(10, $parsed['id_activ']);
        $this->assertSame(1101, $parsed['id_asignatura']);
        $this->assertTrue($parsed['editable']);
        $this->assertSame(0, $parsed['id_schema']);
    }
}
