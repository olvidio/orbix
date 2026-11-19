<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\infrastructure\controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use Tests\myTest;

/**
 * La administración de usuarios, grupos, permisos y roles (`src/usuarios`) es solo para los roles de
 * administración (1 a 3). Antes, cualquier usuario con sesión podía pedir a los endpoints de lectura las
 * cápsulas que autorizan la escritura y, por ejemplo, añadirse al grupo de administración; y un
 * administrador de delegación podía ponerse el rol de superadministrador (revisión de seguridad del
 * 2026-11-19, AUT-01 y AUT-07).
 */
final class AdministracionDeUsuariosSoloAdministradoresTest extends myTest
{
    private const CONTROLADORES = __DIR__ . '/../../../../../src/usuarios/infrastructure/ui/http/controllers';

    /** Controladores de administración: llevan la guarda lo primero. */
    private const ADMINISTRACION = [
        'borrar_pwd', 'borrar_pwd_form_data',
        'grupo_eliminar', 'grupo_guardar', 'grupo_info', 'grupo_lista',
        'perm_activ_eliminar', 'perm_activ_guardar', 'perm_activ_lista',
        'perm_menu_eliminar', 'perm_menu_guardar', 'perm_menu_info', 'perm_menu_lista',
        'role_eliminar', 'role_grupmenu_add', 'role_grupmenu_del', 'role_grupmenu_info', 'role_guardar',
        'role_info', 'role_lista',
        'usuario_eliminar', 'usuario_form', 'usuario_grupo_add', 'usuario_grupo_del', 'usuario_grupo_del_lst',
        'usuario_grupo_lst', 'usuario_guardar', 'usuario_lista',
    ];

    /** Controladores que no son de administración, con el motivo. */
    private const OTROS = [
        'app_login' => 'entrada de la app (sin sesión)',
        'app_session' => 'entrada de la app (sin sesión)',
        'check_first_login_2fa' => 'el propio usuario, al entrar',
        'mails_contactos_region' => 'consulta de contactos',
        'preferencia_tabla_get' => 'preferencias del propio usuario',
        'preferencias_guardar' => 'preferencias del propio usuario',
        'recuperar_2fa_enlace_confirmar' => 'recuperación de acceso (sin sesión)',
        'recuperar_2fa_enlace_datos' => 'recuperación de acceso (sin sesión)',
        'recuperar_2fa_mail' => 'recuperación de acceso (sin sesión)',
        'recuperar_password_mail' => 'recuperación de acceso (sin sesión)',
        'restablecer_password_datos' => 'recuperación de acceso (sin sesión)',
        'restablecer_password_guardar' => 'recuperación de acceso (sin sesión)',
        'usuario_2fa_info' => '2FA del propio usuario',
        'usuario_2fa_update' => '2FA del propio usuario',
        'usuario_2fa_verify' => '2FA del propio usuario',
        'usuario_ayuda_info' => 'ayuda en la pantalla de entrada (sin sesión)',
        'usuario_check_pwd' => 'validación de la contraseña que se escribe',
        'usuario_guardar_mail' => 'correo del propio usuario',
        'usuario_guardar_mail_form_data' => 'correo del propio usuario',
        'usuario_guardar_pwd' => 'contraseña del propio usuario',
        'usuario_guardar_pwd_form_data' => 'contraseña del propio usuario',
        'usuario_info' => 'datos del propio usuario',
        'usuario_preferencias' => 'preferencias del propio usuario',
    ];

