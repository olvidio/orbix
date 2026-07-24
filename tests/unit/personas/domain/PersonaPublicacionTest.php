<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain;

use PHPUnit\Framework\TestCase;
use src\personas\domain\PersonaPublicacion;

final class PersonaPublicacionTest extends TestCase
{
    public function test_fecha_hasta_default_es_un_mes(): void
    {
        $desde = new \DateTimeImmutable('2026-01-15 12:00:00+00');
        $hasta = PersonaPublicacion::fechaHastaDefault($desde);

        $this->assertSame('2026-02-15', $hasta->format('Y-m-d'));
    }

    public function test_normalizar_dl_quita_sufijo_sv_sf(): void
    {
        $this->assertSame('dlb', PersonaPublicacion::normalizarDl('dlbv'));
        $this->assertSame('dlb', PersonaPublicacion::normalizarDl('dlbf'));
        $this->assertSame('dlb', PersonaPublicacion::normalizarDl('dlb'));
        $this->assertSame('*', PersonaPublicacion::normalizarDl('*'));
    }

    public function test_from_pg_to_pg_via_converter_json(): void
    {
        $mapa = PersonaPublicacion::fromPg('{"dlpv":"2026-08-03 12:00:00+00:00"}');
        $this->assertSame(['dlp' => '2026-08-03 12:00:00+00:00'], $mapa);

        $json = PersonaPublicacion::toPg($mapa);
        $this->assertIsString($json);
        $this->assertStringContainsString('"dlp"', $json);
        $this->assertNull(PersonaPublicacion::toPg([]));
    }

    public function test_mapa_from_jsonb_objeto_y_array_legado(): void
    {
        $mapa = PersonaPublicacion::mapaFromJsonb([
            'dlp' => '2026-07-13T00:00:00+00:00',
            'crArg' => '2026-08-03T12:00:00+00:00',
        ]);
        $this->assertSame('2026-07-13T00:00:00+00:00', $mapa['dlp']);
        $this->assertSame('2026-08-03T12:00:00+00:00', $mapa['crArg']);

        $legado = PersonaPublicacion::mapaFromJsonb(['dlpv', 'bcn']);
        $this->assertArrayHasKey('dlp', $legado);
        $this->assertNull($legado['dlp']);
        $this->assertArrayHasKey('bcn', $legado);
    }

    public function test_merge_destino_caducidad_por_dl_independiente(): void
    {
        $mapa = [];
        $mapa = PersonaPublicacion::mergeDestino(
            $mapa,
            'dlp',
            new \DateTimeImmutable('2026-07-13 00:00:00+00'),
        );
        $mapa = PersonaPublicacion::mergeDestino(
            $mapa,
            'crArg',
            new \DateTimeImmutable('2026-08-03 00:00:00+00'),
        );

        $this->assertSame('2026-07-13 00:00:00+00:00', $mapa['dlp']);
        $this->assertSame('2026-08-03 00:00:00+00:00', $mapa['crArg']);

        // No acorta dlp si ya tenía fecha posterior.
        $mapa = PersonaPublicacion::mergeDestino(
            $mapa,
            'dlp',
            new \DateTimeImmutable('2026-06-01 00:00:00+00'),
        );
        $this->assertSame('2026-07-13 00:00:00+00:00', $mapa['dlp']);
    }

    public function test_merge_asterisco_sin_caducidad(): void
    {
        $mapa = PersonaPublicacion::mergeDestino([], '*', null);
        $this->assertNull($mapa['*']);
        $json = PersonaPublicacion::mapaToJson($mapa);
        $this->assertSame('{"*":null}', $json);
    }

    public function test_texto_vigentes_omite_caducadas(): void
    {
        $texto = PersonaPublicacion::textoVigentes([
            'dlp' => '2026-07-13T00:00:00+00:00',
            'crArg' => '2026-08-03T00:00:00+00:00',
            'bcn' => '2020-01-01T00:00:00+00:00',
        ], new \DateTimeImmutable('2026-07-20 12:00:00+00'));

        $this->assertStringContainsString('crArg', $texto);
        $this->assertStringContainsString('03/08/2026', $texto);
        $this->assertStringNotContainsString('dlp', $texto);
        $this->assertStringNotContainsString('bcn', $texto);
    }

    public function test_sql_publicacion_vigente_usa_mapa_objeto(): void
    {
        $sql = PersonaPublicacion::sqlPublicacionVigente('p');

        $this->assertStringContainsString('p.publicado_para', $sql);
        $this->assertStringContainsString('jsonb_each_text', $sql);
        $this->assertStringContainsString("jsonb_typeof(p.publicado_para) = 'object'", $sql);
        $this->assertStringNotContainsString('publicado_hasta', $sql);
    }

    public function test_sql_vigente_para_dl(): void
    {
        $sql = PersonaPublicacion::sqlVigenteParaDl("'dlp'", "'*'");

        $this->assertStringContainsString("jsonb_exists(publicado_para, 'dlp')", $sql);
        $this->assertStringContainsString("jsonb_exists(publicado_para, '*')", $sql);
        $this->assertStringContainsString('::timestamptz > now()', $sql);
        $this->assertStringNotContainsString('publicado_para ?', $sql);
    }
}
