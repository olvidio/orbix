<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Los scripts de línea de órdenes del repositorio (raíz, `tools/`, `proves/`, `docs/scripts/`) no hacen
 * nada si se piden por el servidor web. Con un servidor que sirva todo el árbol del proyecto se ejecutaban
 * por URL sin sesión, y varios reescriben código o lanzan herramientas (revisión de seguridad del
 * 2026-11-19, AUT-06). Los ficheros que solo declaran clases (con `namespace`) no ejecutan nada al cargarse.
 */
final class ScriptsSoloLineaDeOrdenesTest extends TestCase
{
    private const RAIZ = __DIR__ . '/../../../..';

    /** @return iterable<string, array{string}> */
    public static function scripts(): iterable
    {
        $raiz = (string) realpath(self::RAIZ);
        $ficheros = array_filter(glob($raiz . '/*.php') ?: [], static fn (string $f): bool => basename($f) !== 'index.php');
        foreach (['tools', 'proves', 'docs/scripts'] as $dir) {
            if (!is_dir($raiz . '/' . $dir)) {
                continue;
            }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/' . $dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                $ruta = $f->getPathname();
                if ($f->getExtension() === 'php' && !str_contains($ruta, '/.venv/') && !str_contains($ruta, '/node_modules/')) {
                    $ficheros[] = $ruta;
                }
            }
        }
        sort($ficheros);
        foreach ($ficheros as $f) {
            yield substr($f, strlen($raiz) + 1) => [$f];
        }
    }

    #[DataProvider('scripts')]
    public function test_el_script_no_se_ejecuta_desde_el_servidor_web(string $fichero): void
    {
        $codigo = (string) file_get_contents($fichero);
        if (preg_match('/^namespace\s+[\w\\\\]+;/m', $codigo) === 1) {
            $this->addToAssertionCount(1);

            return;
        }

        $this->assertMatchesRegularExpression(
            '/PHP_SAPI|php_sapi_name\(\)/',
            $codigo,
            'falta al principio: if (PHP_SAPI !== \'cli\') { http_response_code(404); exit; }'
        );
    }
}