    /** @var array<string, mixed> */
    private array $sesionPrevia = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionPrevia = $_SESSION;
    }

    public function tearDown(): void
    {
        $_SESSION = $this->sesionPrevia;
        parent::tearDown();
    }

    private function conRol(int $idRole): void
    {
        $_SESSION['session_auth']['id_role'] = $idRole;
    }

    /**
     * @param array<string, mixed> $post
     * @return array{0: array<string, mixed>, 1: string}
     */
    private function llamar(string $controlador, array $post): array
    {
        $postAnterior = $_POST;
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::CONTROLADORES . '/' . $controlador . '.php';
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            $_POST = $postAnterior;
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, 'debe responder JSON; respondió: «' . substr($salida, 0, 200) . '»');

        return [$json, $salida];
    }

    /** @return iterable<string, array{string, array<string, string>}> */
    public static function lecturasQueFirman(): iterable
    {
        yield 'grupos que se pueden añadir a un usuario' => ['usuario_grupo_lst', ['id_usuario' => 'yo']];
        yield 'grupos que se pueden quitar a un usuario' => ['usuario_grupo_del_lst', ['id_usuario' => 'yo']];
        yield 'permiso de menú de un usuario o grupo' => ['perm_menu_info', ['id_usuario' => 'yo']];
        yield 'permisos de menú de un usuario o grupo' => ['perm_menu_lista', ['id_usuario' => 'yo']];
        yield 'permisos de actividades de un usuario' => ['perm_activ_lista', ['id_usuario' => 'yo']];
        yield 'ficha de un rol' => ['role_info', ['id_role' => '6']];
        yield 'ficha de un rol nuevo' => ['role_info', []];
        yield 'grupos de menú que se pueden añadir a un rol' => ['role_grupmenu_info', ['id_role' => '6']];
        yield 'lista de roles' => ['role_lista', []];
        yield 'ficha de un grupo nuevo' => ['grupo_info', []];
        yield 'lista de grupos' => ['grupo_lista', []];
        yield 'lista de usuarios' => ['usuario_lista', []];
        yield 'ficha de un usuario' => ['usuario_form', ['id_usuario' => 'yo']];
        yield 'poner a todos la contraseña igual al login' => ['borrar_pwd_form_data', []];
    }

    /**
     * @param array<string, string> $post
     */
    #[DataProvider('lecturasQueFirman')]
    public function test_un_usuario_de_oficina_no_recibe_datos_ni_capsulas(string $controlador, array $post): void
    {
        $this->conRol(6);
        $post = array_map(static fn (string $v): string => $v === 'yo' ? (string) ConfigGlobal::mi_id_usuario() : $v, $post);

        [$json, $salida] = $this->llamar($controlador, $post);

        $this->assertFalse($json['success'] ?? null, $controlador . ' responde a un usuario de oficina');
        $this->assertStringNotContainsString('ctx', $salida, $controlador . ' entrega una cápsula a un usuario de oficina');
    }

    /** Control: al administrador le siguen respondiendo. */
    public function test_un_administrador_recibe_la_ficha_de_un_rol_sin_capsula_para_modificarlo(): void
    {
        $this->conRol(2);

        [$json] = $this->llamar('role_info', ['id_role' => '6']);

        $this->assertTrue($json['success'] ?? null, (string) ($json['mensaje'] ?? ''));
        $data = json_decode((string) $json['data'], true);
        $this->assertSame('', $data['ctx_guardar'], 'los roles solo los modifica el superadministrador');
    }

    public function test_el_superadministrador_recibe_la_capsula_para_modificar_un_rol(): void
    {
        $this->conRol(1);

        [$json] = $this->llamar('role_info', ['id_role' => '6']);

        $this->assertTrue($json['success'] ?? null, (string) ($json['mensaje'] ?? ''));
        $data = json_decode((string) $json['data'], true);
        $this->assertSame(['id_role' => 6], array_intersect_key(HashB::open($data['ctx_guardar'], 'role_guardar'), ['id_role' => 0]));
    }

    /** @return iterable<string, array{string, string, array<string, mixed>}> */
    public static function escriturasConCapsulaValida(): iterable
    {
        yield 'añadir un grupo a un usuario' => ['usuario_grupo_add', 'ctx', 'usuario_grupo_add', ['id_usuario' => -999000001, 'id_grupo' => -999000002]];
        yield 'guardar un rol' => ['role_guardar', 'ctx', 'role_guardar', ['id_role' => -999000001]];
        yield 'añadir un grupo de menú a un rol' => ['role_grupmenu_add', 'ctx', 'role_grupmenu_add', ['id_role' => -999000001, 'id_grupmenu' => -999000002]];
        yield 'guardar un usuario' => ['usuario_guardar', 'ctx', 'usuario_guardar', ['que_user' => 'guardar', 'id_usuario' => 999000001, 'quien' => 'usuario']];
        yield 'guardar un grupo' => ['grupo_guardar', 'ctx', 'grupo_guardar', ['id_usuario' => 999000001]];
    }

    /**
     * Aunque la cápsula sea válida (la firma se calcula con datos que el usuario conoce), el que escribe
     * también comprueba el rol.
     *
     * @param array<string, mixed> $payload
     */
    #[DataProvider('escriturasConCapsulaValida')]
    public function test_un_usuario_de_oficina_no_escribe_ni_con_una_capsula_valida(string $controlador, string $campo, string $accion, array $payload): void
    {
        $this->conRol(6);

        [$json] = $this->llamar($controlador, [$campo => HashB::sign($accion, $payload), 'usuario' => 'e2e_no_existe', 'id_role' => '6']);

        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }

    /** Un administrador de delegación no puede ponerse (ni poner a nadie) el rol de superadministrador. */
    public function test_un_administrador_no_asigna_el_rol_de_superadministrador(): void
    {
        $this->conRol(2);
        $ctx = HashB::sign('usuario_guardar', ['que_user' => 'guardar', 'id_usuario' => 999000001, 'quien' => 'usuario']);

        [$json] = $this->llamar('usuario_guardar', ['ctx' => $ctx, 'usuario' => 'e2e_no_existe', 'id_role' => '1']);

        $this->assertFalse($json['success'] ?? null);
        $this->assertStringContainsString('Operación no autorizada', (string) ($json['mensaje'] ?? ''));
    }

    /** @return iterable<string, array{string}> */
    public static function controladoresDeAdministracion(): iterable
    {
        foreach (self::ADMINISTRACION as $nombre) {
            yield $nombre => [$nombre];
        }
    }

    #[DataProvider('controladoresDeAdministracion')]
    public function test_cada_controlador_de_administracion_comprueba_el_permiso_lo_primero(string $nombre): void
    {
        $codigo = (string) file_get_contents(self::CONTROLADORES . '/' . $nombre . '.php');
        $guardia = strpos($codigo, 'PermisoAdministrarUsuariosGuard::');
        $this->assertNotFalse($guardia, $nombre . ' no comprueba el permiso');
        foreach (['FilterPostGet::post(', '$_POST', 'HashB::open(', 'HashB::sign(', 'DependencyResolver::get(', '->execute('] as $antes) {
            $pos = strpos($codigo, $antes);
            if ($pos !== false) {
                $this->assertGreaterThan($guardia, $pos, "{$nombre}: «{$antes}» antes de comprobar el permiso");
            }
        }
    }

    /** Un controlador nuevo del módulo tiene que apuntarse en una de las dos listas. */
    public function test_todos_los_controladores_estan_clasificados(): void
    {
        $enDisco = array_map(static fn (string $f): string => basename($f, '.php'), glob(self::CONTROLADORES . '/*.php') ?: []);
        $clasificados = array_merge(self::ADMINISTRACION, array_keys(self::OTROS));
        sort($enDisco);
        sort($clasificados);

        $this->assertSame($clasificados, $enDisco);
    }
}
