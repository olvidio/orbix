<?php

namespace Tests\unit\static;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Detecta llamadas inseguras del tipo getXxxVo()->value() cuando el getter puede devolver null.
 *
 * PHPStan (nivel 9) emite method.nonObject con el mensaje
 * "Cannot call method value() on …|null". Usar ?->value() o comprobar null antes.
 *
 * @see RepositoryVariableUndefinedPhpstanTest mismo patrón (PHPStan sin baseline)
 */
final class NullableVoValueCallPhpstanTest extends TestCase
{
    private const PHPSTAN_BIN = 'libs/vendor/bin/phpstan';

    private const PHPSTAN_CONFIG = 'phpstan-nobaseline.neon';

    /** Rutas analizadas (ampliar a ['src'] cuando se corrijan el resto del proyecto). */
    private const ANALYSE_PATHS = [
        'src/notas',
        'src/certificados',
    ];

    public function test_notas_y_certificados_sin_value_sobre_vo_nullable(): void
    {
        $violations = $this->collectUnsafeValueCalls(self::ANALYSE_PATHS);

        $this->assertSame(
            [],
            $violations,
            "Usar ?->value() (o comprobar null) cuando el getter Vo es nullable:\n"
            . implode("\n", $violations)
            . "\n\nPHPStan: Cannot call method value() on …|null"
        );
    }

    /**
     * Inventario global (no falla el CI): útil para ir reduciendo deuda antes de ampliar ANALYSE_PATHS.
     */
    #[Group('inventory')]
    public function test_inventario_src_value_sobre_vo_nullable(): void
    {
        $violations = $this->collectUnsafeValueCalls(['src']);
        sort($violations);

        $this->assertGreaterThan(
            0,
            count($violations),
            'Si ya no quedan avisos en src/, ampliar ANALYSE_PATHS a [\'src\'] en el test principal.'
        );
    }

    /**
     * @param list<string> $paths Rutas relativas a la raíz del proyecto
     * @return list<string>
     */
    private function collectUnsafeValueCalls(array $paths): array
    {
        $root = dirname(__DIR__, 3);
        $phpstan = $root . '/' . self::PHPSTAN_BIN;
        $config = $root . '/' . self::PHPSTAN_CONFIG;
        $this->assertFileExists($phpstan, 'Ejecutar composer install en libs/');
        $this->assertFileExists($config);

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

        $violations = [];
        foreach (explode("\n", $stdout . "\n" . $stderr) as $line) {
            if ($line === '' || !str_contains($line, 'Cannot call method value()')) {
                continue;
            }
            if (!str_contains($line, 'method.nonObject')) {
                continue;
            }
            $violations[] = trim($line);
        }

        return $violations;
    }
}
