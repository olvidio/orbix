<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use Tests\myTest;

/**
 * Controladores de `usuarios` que respondían con `exit` (o con el cuerpo vacío):
 * en el despacho en proceso de `PostRequest` el `exit` corta también al
 * controlador frontend que llama. Todos deben responder solo JSON.
 * Solo se ejecutan caminos que no escriben (contraseña que no cumple la política,
 * esquema o usuario inexistentes).
 */
final class UsuariosControllersRespuestaTest extends myTest
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

    /** @param array<string, mixed> $post @return array<string, mixed> */
    private function ejecutar(string $controlador, array $post): array
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
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: «" . substr($salida, 0, 200) . '»');
        return $json;
    }

    private function idUsuario(): int
    {
        $u = $GLOBALS['container']->get(UsuarioRepositoryInterface::class)->matching(\src\shared\domain\criteria\Criteria::create()->limit(1));
        if ($u === []) {
            $this->markTestSkipped('Sin usuarios');
        }
        return $u[0]->getId_usuario();
    }

    public function test_comprobar_contrasena(): void
    {
        $json = $this->ejecutar('usuario_check_pwd.php', ['usuario' => 'pepe', 'password' => 'x']);
        $this->assertFalse($json['success']);
        $this->assertNotSame('', $json['mensaje']);

        $json = $this->ejecutar('usuario_check_pwd.php', ['usuario' => 'pepe', 'password' => 'Un4-Clave.Larga!Seg']);
        $this->assertTrue($json['success'], $json['mensaje'] ?? '');

        $this->assertTrue($this->ejecutar('usuario_check_pwd.php', ['usuario' => 'pepe'])['success'], 'sin contraseña no hay nada que comprobar');
        $json = $this->ejecutar('usuario_check_pwd.php', ['id_usuario' => '999999981', 'password' => 'x']);
        $this->assertSame([false, 'Usuario no encontrado'], [$json['success'], $json['mensaje']]);
    }

    public function test_guardar_una_contrasena_que_no_cumple_la_politica_no_corta_la_peticion(): void
    {
        $json = $this->ejecutar('usuario_guardar_pwd.php', [
            'ctx_guardar' => HashB::sign('usuario_guardar_pwd', ['id_usuario' => $this->idUsuario()]),
            'password' => 'x',
        ]);
        $this->assertFalse($json['success']);
        $this->assertNotSame('', $json['mensaje']);
    }

    public function test_activar_2fa_sin_codigo_o_con_codigo_invalido(): void
    {
        $ctx = HashB::sign('usuario_2fa_update', ['id_usuario' => $this->idUsuario()]);
        foreach (['' => 'Se requiere un código de verificación para activar 2FA', '000000' => 'Código de verificación inválido'] as $codigo => $mensaje) {
            $json = $this->ejecutar('usuario_2fa_update.php', ['ctx_2fa_update' => $ctx, 'enable_2fa' => '1', 'verification_code' => (string) $codigo, 'secret_2fa' => 'JBSWY3DPEHPK3PXP']);
            $this->assertFalse($json['success'], "código «{$codigo}»");
        }
    }

    public function test_ayuda_de_acceso_con_esquema_o_usuario_no_validos(): void
    {
        $json = $this->ejecutar('usuario_ayuda_info.php', ['username' => 'zz', 'esquema' => '']);
        $this->assertSame([false, 'Esquema no válido'], [$json['success'], $json['mensaje']]);
        $json = $this->ejecutar('usuario_ayuda_info.php', ['username' => 'zz_no_existe', 'esquema' => 'H-dlbv']);
        $this->assertSame([false, 'Debe ingresar un nombre de usuario válido'], [$json['success'], $json['mensaje']]);
    }
}
