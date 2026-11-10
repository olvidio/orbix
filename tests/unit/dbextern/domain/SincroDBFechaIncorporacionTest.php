<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain;

use PHPUnit\Framework\TestCase;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\domain\SincroDB;

/** Regresión: `f_inc` de la BDU llega en ISO y nunca se sincronizaba (pasaba por `createFromLocal`). */
final class SincroDBFechaIncorporacionTest extends TestCase
{
    /**
     * @dataProvider fechasValidasProvider
     */
    public function test_la_fecha_iso_de_la_bdu_se_convierte(string $iso, string $local): void
    {
        $f = SincroDB::fechaIncorporacion($iso);

        $this->assertNotNull($f);
        $this->assertSame($local, $f->getFromLocal());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function fechasValidasProvider(): iterable
    {
        yield 'con ceros' => ['2001-03-15', '15/3/2001'];
        yield 'sin ceros' => ['2001-3-5', '5/3/2001'];
        yield 'con espacios' => [' 1999-12-31 ', '31/12/1999'];
        yield 'bisiesto' => ['2024-02-29', '29/2/2024'];
    }

    /**
     * @dataProvider fechasInvalidasProvider
     */
    public function test_vacia_o_invalida_es_null(string $valor): void
    {
        $this->assertNull(SincroDB::fechaIncorporacion($valor));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function fechasInvalidasProvider(): iterable
    {
        yield 'vacía' => [''];
        yield 'solo guiones' => ['--'];
        yield 'formato local' => ['15/03/2001'];
        yield 'día inexistente' => ['2001-02-31'];
        yield 'no bisiesto' => ['2023-02-29'];
        yield 'mes 13' => ['2001-13-01'];
        yield 'texto' => ['pendiente'];
    }

    public function test_la_incorporacion_de_la_bdu_llega_como_fecha_utilizable(): void
    {
        $p = new PersonaBDU();
        $p->setIncorporacion('sc 15-03-2001');

        $this->assertSame('sc', $p->getInc());
        $this->assertSame('15/3/2001', SincroDB::fechaIncorporacion($p->getF_inc())?->getFromLocal());
    }

    public function test_sin_incorporacion_no_hay_fecha(): void
    {
        $p = new PersonaBDU();
        $p->setIncorporacion('');

        $this->assertNull(SincroDB::fechaIncorporacion($p->getF_inc()));
    }
}
