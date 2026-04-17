<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosGet;

/**
 * Test unitario del método público estático ProcesosGet::dibujarTree(),
 * que no depende de BD y se puede probar con arrays sintéticos.
 */
final class ProcesosGetDibujarTreeTest extends TestCase
{
    public function test_arbol_simple_sin_hijos_devuelve_entries(): void
    {
        $aPadres = [
            0 => [
                1 => ['id' => 10, 'nom' => 'Fase A'],
                2 => ['id' => 20, 'nom' => 'Fase B'],
            ],
        ];

        $html = ProcesosGet::dibujarTree($aPadres);

        $this->assertStringStartsWith('<div id="tree">', $html);
        $this->assertStringEndsWith('</div>', $html);
        $this->assertStringContainsString('<span>Fase A</span>', $html);
        $this->assertStringContainsString('<span>Fase B</span>', $html);
        // Sin hijos las fases se marcan directamente como entry sin branch interna
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

        $html = ProcesosGet::dibujarTree($aPadres);

        $this->assertStringContainsString('<span>Fase A</span>', $html);
        $this->assertStringContainsString('<span>Fase A.1</span>', $html);
        $this->assertStringContainsString('<span>Fase A.2</span>', $html);
        // La raíz tiene una branch interna que contiene a los hijos
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'class="branch"'));
    }
}
