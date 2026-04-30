<?php

declare(strict_types=1);

namespace Tests\unit\frontend;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Evita fatals del tipo "Class AppUrlConfig not found" cuando un .php usa
 * AppUrlConfig:: sin importar frontend\shared\config\AppUrlConfig.
 */
class AppUrlConfigImportInFrontendPhpTest extends TestCase
{
    private const IMPORT_LINE = 'use frontend\\shared\\config\\AppUrlConfig';

    public function test_frontend_php_files_with_short_app_url_config_reference_import_class(): void
    {
        $frontendDir = dirname(__DIR__, 3) . '/frontend';
        self::assertDirectoryExists($frontendDir);

        $violations = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($frontendDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $path = $file->getPathname();
            $src = (string)file_get_contents($path);
            if ($src === '') {
                continue;
            }
            if (!preg_match('/(?<![\\\\w])AppUrlConfig::/', $src)) {
                continue;
            }
            if (str_contains($src, self::IMPORT_LINE)) {
                continue;
            }
            $violations[] = str_replace($frontendDir . '/', 'frontend/', $path);
        }

        self::assertSame(
            [],
            $violations,
            "Faltan imports de AppUrlConfig en:\n" . implode("\n", $violations)
        );
    }
}
