<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\controllers;

use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
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

    public function test_cambiar_la_contrasena_exige_la_actual(): void
    {
        /** @var \src\usuarios\domain\contracts\UsuarioRepositoryInterface $repo */
        $repo = $GLOBALS['container']->get(\src\usuarios\domain\contracts\UsuarioRepositoryInterface::class);
        $antes = $repo->findById($this->idUsuario())?->getPasswordAsString();
        $ctx = HashB::sign('usuario_guardar_pwd', ['id_usuario' => $this->idUsuario()]);

        try {
            foreach ([[], ['password_actual' => 'no-es-esta']] as $extra) {
                $json = $this->ejecutar('usuario_guardar_pwd.php', ['ctx_guardar' => $ctx, 'password' => 'Zx9!kq#Lm2vT-nueva'] + $extra);

                $this->assertFalse($json['success']);
                $this->assertSame('La contraseña actual no es correcta', $json['mensaje']);
            }
            $this->assertSame($antes, $repo->findById($this->idUsuario())?->getPasswordAsString(), 'la contraseña no ha cambiado');
        } finally {
            // Si el control fallara, la contraseña del usuario de la BD de pruebas habría cambiado: se repone.
            $u = $repo->findById($this->idUsuario());
            if ($u !== null && is_string($antes) && $u->getPasswordAsString() !== $antes) {
                $u->setPasswordVo(new \src\usuarios\domain\value_objects\Password($antes));
                $repo->Guardar($u);
            }
        }
    }

    public function test_activar_2fa_sin_codigo_o_con_codigo_invalido(): void
    {
        $ctx = HashB::sign('usuario_2fa_update', ['id_usuario' => $this->idUsuario()]);
        foreach (['' => 'Se requiere un código de verificación para activar 2FA', '000000' => 'Código de verificación inválido'] as $codigo => $mensaje) {
            $json = $this->ejecutar('usuario_2fa_update.php', ['ctx_2fa_update' => $ctx, 'enable_2fa' => '1', 'verification_code' => (string) $codigo, 'secret_2fa' => 'JBSWY3DPEHPK3PXP']);
            $this->assertFalse($json['success'], "código «{$codigo}»");
        }
    }

    /**
     * Las casas de un usuario de casa (`csv_id_pau`) solo guardan ids numéricos: el formulario manda los de
     * un desplegable, pero `casas[]` llegaba tal cual y se guardaba cualquier texto (la lectura ya filtra).
     * Revisión de seguridad del 2026-11-18 (sección 38). Si no queda ninguna casa válida no se toca el pau
     * (antes, con todas vacías, `IdPau('')` lanzaba una excepción).
     */
    public function test_las_casas_de_un_usuario_de_casa_solo_guardan_ids_numericos(): void
    {
        /** @var RoleRepositoryInterface $roles */
        $roles = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $idRole = array_search('cdc', $roles->getArrayRolesPau(), true);
        if ($idRole === false) {
            $this->markTestSkipped('Sin rol de casa');
        }
        /** @var UsuarioRepositoryInterface $usuarios */
        $usuarios = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $login = 'e2e_casas_' . bin2hex(random_bytes(3));
        $guardar = fn (array $casas): array => $this->ejecutar('usuario_guardar.php', [
            'ctx' => HashB::sign('usuario_guardar', ['que_user' => 'nuevo', 'id_usuario' => 0, 'quien' => 'usuario']),
            'usuario' => $login,
            'id_role' => (string) $idRole,
            'casas' => $casas,
        ]);
        $creado = function () use ($usuarios, $login): ?\src\usuarios\domain\entity\Usuario {
            $c = $usuarios->matching(\src\shared\domain\criteria\Criteria::create()->where(\src\shared\domain\criteria\Filter::eq(\src\usuarios\domain\criteria\UsuarioField::Usuario, $login)));
            return $c === [] ? null : $c[array_key_first($c)];
        };
        try {
            $json = $guardar(['101', "102) OR (1=1", '', 'abc', ' 103 ', '-5']);
            $this->assertTrue($json['success'], $json['mensaje'] ?? '');
            $this->assertSame('101,103', $creado()?->getCsvIdPauAsString());
        } finally {
            if (($u = $creado()) !== null) {
                $usuarios->Eliminar($u);
            }
        }
        try {
            $json = $guardar(['', 'abc']);
            $this->assertTrue($json['success'], $json['mensaje'] ?? '');
            $this->assertNull($creado()?->getCsvIdPauAsString());
        } finally {
            if (($u = $creado()) !== null) {
                $usuarios->Eliminar($u);
            }
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
