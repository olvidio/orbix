<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\TestCase;

/**
 * Test-guardia: en `libs/` (fuera de `vendor`) no hay ningún fichero PHP que lea la petición ni que
 * lance órdenes del sistema. Un `.php` de `libs/` lo sirve el servidor web directamente, sin el arranque
 * común (ni sesión ni firma): lo que atienda una petición va en un controlador de `frontend/` o `src/`.
 *
 * Revisión de seguridad del 2026-11-20 (SES-01).
 */
final class LibsSinEntradaDeLaPeticionTest extends TestCase
{
    public function test_ningun_php_de_libs_lee_la_peticion_ni_lanza_ordenes(): void
    {
        $raiz = (string) realpath(__DIR__ . '/../../../..');
        $malos = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/libs', \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            /** @var \SplFileInfo $f */
            $ruta = $f->getPathname();
            if ($f->getExtension() !== 'php' || str_contains($ruta, '/libs/vendor/') || str_contains($ruta, '/libs/private/')) {
                continue;
            }
            $codigo = (string) file_get_contents($ruta);
            if (preg_match('/\$_(POST|GET|REQUEST|COOKIE|FILES)\b|\b(shell_exec|exec|system|passthru|proc_open|popen)\s*\(/', $codigo) === 1) {
                $malos[] = substr($ruta, strlen($raiz) + 1);
            }
        }

        $this->assertSame([], $malos);
    }
}
