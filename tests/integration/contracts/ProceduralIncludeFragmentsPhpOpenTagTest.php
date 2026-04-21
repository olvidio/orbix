<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Ficheros PHP pensados para require/include como fragmento procedural deben
 * empezar (tras espacios/BOM) por <?php. Si no, PHP los trata como HTML y el
 * código fuente se envía al cliente (v. src/misas/application/_cuadricula_zona_grid_fragment.php).
 *
 * Convención cubierta: bajo src/, nombre de fichero con prefijo '_' (p. ej. _foo.php).
 */
final class ProceduralIncludeFragmentsPhpOpenTagTest extends TestCase
{
    private static function repoRoot(): string
    {
        return dirname(__DIR__, 3);
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function fragmentFilesProvider(): iterable
    {
        $src = self::repoRoot() . '/src';
        if (!is_dir($src)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            $path = $fileInfo->getPathname();
            if (!str_ends_with($path, '.php')) {
                continue;
            }
            if (!str_starts_with($fileInfo->getBasename(), '_')) {
                continue;
            }

            $rel = substr($path, strlen(self::repoRoot()) + 1);
            yield $rel => [$path];
        }
    }

    #[DataProvider('fragmentFilesProvider')]
    public function test_fragment_starts_with_php_open_tag(string $absolutePath): void
    {
        $raw = file_get_contents($absolutePath) ?: '';
        $trimmed = ltrim($raw, " \t\n\r\0\x0B\u{FEFF}");
        $this->assertStringStartsWith(
            '<?php',
            $trimmed,
            "El fragmento debe abrir con <?php para ejecutarse al require, no volcarse como texto: {$absolutePath}"
        );
    }

    #[DataProvider('fragmentFilesProvider')]
    public function test_fragment_passes_php_lint(string $absolutePath): void
    {
        $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($absolutePath);
        exec($cmd, $output, $exitCode);
        $message = implode("\n", $output);
        $this->assertSame(0, $exitCode, "Error de sintaxis en {$absolutePath}:\n{$message}");
    }
}
