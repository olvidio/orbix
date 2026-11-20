<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\TestCase;

/**
 * Las descargas llevan datos personales (actas, certificados, listados): ninguna se declara cacheable
 * por intermediarios con `Cache-Control: public` (revisión de seguridad del 2026-11-20, CFG-13).
 */
final class DescargasSinCachePublicaTest extends TestCase
{
    public function test_ninguna_respuesta_se_declara_publica(): void
    {
        $raiz = dirname(__DIR__, 4);
        $encontrados = [];
        foreach (['src', 'frontend', 'public'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/' . $dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $fichero) {
                if (str_ends_with((string) $fichero, '.php') && preg_match('/Cache-Control:\s*public/i', (string) file_get_contents((string) $fichero)) === 1) {
                    $encontrados[] = substr((string) $fichero, strlen($raiz) + 1);
                }
            }
        }

        $this->assertSame([], $encontrados);
    }
}
