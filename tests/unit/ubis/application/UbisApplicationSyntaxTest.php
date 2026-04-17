<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UbisApplicationSyntaxTest extends TestCase
{
    public static function applicationFilesProvider(): array
    {
        $baseDir = __DIR__ . '/../../../../src/ubis/application';
        $files = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseDir));
        foreach ($it as $fileInfo) {
            /** @var \SplFileInfo $fileInfo */
            if (!$fileInfo->isFile()) {
                continue;
            }
            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }
            $basename = $fileInfo->getBasename();
            if (str_starts_with($basename, 'zz') || $basename === 'example.php') {
                continue;
            }
            $files[$fileInfo->getPathname()] = [$fileInfo->getPathname()];
        }
        ksort($files);
        return $files;
    }

    #[DataProvider('applicationFilesProvider')]
    public function testFileExistsAndIsReadable(string $file): void
    {
        $this->assertFileExists($file, "El fichero no existe: $file");
        $this->assertIsReadable($file, "El fichero no es legible: $file");
    }

    #[DataProvider('applicationFilesProvider')]
    public function testPassesPhpLint(string $file): void
    {
        $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($file);
        exec($cmd, $output, $exitCode);
        $message = implode("\n", $output);
        $this->assertSame(0, $exitCode, "Error de sintaxis en $file:\n$message");
    }

    #[DataProvider('applicationFilesProvider')]
    public function testStartsWithPhpOpenTag(string $file): void
    {
        $contents = file_get_contents($file) ?: '';
        $trimmed = ltrim($contents);
        $this->assertStringStartsWith('<?php', $trimmed, "El fichero debe empezar con '<?php': $file");
    }

    #[DataProvider('applicationFilesProvider')]
    public function testNamespaceMatchesFolder(string $file): void
    {
        $contents = file_get_contents($file) ?: '';
        if (!preg_match('/^namespace\s+([^;]+);/m', $contents, $m)) {
            $this->markTestSkipped("Sin namespace: $file");
        }
        $ns = trim($m[1]);
        $relative = str_replace(
            realpath(__DIR__ . '/../../../../') . DIRECTORY_SEPARATOR,
            '',
            realpath(dirname($file))
        );
        $expected = str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
        $this->assertSame(
            $expected,
            $ns,
            "El namespace '$ns' no coincide con la carpeta esperada '$expected' en $file"
        );
    }
}
