<?php

declare(strict_types=1);

namespace Tests\unit\notas\domain\entity;

use PHPUnit\Framework\Attributes\DataProvider;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\NotaMax;
use src\notas\domain\value_objects\NotaNum;
use src\notas\domain\value_objects\NotaSituacion;
use Tests\myTest;

/**
 * `isAprobada()` y `getNota_txt()` de `PersonaNota` y `PersonaNotaOtraRegionStgr` (mismo código en las dos):
 * si la nota cuenta como superada, con la nota de corte de la configuración, y su texto (cum laude, etc.).
 */
final class PersonaNotaAprobadaTextoTest extends myTest
{
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $_SESSION['oConfig'] = self::config('0.6');
    }

    public function tearDown(): void
    {
        $_SESSION['oConfig'] = $this->configAnterior;
        parent::tearDown();
    }

    private static function config(string $notaCorte): ConfigSnapshot
    {
        return new ConfigSnapshot(null, null, null, null, null, null, null, $notaCorte, null, null, 'es_ES.UTF-8', null, null, null, null);
    }

    /** @return iterable<string, array{class-string}> */
    public static function entidadesProvider(): iterable
    {
        yield 'dl' => [PersonaNota::class];
        yield 'otra región' => [PersonaNotaOtraRegionStgr::class];
    }

    /**
     * @param class-string<PersonaNota|PersonaNotaOtraRegionStgr> $clase
     */
    private static function nota(string $clase, int $situacion, ?float $num = null, ?int $max = null): PersonaNota|PersonaNotaOtraRegionStgr
    {
        $nota = new $clase();
        $nota->setIdSituacionVo(new NotaSituacion($situacion));
        if ($num !== null) {
            $nota->setNotaNumVo(new NotaNum($num));
        }
        if ($max !== null) {
            $nota->setNotaMaxVo(new NotaMax($max));
        }

        return $nota;
    }

    /**
     * @param class-string<PersonaNota|PersonaNotaOtraRegionStgr> $clase
     */
    #[DataProvider('entidadesProvider')]
    public function test_situaciones_superadas_y_no_superadas(string $clase): void
    {
        foreach ([NotaSituacion::SUPERADA, NotaSituacion::MAGNA, NotaSituacion::SUMMA, NotaSituacion::CONVALIDADA, NotaSituacion::EXENTO] as $s) {
            $this->assertTrue(self::nota($clase, $s)->isAprobada(), "situación $s");
        }
        foreach ([NotaSituacion::DESCONOCIDO, NotaSituacion::CURSADA, NotaSituacion::PREVISTA_CA, NotaSituacion::NO_HECHA_CA, NotaSituacion::EXAMINADO, NotaSituacion::FALTA_CERTIFICADO] as $s) {
            $this->assertFalse(self::nota($clase, $s)->isAprobada(), "situación $s");
        }
    }

    /**
     * @param class-string<PersonaNota|PersonaNotaOtraRegionStgr> $clase
     */
    #[DataProvider('entidadesProvider')]
    public function test_nota_numerica_segun_la_nota_de_corte(string $clase): void
    {
        $this->assertTrue(self::nota($clase, NotaSituacion::NUMERICA, 6.0, 10)->isAprobada(), 'justo la nota de corte');
        $this->assertFalse(self::nota($clase, NotaSituacion::NUMERICA, 5.9, 10)->isAprobada());
        $this->assertTrue(self::nota($clase, NotaSituacion::NUMERICA, 3.0, 5)->isAprobada(), 'sobre otra nota máxima');
        $this->assertFalse(self::nota($clase, NotaSituacion::NUMERICA, 6.0, 0)->isAprobada(), 'nota máxima 0');
        $this->assertFalse(self::nota($clase, NotaSituacion::NUMERICA)->isAprobada(), 'sin números');

        $_SESSION['oConfig'] = self::config('0.7');
        $this->assertFalse(self::nota($clase, NotaSituacion::NUMERICA, 6.0, 10)->isAprobada(), 'con otra nota de corte');
    }

    /**
     * @param class-string<PersonaNota|PersonaNotaOtraRegionStgr> $clase
     */
    #[DataProvider('entidadesProvider')]
    public function test_sin_configuracion_no_cuenta_como_aprobada(string $clase): void
    {
        unset($_SESSION['oConfig']);

        $this->assertFalse(self::nota($clase, NotaSituacion::SUPERADA)->isAprobada());
    }

    /**
     * @param class-string<PersonaNota|PersonaNotaOtraRegionStgr> $clase
     */
    #[DataProvider('entidadesProvider')]
    public function test_texto_de_magna_summa_y_otras_situaciones(string $clase): void
    {
        $this->assertSame('Magna cum laude (8,6-9,5/10)', self::nota($clase, NotaSituacion::MAGNA)->getNota_txt());
        $this->assertSame('Summa cum laude (9,6-10/10)', self::nota($clase, NotaSituacion::SUMMA)->getNota_txt());
        $this->assertSame('superada', self::nota($clase, NotaSituacion::SUPERADA)->getNota_txt());
        $this->assertSame('cursada', self::nota($clase, NotaSituacion::CURSADA)->getNota_txt());
    }

    /**
     * @return iterable<string, array{float, int, string}>
     */
    public static function textoNumericoProvider(): iterable
    {
        yield 'summa' => [9.6, 10, 'Summa cum laude'];
        yield 'magna' => [9.0, 10, 'Magna cum laude'];
        yield 'cum laude' => [8.0, 10, 'Cum laude'];
        yield 'bene probatus' => [7.0, 10, 'Bene probatus'];
        yield 'probatus' => [6.0, 10, 'Probatus'];
        yield 'non probatus' => [5.9, 10, 'Non probatus'];
        yield 'otra escala' => [4.5, 5, 'Magna cum laude'];
    }

    #[DataProvider('textoNumericoProvider')]
    public function test_texto_de_la_nota_numerica(float $num, int $max, string $calificacion): void
    {
        foreach ([PersonaNota::class, PersonaNotaOtraRegionStgr::class] as $clase) {
            $txt = self::nota($clase, NotaSituacion::NUMERICA, $num, $max)->getNota_txt();
            $this->assertStringStartsWith("$calificacion (", $txt, $clase);
            $this->assertMatchesRegularExpression('#\(' . str_replace('.', '[.,]', (string) $num) . "/$max\)$#", $txt, $clase);
        }
    }

    /**
     * @param class-string<PersonaNota|PersonaNotaOtraRegionStgr> $clase
     */
    #[DataProvider('entidadesProvider')]
    public function test_texto_numerico_sin_calificacion_o_con_datos_erroneos(string $clase): void
    {
        $this->assertSame('0/0', self::nota($clase, NotaSituacion::NUMERICA, 0.0, 0)->getNota_txt(), 'nota máxima 0: solo los números');
        $this->assertStringStartsWith('Error: algún número está mal', self::nota($clase, NotaSituacion::NUMERICA)->getNota_txt());
    }
}
