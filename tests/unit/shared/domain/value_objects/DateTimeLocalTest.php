<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

final class DateTimeLocalTest extends myTest
{
    public function test_create_from_format_and_iso(): void
    {
        $dt = DateTimeLocal::createFromFormat('Y-m-d', '2023-06-15');
        $this->assertInstanceOf(DateTimeLocal::class, $dt);
        $this->assertSame('2023-06-15', $dt->getIso());
        $this->assertStringStartsWith('2023-06-15', $dt->getIsoTime());
    }

    public function test_false_on_invalid_parse(): void
    {
        $dt = DateTimeLocal::createFromFormat('Y-m-d', 'invalid');
        $this->assertFalse($dt);
    }

    public function test_static_meses_latin_has_twelve_keys(): void
    {
        $latin = DateTimeLocal::Meses_latin();
        $this->assertSame('martio', $latin['3']);
        $this->assertCount(12, $latin);
    }

    public function test_duration_between_two_dates(): void
    {
        $start = DateTimeLocal::createFromFormat('Y-m-d', '2024-01-01');
        $this->assertInstanceOf(DateTimeLocal::class, $start);
        $other = DateTimeLocal::createFromFormat('Y-m-d', '2024-01-03');
        $this->assertInstanceOf(DateTimeLocal::class, $other);
        $dur = $start->duracion($other);
        $this->assertGreaterThanOrEqual(1.9, $dur);
    }

    /**
     * Fechas imposibles (duda 9 de la sección 6b del plan): antes «31/02/2026» se guardaba como
     * 3 de marzo y «31/31/2026» como julio de 2028.
     */
    #[DataProvider('fechasLocales')]
    public function test_create_from_local_rechaza_las_fechas_imposibles(string $idioma, string $texto, string|false $esperado): void
    {
        $antes = $_SESSION['session_auth']['idioma'] ?? null;
        try {
            $_SESSION['session_auth']['idioma'] = $idioma;
            $dt = DateTimeLocal::createFromLocal($texto);
            $this->assertSame($esperado, $dt === false ? false : $dt?->getIso());
        } finally {
            $_SESSION['session_auth']['idioma'] = $antes;
        }
    }

    /** @return iterable<string, array{string, string, string|false}> */
    public static function fechasLocales(): iterable
    {
        yield 'válida' => ['es_ES.UTF-8', '15/6/2031', '2031-06-15'];
        yield 'con ceros y guiones' => ['ca_ES.UTF-8', '01-02-2026', '2026-02-01'];
        yield '29 de febrero bisiesto' => ['es_ES.UTF-8', '29/2/2024', '2024-02-29'];
        yield 'año de dos cifras' => ['es_ES.UTF-8', '1/3/26', '2026-03-01'];
        yield '31 de febrero' => ['es_ES.UTF-8', '31/02/2026', false];
        yield '29 de febrero no bisiesto' => ['es_ES.UTF-8', '29/2/2025', false];
        yield 'mes 31' => ['es_ES.UTF-8', '31/31/2026', false];
        yield 'mes 13' => ['es_ES.UTF-8', '1/13/2026', false];
        yield 'inglés válida' => ['en_US.UTF-8', '6/15/2031', '2031-06-15'];
        yield 'inglés con día y mes al revés' => ['en_US.UTF-8', '15/6/2031', false];
        yield 'texto' => ['es_ES.UTF-8', 'mañana', false];
    }
}
