<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaTextoChars;
use Tests\myTest;

final class PersonaTextoCharsTest extends myTest
{
    public function test_normalize_tipografico_sustituye_guiones_y_comillas(): void
    {
        $this->assertSame('a-b-c-d', PersonaTextoChars::normalizeTipografico("a\u{2013}b\u{2014}c\u{2212}d"));
        $this->assertSame("d'a'b'c'e", PersonaTextoChars::normalizeTipografico("d\u{2018}a\u{2019}b\u{201B}c`e"));
    }

    public function test_normalize_tipografico_compone_unicode_nfc(): void
    {
        $descompuesto = "e\u{0301}"; // e + acento combinante
        $this->assertSame("\u{00E9}", PersonaTextoChars::normalizeTipografico($descompuesto));
    }

    public function test_normalize_tipografico_no_toca_texto_normal(): void
    {
        $this->assertSame('María-José O\'Brien', PersonaTextoChars::normalizeTipografico('María-José O\'Brien'));
        $this->assertSame('', PersonaTextoChars::normalizeTipografico(''));
    }

    public function test_full_pattern(): void
    {
        $this->assertSame('/^[A-Za-z0-9 ]+$/u', PersonaTextoChars::fullPattern(PersonaTextoChars::CLASE_NX));
    }

    /**
     * @dataProvider textosPersonaValidosProvider
     */
    public function test_clase_texto_persona_acepta(string $valor): void
    {
        PersonaTextoChars::throwsIfNotMatching('X', $valor, PersonaTextoChars::CLASE_TEXTO_PERSONA);
        $this->addToAssertionCount(1);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function textosPersonaValidosProvider(): iterable
    {
        yield 'con acentos y ñ' => ['José Ñandú'];
        yield 'apóstrofo tipográfico' => ['D’Angelo'];
        yield 'guion y paréntesis' => ['Pérez-Gil (hijo)'];
        yield 'punto, coma y barra' => ['Dr. A, B/C'];
        yield 'con números' => ['Juan 2'];
        yield 'punto medio y asterisco' => ['Mol·la *'];
    }

    /**
     * @dataProvider textosPersonaInvalidosProvider
     */
    public function test_clase_texto_persona_rechaza(string $valor): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PersonaTextoChars::throwsIfNotMatching('Nombre', $valor, PersonaTextoChars::CLASE_TEXTO_PERSONA);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function textosPersonaInvalidosProvider(): iterable
    {
        yield 'vacío (el patrón exige al menos un carácter)' => [''];
        yield 'arroba' => ['a@b'];
        yield 'punto y coma' => ['a;b'];
        yield 'salto de línea' => ["a\nb"];
        yield 'emoji' => ['Juan 😀'];
    }

    public function test_clase_nx(): void
    {
        PersonaTextoChars::throwsIfNotMatching('Nx', 'AB 12', PersonaTextoChars::CLASE_NX);
        $this->expectException(\InvalidArgumentException::class);
        PersonaTextoChars::throwsIfNotMatching('Nx', 'Ñ', PersonaTextoChars::CLASE_NX);
    }

    public function test_clase_tabla_code(): void
    {
        PersonaTextoChars::throwsIfNotMatching('T', 'p_nax_2', PersonaTextoChars::CLASE_TABLA_CODE);
        $this->expectException(\InvalidArgumentException::class);
        PersonaTextoChars::throwsIfNotMatching('T', 'p-nax', PersonaTextoChars::CLASE_TABLA_CODE);
    }

    public function test_clase_situacion(): void
    {
        PersonaTextoChars::throwsIfNotMatching('S', 'A_B (+)', PersonaTextoChars::CLASE_SITUACION);
        $this->expectException(\InvalidArgumentException::class);
        PersonaTextoChars::throwsIfNotMatching('S', 'A;B', PersonaTextoChars::CLASE_SITUACION);
    }

    public function test_clase_trato_es_permisiva_pero_no_acepta_vacio(): void
    {
        PersonaTextoChars::throwsIfNotMatching('Trato', 'Sr. D.', PersonaTextoChars::CLASE_TRATO);
        $this->expectException(\InvalidArgumentException::class);
        PersonaTextoChars::throwsIfNotMatching('Trato', '', PersonaTextoChars::CLASE_TRATO);
    }

    public function test_mensaje_de_error_incluye_vo_valor_y_caracteres_no_permitidos(): void
    {
        try {
            PersonaTextoChars::throwsIfNotMatching('Nombre', 'a@b', PersonaTextoChars::CLASE_TEXTO_PERSONA);
            $this->fail('Debía lanzar excepción');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'Nombre has invalid characters (value="a@b"; no permitidos: "@" (U+0040))',
                $e->getMessage()
            );
        }
    }

    public function test_disallowed_chars_summary_sin_duplicados_y_en_orden(): void
    {
        $this->assertSame(
            '"@" (U+0040), ";" (U+003B)',
            PersonaTextoChars::disallowedCharsSummary('a@b@;', PersonaTextoChars::CLASE_TEXTO_PERSONA)
        );
    }

    public function test_disallowed_chars_summary_sin_detalle_si_todo_es_valido(): void
    {
        $this->assertSame('(sin detalle)', PersonaTextoChars::disallowedCharsSummary('abc', PersonaTextoChars::CLASE_TEXTO_PERSONA));
    }

    public function test_safe_repr_delega_en_value_object_messages(): void
    {
        $this->assertSame('"ñ"', PersonaTextoChars::safeRepr('ñ'));
    }
}
