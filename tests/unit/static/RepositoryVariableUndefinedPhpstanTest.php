<?php

namespace Tests\unit\static;

use PHPUnit\Framework\TestCase;

/**
 * Evita regresiones del tipo "Undefined variable" en repositorios (rompen JSON en endpoints).
 * PHPStan nivel 9 sin baseline; solo falla por identifier variable.undefined.
 */
final class RepositoryVariableUndefinedPhpstanTest extends TestCase
{
    private const PHPSTAN_BIN = 'libs/vendor/bin/phpstan';

    private const PHPSTAN_CONFIG = 'phpstan-nobaseline.neon';

    public function test_repositorios_sin_variables_indefinidas(): void
    {
        $root = dirname(__DIR__, 3);
        $phpstan = $root . '/' . self::PHPSTAN_BIN;
        $config = $root . '/' . self::PHPSTAN_CONFIG;
        $this->assertFileExists($phpstan, 'Ejecutar composer install en libs/');
        $this->assertFileExists($config);

        $paths = $this->repositoryPaths($root);
        $this->assertNotEmpty($paths, 'No se encontraron ficheros *Repository.php en persistence');

        $cmd = [
            PHP_BINARY,
            $phpstan,
            'analyse',
            '-c',
            $config,
            '--memory-limit=2G',
            '--no-progress',
            '--error-format=raw',
            ...$paths,
        ];

        $descriptorSpec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($cmd, $descriptorSpec, $pipes, $root);
        $this->assertIsResource($process);

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $undefined = [];
        foreach (explode("\n", $stdout . "\n" . $stderr) as $line) {
            if ($line === '' || !str_contains($line, 'variable.undefined')) {
                continue;
            }
            $undefined[] = trim($line);
        }

        $this->assertSame(
            [],
            $undefined,
            "Variables posiblemente indefinidas en repositorios (PHPStan variable.undefined):\n"
            . implode("\n", $undefined)
            . "\n\nCorregir inicializando la variable o lanzando excepción si falta el dato."
        );
    }

    /**
     * @return list<string> Rutas relativas a la raíz del proyecto
     */
    private function repositoryPaths(string $root): array
    {
        $paths = [];
        $src = $root . '/src';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $pathname = $file->getPathname();
            if (!str_contains($pathname, '/infrastructure/persistence/')) {
                continue;
            }
            if (!preg_match('/Repository\.php$/', $file->getFilename())) {
                continue;
            }
            $paths[] = substr($pathname, strlen($root) + 1);
        }
        sort($paths);

        return $paths;
    }
}
