<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * Endpoints anónimos del enlace de recuperación del 2FA (`recovery.php`): responden JSON y, con un
 * enlace que no vale, ni lo dan por válido ni quitan nada. El flujo con un enlace de verdad (correo →
 * confirmar → 2FA desactivado sin sesión) lo cubre `e2e/specs/authenticated/recuperar-2fa.spec.ts`,
 * y la lógica `Tests\unit\usuarios\infrastructure\Recuperacion2faTest`.
 */
final class Recuperar2faEnlaceControllersTest extends myTest
{
    private const DIR = __DIR__ . '/../../../../../src/usuarios/infrastructure/ui/http/controllers/';

    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
    }

    public function tearDown(): void
    {
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function data(string $controlador, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));
        $this->assertTrue($json['success'] ?? null, $controlador);
        $data = json_decode((string) ($json['data'] ?? ''), true);
        $this->assertIsArray($data);

        return $data;
    }

    /** @return iterable<string, array{array<string, mixed>}> */
    public static function enlacesQueNoValenProvider(): iterable
    {
        yield 'sin nada' => [[]];
        yield 'sin token' => [['esquema' => 'H-dlbv', 'id_usuario' => '1', 'token' => '']];
        yield 'token inventado' => [['esquema' => 'H-dlbv', 'id_usuario' => '1', 'token' => 'no-es-un-token']];
        yield 'esquema que no existe' => [['esquema' => 'H-noexistev', 'id_usuario' => '1', 'token' => 'x']];
    }

    /**
     * @param array<string, mixed> $post
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('enlacesQueNoValenProvider')]
    public function test_un_enlace_que_no_vale_ni_se_da_por_bueno_ni_desactiva(array $post): void
    {
        $this->assertSame(['valido' => false], $this->data('recuperar_2fa_enlace_datos.php', $post));
        $this->assertSame(['desactivado' => false], $this->data('recuperar_2fa_enlace_confirmar.php', $post));
    }
}
