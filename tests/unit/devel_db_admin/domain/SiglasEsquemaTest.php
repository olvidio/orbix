<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\domain;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\devel_db_admin\domain\SiglasEsquema;

/**
 * Siglas de región y dl, y nombres de esquema, que las herramientas de esquemas usan para montar nombres de
 * esquema y de rol y literales de DDL (revisión de seguridad del 2026-11-18).
 */
final class SiglasEsquemaTest extends TestCase
{
    /** @return iterable<string, array{string}> */
    public static function validas(): iterable
    {
        yield 'dl' => ['dlb'];
        yield 'con mayúscula' => ['dlmE'];
        yield 'región' => ['H'];
        yield 'con más' => ['crs+'];
        yield 'con guion bajo' => ['dl_x'];
    }

    #[DataProvider('validas')]
    public function test_una_sigla_valida_se_acepta(string $sigla): void
    {
        SiglasEsquema::validarSiglas($sigla, $sigla);
        $this->addToAssertionCount(1);
    }

    /** @return iterable<string, array{string}> */
    public static function noValidas(): iterable
    {
        yield 'vacía' => [''];
        yield 'comilla' => ["x', 'g') OR true --"];
        yield 'comillas dobles' => ['x"; DROP TABLE foo; --'];
        yield 'dólar' => ['x$$'];
        yield 'espacio' => ['dl b'];
        yield 'guion' => ['dl-b'];
        yield 'punto y coma' => ['dlb;'];
    }

    #[DataProvider('noValidas')]
    public function test_una_sigla_no_valida_se_rechaza(string $sigla): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SiglasEsquema::validarSiglas('H', $sigla);
    }

    #[DataProvider('noValidas')]
    public function test_una_region_no_valida_se_rechaza(string $sigla): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SiglasEsquema::validarSiglas($sigla, 'dlb');
    }

    /** @return iterable<string, array{string, bool}> */
    public static function esquemas(): iterable
    {
        yield 'base' => ['H-dlb', true];
        yield 'interior' => ['H-dlbv', true];
        yield 'región stgr' => ['Galbel-crGalbelv', true];
        yield 'público' => ['publicv', true];
        yield 'con comilla' => ["x') OR true; --v", false];
        yield 'con comillas dobles' => ['H-dlb"', false];
        yield 'vacío' => ['', false];
    }

    #[DataProvider('esquemas')]
    public function test_nombre_de_esquema(string $esquema, bool $valido): void
    {
        if (!$valido) {
            $this->expectException(\InvalidArgumentException::class);
        }
        SiglasEsquema::validarEsquema($esquema);
        $this->addToAssertionCount(1);
    }
}
