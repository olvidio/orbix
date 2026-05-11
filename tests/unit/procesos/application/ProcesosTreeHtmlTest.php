<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use frontend\procesos\support\ProcesosTreeHtml;
use PHPUnit\Framework\TestCase;

/**
 * HTML del árbol a partir de `aPadres` (sin BD).
 */
final class ProcesosTreeHtmlTest extends TestCase
{
    public function test_arbol_simple_sin_hijos_devuelve_entries(): void
    {
        $aPadres = [
            0 => [
                1 => ['id' => 10, 'nom' => 'Fase A'],
                2 => ['id' => 20, 'nom' => 'Fase B'],
            ],
        ];

        $html = ProcesosTreeHtml::dibujarTree($aPadres);

        $this->assertStringStartsWith('<div id="tree">', $html);
        $this->assertStringEndsWith('</div>', $html);
        $this->assertStringContainsString('<span>Fase A</span>', $html);
        $this->assertStringContainsString('<span>Fase B</span>', $html);
        $this->assertSame(2, substr_count($html, 'class="entry"'));
    }

    public function test_arbol_con_hijos_crea_branches_anidadas(): void
    {
        $aPadres = [
            0 => [
                1 => ['id' => 10, 'nom' => 'Fase A'],
            ],
            10 => [
                1 => ['id' => 11, 'nom' => 'Fase A.1'],
                2 => ['id' => 12, 'nom' => 'Fase A.2'],
            ],
        ];

        $html = ProcesosTreeHtml::dibujarTree($aPadres);

        $this->assertStringContainsString('<span>Fase A</span>', $html);
        $this->assertStringContainsString('<span>Fase A.1</span>', $html);
        $this->assertStringContainsString('<span>Fase A.2</span>', $html);
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'class="branch"'));
    }
}
