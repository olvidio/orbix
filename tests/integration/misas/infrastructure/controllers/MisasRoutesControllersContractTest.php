<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Asegura que cada `require` declarado en routes.php apunta a un fichero real.
 */
final class MisasRoutesControllersContractTest extends TestCase
{
    private function routesFilePath(): string
    {
        return __DIR__ . '/../../../../../src/misas/config/routes.php';
    }

    public function test_routes_file_exists(): void
    {
        $this->assertFileExists($this->routesFilePath());
    }

    public function test_every_route_require_targets_existing_controller_file(): void
    {
        $routesFile = $this->routesFilePath();
        $content = file_get_contents($routesFile) ?: '';
        preg_match_all("/require\\s+__DIR__\\s*\\.\\s*'([^']+)'/", $content, $matches);
        $this->assertNotEmpty($matches[1], 'No se encontraron require __DIR__ en routes.php');

        $base = dirname($routesFile);
        foreach ($matches[1] as $relative) {
            $fullPath = $base . $relative;
            $this->assertFileExists(
                $fullPath,
                sprintf('Ruta en routes.php no resuelve a fichero: %s', $relative)
            );
            $this->assertStringEndsWith(
                '.php',
                $relative,
                'Los controladores deben ser ficheros .php'
            );
        }
    }

    public function test_routes_register_at_least_one_endpoint(): void
    {
        $content = file_get_contents($this->routesFilePath()) ?: '';
        $this->assertGreaterThanOrEqual(
            1,
            substr_count($content, '->addRoute('),
            'routes.php debería registrar rutas con addRoute'
        );
    }
}
