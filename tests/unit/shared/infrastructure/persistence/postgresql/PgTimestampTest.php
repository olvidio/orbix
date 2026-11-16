<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence\postgresql;

use PHPUnit\Framework\TestCase;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\persistence\postgresql\PgTimestamp;

final class PgTimestampTest extends TestCase
{
    public function test_toPg_devuelve_null_con_fecha_nula(): void
    {
        $converter = new PgTimestamp(null);

        $this->assertNull($converter->toPg('date'));
        $this->assertNull($converter->toPgStandardFormat());
    }

    public function test_toPg_devuelve_null_con_hora_nula(): void
    {
        $converter = new PgTimestamp(null);

        $this->assertNull($converter->toPg('time'));
    }

    public function test_toPg_formatea_fecha_real(): void
    {
        $converter = new PgTimestamp(new DateTimeLocal('2024-03-15'));

        $this->assertSame('2024-03-15', $converter->toPg('date'));
    }

    public function test_toPg_de_cada_tipo_con_un_datetime_cualquiera(): void
    {
        $fecha = new \DateTimeImmutable('2026-11-16 10:30:05', new \DateTimeZone(date_default_timezone_get()));

        $this->assertSame('2026-11-16', (new PgTimestamp($fecha))->toPg('date'));
        $this->assertSame('10:30:05', (new PgTimestamp($fecha))->toPg('time'));
        $this->assertSame($fecha->format('Y-m-d H:i:s.uP'), (new PgTimestamp($fecha))->toPg('timestamp'));
        $this->assertNull((new PgTimestamp($fecha))->toPg('timestamptz'), 'tipo no soportado en toPg');
        $this->assertSame($fecha->format('Y-m-d H:i:s.uP'), (new PgTimestamp($fecha))->toPgStandardFormat());
        $this->assertNull((new PgTimestamp(null))->toPgStandardFormat());
    }

    public function test_fromPg_de_cada_tipo(): void
    {
        $this->assertSame('2026-11-16', (new PgTimestamp('2026-11-16'))->fromPg('date')?->format('Y-m-d'));
        $this->assertSame('2026-11-16 10:30:00', (new PgTimestamp('2026-11-16 10:30:00'))->fromPg('timestamp')?->format('Y-m-d H:i:s'));
        $this->assertSame('10:30:00', (new PgTimestamp('10:30:00'))->fromPg('time')?->format('H:i:s'));
        $this->assertNull((new PgTimestamp(null))->fromPg('date'));
    }

    public function test_fromPg_con_un_tipo_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\Exception::class);
        (new PgTimestamp('2026-11-16'))->fromPg('interval');
    }

    public function test_toPg_con_un_valor_que_no_es_fecha_lanza_excepcion(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot convert data from invalid datetime representation');
        (new PgTimestamp(['no', 'es', 'fecha']))->toPg('date');
    }

    /**
     * Hallazgo: la rama que acepta una hora en texto «HH:MM» no funciona: le añade los segundos y la pasa a
     * `DateTimeLocal::createFromLocal()`, que espera una fecha en el formato del idioma, y acaba en excepción. No hay
     * ningún caso que llegue aquí (las entidades guardan `TimeLocal`, que se acepta tal cual).
     */
    public function test_una_hora_en_texto_no_se_convierte(): void
    {
        $this->expectException(\Exception::class);
        (new PgTimestamp('10:30'))->toPg('time');
    }
}

