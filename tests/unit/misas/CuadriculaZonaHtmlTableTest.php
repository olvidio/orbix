<?php

declare(strict_types=1);

use frontend\misas\support\CuadriculaZonaHtmlTable;
use PHPUnit\Framework\TestCase;

final class CuadriculaZonaHtmlTableTest extends TestCase
{
    public function testRenderSkipsTitleRowAndAppliesColors(): void
    {
        $rows = [
            [
                'encargo' => 'Misa dominical',
                '2026-05-01' => 'ABC',
                'meta' => [
                    '2026-05-01' => ['color' => 'verde', 'texto' => 'ok', 'tipo' => 'misas'],
                ],
            ],
            [
                'encargo' => 'Sacerdotes',
                'color_encargo' => 'titulo',
                '2026-05-01' => 'V 1.5',
                'meta' => [
                    '2026-05-01' => ['tipo' => 'titulo', 'texto' => '2026-05-01'],
                ],
            ],
            [
                'encargo' => 'P. Juan',
                '2026-05-01' => 'SI',
                'meta' => [
                    '2026-05-01' => ['color' => 'amarillo', 'texto' => 'dos misas', 'tipo' => 'sacd'],
                ],
            ],
        ];

        $html = CuadriculaZonaHtmlTable::render($rows);

        self::assertStringContainsString('<table class="cuadricula_zona">', $html);
        self::assertStringContainsString('<th>V 1.5</th>', $html);
        self::assertStringContainsString('Misa dominical', $html);
        self::assertStringContainsString('class="verde"', $html);
        self::assertStringContainsString('class="amarillo"', $html);
        self::assertStringContainsString('title="dos misas"', $html);
        self::assertStringNotContainsString('Sacerdotes', $html);
    }

    public function testRenderUsesColumnDefinitionsWhenNoTitleRow(): void
    {
        $columns = json_encode([
            ['field' => 'encargo', 'name' => 'Encargo'],
            ['field' => '2026-05-02', 'name' => 'L'],
        ], JSON_THROW_ON_ERROR);

        $rows = [
            [
                'encargo' => 'Oficio',
                '2026-05-02' => '--',
                'meta' => ['2026-05-02' => ['tipo' => 'misas', 'color' => '', 'texto' => '']],
            ],
        ];

        $html = CuadriculaZonaHtmlTable::render($rows, $columns);

        self::assertStringContainsString('<th>Encargo</th>', $html);
        self::assertStringContainsString('<th>L</th>', $html);
        self::assertStringContainsString('Oficio', $html);
    }
}
