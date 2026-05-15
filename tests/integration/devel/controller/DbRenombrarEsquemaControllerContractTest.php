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

        $this->assertStringContainsString('renombrarBloqueRolEsquema', $contents);
        $this->assertStringContainsString('renombrarBloqueSoloEsquema', $contents);
        $this->assertStringContainsString('renombrarClaveInc', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'comun\');', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'sv\');', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'sv-e\');', $contents);
        $this->assertStringContainsString('$DbSchemaRepository->cambiarNombre($esquema_old, $esquema, \'sf\');', $contents);
        $this->assertStringContainsString('if (!$isDocker)', $contents);
        $this->assertStringContainsString('ServerConf::SERVIDOR', $contents);
        $this->assertStringContainsString('validarFicherosPasswordAntesDeRenombre', $contents);
        $this->assertStringContainsString("return ['avisos' => \$oDBRol->consumirAvisosRenameRol()];", $contents);
    }

    public function test_rename_blocks_are_idempotent_via_existence_checks(): void
    {
        $contents = file_get_contents($this->getApplicationPath()) ?: '';

        $this->assertMatchesRegularExpression(
            '/existeEsquema\(\$pdo,\s*\$esquemaOld\).*existeEsquema\(\$pdo,\s*\$esquemaNew\)/s',
            $contents,
            'renombrarBloqueRolEsquema debe saltar ALTER SCHEMA si el nuevo ya existe y el viejo no.',
        );
        $this->assertMatchesRegularExpression(
            '/existeRol\(\$pdo,\s*\$esquemaOld\).*existeRol\(\$pdo,\s*\$esquemaNew\)/s',
            $contents,
            'renombrarBloqueRolEsquema debe saltar ALTER ROLE si el nuevo ya existe y el viejo no.',
        );
        $this->assertStringContainsString('repararEsquemaPostRenombre', $contents);
        $this->assertStringContainsString('incTieneClave', $contents);
    }

    public function test_password_validation_accepts_old_or_new_key(): void
    {
        $contents = file_get_contents($this->getApplicationPath()) ?: '';

        $this->assertStringContainsString('leerPasswordEsquema', $contents);
        $this->assertMatchesRegularExpression(
            '/foreach \(\[\$esquemaOld,\s*\$esquemaNew\] as \$clave\)/',
            $contents,
            'leerPasswordEsquema debe intentar primero la clave vieja y luego la nueva en cada .inc.',
        );
        $this->assertStringContainsString(
            "ni con el nombre antiguo ni con el nuevo",
            $contents,
            'El mensaje de error de validación debe reflejar que se probaron ambas claves.',
        );
    }

    public function test_rename_uses_only_importar_connection_for_ddl(): void
    {
        $contents = file_get_contents($this->getApplicationPath()) ?: '';

        $this->assertStringContainsString('pdoDesdeImportar', $contents);

        $count = preg_match_all("/new ConfigDB\('importar'\)/", $contents);
        $this->assertGreaterThanOrEqual(
            3,
            $count,
            'Las operaciones DDL deben crearse contra la conexión `importar` (superusuario).',
        );
    }

    public function test_controller_delegates_via_post_request(): void
    {
        $contents = file_get_contents($this->getFilePath()) ?: '';

        $this->assertStringContainsString('PostRequest::getDataFromUrl', $contents);
        $this->assertStringContainsString("'sf' => \$Qsf", $contents);
        $this->assertStringContainsString("'esquema_origen' =>", $contents);
    }

    public function test_http_controller_invokes_renombrar_esquema(): void
    {
        $contents = file_get_contents($this->getHttpControllerPath()) ?: '';

        $this->assertStringContainsString('RenombrarEsquema', $contents);
        $this->assertStringContainsString('->ejecutar(', $contents);
        $this->assertStringContainsString('ContestarJson::enviar', $contents);
        $this->assertStringContainsString("\$payload['error']", $contents);
        $this->assertStringContainsString("'avisos'", $contents);
    }

    public function test_dbrol_handles_duplicate_role_on_rename(): void
    {
        $path = __DIR__ . '/../../../../src/shared/infrastructure/persistence/postgresql/DBRol.php';
        $contents = file_get_contents($path) ?: '';

        $this->assertStringContainsString('eliminarRolConflicto', $contents);
        $this->assertStringContainsString('consumirAvisosRenameRol', $contents);
    }

    public function test_verificar_route_and_application_exist(): void
    {
        $routes = file_get_contents(__DIR__ . '/../../../../src/devel_db_admin/config/routes.php') ?: '';
        $this->assertStringContainsString('verificar_renombrar_esquema', $routes);
        $this->assertStringContainsString('corregir_renombrar_esquema', $routes);

        $app = __DIR__ . '/../../../../src/devel_db_admin/application/VerificarEstadoRenombrarEsquema.php';
        $this->assertFileExists($app);
        $c = file_get_contents($app) ?: '';
        $this->assertStringContainsString('VerificarEstadoRenombrarEsquema', $c);
        $this->assertStringContainsString('calcularListo', $c);

        $cor = __DIR__ . '/../../../../src/devel_db_admin/application/CorregirEstadoRenombrarEsquema.php';
        $this->assertFileExists($cor);
        $this->assertStringContainsString('CorregirEstadoRenombrarEsquema', file_get_contents($cor) ?: '');
    }
}
