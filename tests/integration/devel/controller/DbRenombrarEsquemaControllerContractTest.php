<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DbRenombrarEsquemaControllerContractTest extends TestCase
{
    private function getFilePath(): string
    {
        return __DIR__ . '/../../../../frontend/devel_db_admin/controller/db_renombrar_esquema.php';
    }

    private function getApplicationPath(): string
    {
        return __DIR__ . '/../../../../src/devel_db_admin/application/RenombrarEsquema.php';
    }

    private function getHttpControllerPath(): string
    {
        return __DIR__ . '/../../../../src/devel_db_admin/infrastructure/ui/http/controllers/renombrar_esquema.php';
    }

    public function test_file_exists_and_is_readable(): void
    {
        $file = $this->getFilePath();

        $this->assertFileExists($file, "No existe el controlador: {$file}");
        $this->assertIsReadable($file, "No se puede leer el controlador: {$file}");
    }

    public function test_php_lint_passes(): void
    {
        $file = $this->getFilePath();
        $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($file);
        exec($cmd, $output, $exitCode);
        $message = implode("\n", $output);

        $this->assertSame(0, $exitCode, "Error de sintaxis en {$file}:\n{$message}");
    }

    public function test_starts_with_php_open_tag(): void
    {
        $file = $this->getFilePath();
        $contents = file_get_contents($file) ?: '';

        $this->assertStringStartsWith('<?php', ltrim($contents));
    }

    public function test_contains_schema_and_user_rename_flow(): void
    {
        $contents = file_get_contents($this->getApplicationPath()) ?: '';

        $this->assertStringContainsString('$oDBRol->renombrarSchema($esquema_old);', $contents);
        $this->assertStringContainsString('$oDBRol->renombrarUsuario($esquema_old);', $contents);
        $this->assertStringContainsString('$oDBRol->renombrarSchema($esquema_oldv);', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'comun\');', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'sv\');', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'sv-e\');', $contents);
    }

    public function test_controller_delegates_via_post_request(): void
    {
        $contents = file_get_contents($this->getFilePath()) ?: '';

        $this->assertStringContainsString('PostRequest::getDataFromUrl', $contents);
        $this->assertStringContainsString('/src/devel_db_admin/renombrar_esquema', $contents);
    }

    public function test_http_controller_invokes_renombrar_esquema(): void
    {
        $contents = file_get_contents($this->getHttpControllerPath()) ?: '';

        $this->assertStringContainsString('RenombrarEsquema', $contents);
        $this->assertStringContainsString('->ejecutar(', $contents);
    }
}
