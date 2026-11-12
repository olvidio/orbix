<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use Tests\myTest;

/**
 * Controladores de lectura de `usuarios` (fichas y listas de la administración
 * de usuarios, roles y permisos), ejecutados en proceso contra la BD de
 * desarrollo: responden solo JSON con el envelope de ContestarJson, con los
 * datos esperados para un usuario/rol real y error para uno inexistente.
 */
final class UsuariosLecturasContractTest extends myTest
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
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: «" . substr($salida, 0, 300) . '»');
        return $json;
    }

    /** @return array<string, mixed> */
    private static function data(array $json): array
    {
        $d = is_string($json['data'] ?? null) ? json_decode($json['data'], true) : ($json['data'] ?? null);
        return is_array($d) ? $d : [];
    }

    private function idUsuario(): int
    {
        $u = $GLOBALS['container']->get(UsuarioRepositoryInterface::class)->matching(Criteria::create()->limit(1));
        if ($u === []) {
            $this->markTestSkipped('Sin usuarios');
        }
        return $u[0]->getId_usuario();
    }

    private function idRole(): int
    {
        $r = $GLOBALS['container']->get(RoleRepositoryInterface::class)->matching(Criteria::create()->limit(1));
        if ($r === []) {
            $this->markTestSkipped('Sin roles');
        }
        return $r[0]->getId_role();
    }

    /** @return iterable<string, array{string, string}> */
    public static function porUsuarioProvider(): iterable
    {
        yield 'ficha' => ['usuario_form.php', 'a_campos'];
        yield 'permisos de actividades' => ['perm_activ_lista.php', ''];
        yield 'grupos del usuario' => ['usuario_grupo_lst.php', ''];
        yield 'grupos para quitar' => ['usuario_grupo_del_lst.php', ''];
    }

    #[DataProvider('porUsuarioProvider')]
    public function test_lecturas_de_un_usuario(string $ctrl, string $clave): void
    {
        $json = $this->ejecutar($ctrl, ['id_usuario' => (string) $this->idUsuario(), 'quien' => 'usuario']);
        $this->assertTrue($json['success'], $json['mensaje'] ?? '');
        if ($clave !== '') {
            $this->assertArrayHasKey($clave, self::data($json));
        }
    }

    public function test_ficha_de_usuario_nuevo(): void
    {
        $json = $this->ejecutar('usuario_form.php', ['id_usuario' => '', 'quien' => 'usuario']);
        $this->assertTrue($json['success'], $json['mensaje'] ?? '');
    }

    public function test_usuario_inexistente(): void
    {
        $json = $this->ejecutar('usuario_grupo_lst.php', ['id_usuario' => '999999981']);
        $this->assertSame([false, 'Usuario no encontrado'], [$json['success'], $json['mensaje']]);
    }

    public function test_lecturas_de_un_rol(): void
    {
        foreach (['role_info.php', 'role_grupmenu_info.php'] as $ctrl) {
            $json = $this->ejecutar($ctrl, ['id_role' => (string) $this->idRole()]);
            $this->assertTrue($json['success'], "$ctrl: " . ($json['mensaje'] ?? ''));
            $json = $this->ejecutar($ctrl, ['id_role' => '9999']);
            $this->assertFalse($json['success'], "$ctrl con un rol inexistente");
        }
    }

    public function test_preferencias_del_usuario_de_la_sesion(): void
    {
        $json = $this->ejecutar('usuario_preferencias.php', []);
        $this->assertTrue($json['success'], $json['mensaje'] ?? '');
        $this->assertNotSame([], self::data($json));
    }
}
