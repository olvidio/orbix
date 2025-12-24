<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class GrupMenuListaUseCaseSyntaxTest extends TestCase
{
    private function getFilePath(): string
    {
        return __DIR__ . '/../../../../src/menus/application/GrupMenuListaUseCase.php';
    }

    public function test_file_exists_and_is_readable(): void
    {
        $file = $this->getFilePath();
        $this->assertFileExists($file, 'El fichero de la use case no existe');
        $this->assertIsReadable($file, 'El fichero de la use case no es legible');
    }

    public function test_php_lint_passes(): void
    {
        $file = $this->getFilePath();
        $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($file);
        exec($cmd, $output, $exitCode);
        $message = implode("\n", $output);
        $this->assertSame(0, $exitCode, "Error de sintaxis en $file:\n$message");
    }

    public function test_namespace_and_class_declaration_present(): void
    {
        $file = $this->getFilePath();
        $contents = file_get_contents($file) ?: '';
        $this->assertStringContainsString('namespace src\\menus\\application;', $contents, 'Falta el namespace esperado');
        $this->assertStringContainsString('class GrupMenuListaUseCase', $contents, 'Falta la declaración de clase esperada');
    }
}
