<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Comprueba que los controladores HTTP del módulo misas son legibles y válidos
 * en sintaxis PHP sin ejecutarlos (evita dependencias de sesión/BD en el lint).
 */
final class MisasControllersSyntaxTest extends TestCase
{
    public static function controllersProvider(): array
    {
        $baseDir = __DIR__ . '/../../../../../src/misas/infrastructure/ui/http/controllers';
        $files = glob($baseDir . '/*.php') ?: [];
        $cases = [];
        foreach ($files as $file) {
            $basename = basename($file);
            if (str_starts_with($basename, 'zz')) {
                continue;
            }
            $cases[$basename] = [$file];
        }

        return $cases;
    }

    #[DataProvider('controllersProvider')]
    public function testControllerFileExistsAndIsReadable(string $file): void
    {
        $this->assertFileExists($file, "El fichero del controlador no existe: $file");
        $this->assertIsReadable($file, "El fichero del controlador no es legible: $file");
    }

    #[DataProvider('controllersProvider')]
    public function testControllerPassesPhpLint(string $file): void
    {
        $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($file);
        exec($cmd, $output, $exitCode);
        $message = implode("\n", $output);
        $this->assertSame(0, $exitCode, "Error de sintaxis en $file:\n$message");
    }

    #[DataProvider('controllersProvider')]
    public function testControllerStartsWithPhpOpenTag(string $file): void
    {
        $contents = file_get_contents($file) ?: '';
        $trimmed = ltrim($contents);
        $this->assertStringStartsWith('<?php', $trimmed, "El fichero debe empezar con '<?php': $file");
    }
}
