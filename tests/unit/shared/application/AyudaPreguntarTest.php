<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\AyudaPreguntar;

final class AyudaPreguntarTest extends TestCase
{
    private string $docsRoot;

    protected function setUp(): void
    {
        $this->docsRoot = sys_get_temp_dir() . '/orbix_ayuda_' . bin2hex(random_bytes(4));
        mkdir($this->docsRoot . '/manual', 0777, true);
        mkdir($this->docsRoot . '/ai/notas/flujos', 0777, true);

        file_put_contents($this->docsRoot . '/manual/notas.md', <<<'MD'
---
tipo: "manual_usuario"
modulo: "notas"
---

# Manual De Usuario - notas

## Acta

Cómo crear un acta y añadir alumnos al listado.
MD);

        file_put_contents($this->docsRoot . '/ai/notas/flujos/acta_ver.md', <<<'MD'
---
tipo: "ayuda_ia"
titulo: "Acta Ver"
preguntas: ["Como añadir alumno al acta?", "Como ver notas del acta?"]
---

# Ayuda IA - Acta Ver

Pasos para añadir un alumno al acta desde el listado standalone.
MD);
    }

    protected function tearDown(): void
    {
        $this->rmTree($this->docsRoot);
    }

    public function test_pregunta_vacia(): void
    {
        $uc = new AyudaPreguntar($this->docsRoot);
        $r = $uc->execute([]);
        $this->assertSame([], $r['resultados']);
        $this->assertNotSame('', $r['respuesta']);
    }

    public function test_encuentra_acta_en_ai_y_manual(): void
    {
        $uc = new AyudaPreguntar($this->docsRoot);
        $r = $uc->execute(['pregunta' => 'cómo añadir alumno al acta']);
        $this->assertNotSame([], $r['resultados']);
        $fuentes = array_column($r['resultados'], 'fuente');
        $this->assertTrue(
            in_array('docs/ai/notas/flujos/acta_ver.md', $fuentes, true)
            || in_array('docs/manual/notas.md', $fuentes, true)
        );
        $this->assertSame('busqueda_local', $r['modo']);
        $this->assertStringContainsString('documentación local', mb_strtolower($r['respuesta']));
    }

    private function rmTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname());
        }
        rmdir($dir);
    }
}
