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

    // ---- comprobarSolapes (periodos de una casa) ------------------------------------------------

    /** @return array{inicio: DateTimeLocal, fin: DateTimeLocal} */
    private function periodo(string $inicio, string $fin): array
    {
        return ['inicio' => new DateTimeLocal($inicio), 'fin' => new DateTimeLocal($fin)];
    }

    private function local(string $iso): string
    {
        return (new DateTimeLocal($iso))->getFromLocal();
    }

    public function test_periodos_seguidos_no_dan_aviso(): void
    {
        $this->assertFalse((new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-01-31'),
            $this->periodo('2026-02-01', '2026-06-30'),
            $this->periodo('2026-07-01', '2026-12-31'),
        ]));
        $this->assertFalse((new DateTimeLocal())->comprobarSolapes([]));
    }

    public function test_dias_libres_y_solapes_entre_periodos(): void
    {
        $this->assertSame(sprintf('dias libres cerca de %s', $this->local('2026-01-10')), (new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-01-10'),
            $this->periodo('2026-01-15', '2026-01-31'),
        ]));
        $this->assertSame(sprintf('hay un solape cerca de %s', $this->local('2026-01-10')), (new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-01-10'),
            $this->periodo('2026-01-10', '2026-01-31'),
        ]));
    }

    /** Un hueco o un solape de más de un mes también cuenta (antes solo se miraban los días sueltos del intervalo). */
    public function test_dias_libres_y_solapes_de_mas_de_un_mes(): void
    {
        // Intervalo de «1 mes y 1 día»: los días sueltos (1) parecen periodos seguidos.
        $this->assertSame(sprintf('dias libres cerca de %s', $this->local('2026-01-15')), (new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-01-15'),
            $this->periodo('2026-02-16', '2026-03-31'),
        ]));
        // Intervalo de «1 mes y 0 días»: los días sueltos (0) parecían un solape.
        $this->assertSame(sprintf('dias libres cerca de %s', $this->local('2026-01-10')), (new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-01-10'),
            $this->periodo('2026-02-10', '2026-03-31'),
        ]));
        $this->assertSame(sprintf('hay un solape cerca de %s', $this->local('2026-03-15')), (new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-03-15'),
            $this->periodo('2026-02-14', '2026-04-30'),
        ]));
    }

    public function test_fecha_fin_igual_o_anterior_a_la_de_inicio(): void
    {
        $this->assertSame(sprintf('la fecha fin es igual a la fecha inicio en el periodo 1: %s', $this->local('2026-01-05')),
            (new DateTimeLocal())->comprobarSolapes([$this->periodo('2026-01-05', '2026-01-05')]));
        $this->assertSame(sprintf('la fecha fin es menor que la fecha inicio en el periodo 1: %s', $this->local('2026-01-05')),
            (new DateTimeLocal())->comprobarSolapes([$this->periodo('2026-01-05', '2026-01-02')]));
    }

    public function test_varios_avisos_se_separan_con_br(): void
    {
        $txt = (new DateTimeLocal())->comprobarSolapes([
            $this->periodo('2026-01-01', '2026-01-10'),
            $this->periodo('2026-01-20', '2026-01-15'),
        ]);

        $this->assertSame(sprintf('dias libres cerca de %s<br>la fecha fin es menor que la fecha inicio en el periodo 2: %s',
            $this->local('2026-01-10'), $this->local('2026-01-20')), $txt);
    }

    public function test_fecha_en_latin(): void
    {
        $this->assertSame('die 16 mense  novembri  anno  2026', (new DateTimeLocal('2026-11-16'))->getFechaLatin());
        $this->assertSame('die 1 mense  ianuario  anno  2027', (new DateTimeLocal('2027-01-01'))->getFechaLatin());
    }
}

