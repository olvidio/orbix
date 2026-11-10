<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\application;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\config\ConfigGlobal;
use src\usuarios\application\AppMobileLogin;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\PasswordHasher;
use Tests\myTest;

/**
 * Login de la app móvil (`AppMobileLogin`) contra la BD de desarrollo, con un único usuario de
 * prueba (id 4990021) que se crea y se borra en cada test. Ver {@see UsuarioDePruebaLogin}.
 *
 * A diferencia del login web, este caso de uso SÍ escribe `$_SESSION` y llama a `setcookie()`:
 * el trait guarda la sesión en `setUp` y la restaura en `tearDown`.
 */
class AppMobileLoginIntegracionTest extends myTest
{
    use UsuarioDePruebaLogin;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepararEscenarioLogin();
        putenv('ESQUEMA');
        putenv('PRIVATE');
    }

    public function tearDown(): void
    {
        $this->restaurarEscenarioLogin();
        parent::tearDown();
    }

    /**
     * @param array<string, string> $input
     * @return array<string, mixed>
     */
    private function login(array $input = []): array
    {
        $input += ['username' => self::USUARIO, 'password' => self::CLAVE, 'esquema' => self::ESQUEMA];

        // En CLI `setcookie()` no hace nada (ni avisa): las cookies «esquema» e «idioma» no se pueden comprobar aquí.
        return (new AppMobileLogin())->execute($input);
    }

    /** @return array{ok: false, code: string, mensaje: string} */
    private static function rechazo(): array
    {
        return ['ok' => false, 'code' => 'invalid_credentials', 'mensaje' => _("Usuario o contraseña incorrectos")];
    }

    /**
     * Un rechazo no puede autenticar la sesión ni devolver nada del usuario.
     *
     * @param array<string, mixed> $r
     */
    private function assertRechazadoSinFugas(array $r, string $code): void
    {
        $this->assertFalse($r['ok']);
        $this->assertSame($code, $r['code']);
        $this->assertNotSame('', $r['mensaje']);
        $this->assertSame($this->sesionPrevia['session_auth'], $_SESSION['session_auth'], 'la sesión no se autentica');
        $this->assertSame($this->sesionPrevia['config'], $_SESSION['config']);
        $texto = $this->comoTexto($r);
        $this->assertStringNotContainsString(self::CLAVE, $texto);
        $this->assertStringNotContainsString(self::SECRETO_2FA, $texto);
        $this->assertStringNotContainsStringIgnoringCase('select', $texto);
        $this->assertStringNotContainsString('aux_usuarios', $texto);
    }

    // ------------------------------------------------------------------ credenciales incorrectas

    public function test_usuario_inexistente_se_rechaza(): void
    {
        $r = $this->login(['username' => 'tstu_no_existe_zz']);

        $this->assertSame(self::rechazo(), $r);
        $this->assertRechazadoSinFugas($r, 'invalid_credentials');
    }

    public function test_contrasena_incorrecta_se_rechaza(): void
    {
        $this->crearUsuario();

        $r = $this->login(['password' => 'otra-cosa']);

        $this->assertSame(self::rechazo(), $r);
        $this->assertRechazadoSinFugas($r, 'invalid_credentials');
        $this->assertStringNotContainsString($this->hashGuardado(), $this->comoTexto($r));
    }

    /** No se puede distinguir «no existe el usuario» de «contraseña incorrecta». */
    public function test_usuario_inexistente_y_contrasena_incorrecta_dan_la_misma_respuesta(): void
    {
        $this->crearUsuario();

        $this->assertSame(
            $this->login(['username' => 'tstu_no_existe_zz']),
            $this->login(['password' => 'otra-cosa'])
        );
    }

    #[DataProvider('contrasenasCasiCorrectas')]
    public function test_una_contrasena_casi_correcta_se_rechaza(string $password): void
    {
        $this->crearUsuario();

        $this->assertSame(self::rechazo(), $this->login(['password' => $password]));
    }

    /** @return iterable<string, array{string}> */
    public static function contrasenasCasiCorrectas(): iterable
    {
        yield 'en minúsculas' => [strtolower(self::CLAVE)];
        yield 'con espacio final (la contraseña no se recorta)' => [self::CLAVE . ' '];
        yield 'con espacio inicial' => [' ' . self::CLAVE];
        yield 'sin el último carácter' => [substr(self::CLAVE, 0, -1)];
        yield 'con byte nulo' => [self::CLAVE . "\0"];
    }

    public function test_el_hash_guardado_no_sirve_como_contrasena(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::rechazo(), $this->login(['password' => $this->hashGuardado()]));
    }

    public function test_usuario_sin_contrasena_en_bd_no_puede_entrar(): void
    {
        $this->crearUsuario(['password' => null]);

        $this->assertSame(self::rechazo(), $this->login(['password' => self::CLAVE]));
        $this->assertSame('missing_credentials', $this->login(['password' => ''])['code']);
    }

    /**
     * Hallazgo: un hash vacío en BD no deja entrar, pero la respuesta no es controlada: el VO
     * `Password` lanza `InvalidArgumentException` al hidratar el usuario.
     */
    public function test_usuario_con_hash_vacio_en_bd_lanza_excepcion_sin_controlar(): void
    {
        $this->crearUsuario(['password' => '']);

        $this->expectException(\InvalidArgumentException::class);

        $this->login(['password' => 'x']);
    }

    #[DataProvider('usuariosConInyeccion')]
    public function test_inyeccion_sql_en_el_usuario_no_permite_entrar(string $username): void
    {
        $this->crearUsuario();

        $this->assertSame(self::rechazo(), $this->login(['username' => $username]));
    }

    /** @return iterable<string, array{string}> */
    public static function usuariosConInyeccion(): iterable
    {
        yield 'or 1=1' => ["' OR '1'='1"];
        yield 'comentario' => [self::USUARIO . "' --"];
        yield 'union' => ["x' UNION SELECT * FROM aux_usuarios --"];
        yield 'punto y coma' => [self::USUARIO . "'; SELECT 1; --"];
    }

    public function test_el_nombre_de_usuario_distingue_mayusculas(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::rechazo(), $this->login(['username' => strtoupper(self::USUARIO)]));
    }

    /** La app recorta usuario y esquema (el login web no recorta el usuario). */
    public function test_usuario_y_esquema_se_recortan(): void
    {
        $this->crearUsuario();

        $r = $this->login(['username' => '  ' . self::USUARIO . ' ', 'esquema' => ' ' . self::ESQUEMA . ' ']);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::USUARIO, $r['data']['username']);
        $this->assertSame(self::ESQUEMA, $r['data']['esquema']);
    }

    /**
     * Hallazgo: no hay límite de intentos fallidos ni bloqueo temporal; tras varios fallos seguidos
     * la contraseña correcta entra a la primera.
     */
    public function test_los_intentos_fallidos_no_bloquean_al_usuario(): void
    {
        $this->crearUsuario();
        for ($i = 0; $i < 6; $i++) {
            $this->assertSame(self::rechazo(), $this->login(['password' => 'intento-' . $i]));
        }

        $this->assertTrue($this->login()['ok']);
    }

    // ------------------------------------------------------------------ esquema

    public function test_esquema_fijado_por_entorno_inexistente_se_rechaza_antes_de_mirar_credenciales(): void
    {
        $this->crearUsuario();
        putenv('ESQUEMA=Xx-noexistev');

        $r = $this->login();

        $this->assertSame('invalid_schema_env', $r['code']);
        $this->assertSame(sprintf(_("No existe este equema: %s"), 'Xx-noexistev'), $r['mensaje']);
        $this->assertSame('invalid_schema_env', $this->login(['username' => '', 'password' => ''])['code']);
        $this->assertRechazadoSinFugas($r, 'invalid_schema_env');
    }

    public function test_sin_esquema_en_la_peticion_se_usa_el_del_entorno(): void
    {
        $this->crearUsuario();
        putenv('ESQUEMA=' . self::ESQUEMA);

        $r = $this->login(['esquema' => '']);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['data']['esquema']);
    }

    /**
     * Hallazgo: si la instalación fija el esquema con ESQUEMA, el esquema de la petición sigue
     * teniendo prioridad: el cliente puede autenticarse contra otra dl.
     */
    public function test_el_esquema_de_la_peticion_prevalece_sobre_el_del_entorno(): void
    {
        $this->crearUsuario();
        putenv('ESQUEMA=H-Hv');

        $r = $this->login(['esquema' => self::ESQUEMA]);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['data']['esquema']);
    }

    /**
     * Hallazgo: un esquema que no está en los ficheros de conexión NO devuelve `invalid_schema`:
     * `ConfigDB` lanza `RuntimeException` (solo se captura `PDOException`) y el mensaje incluye la
     * ruta de los ficheros de contraseñas del servidor.
     */
    #[DataProvider('esquemasDesconocidos')]
    public function test_esquema_desconocido_lanza_excepcion_sin_controlar(string $esquema): void
    {
        $this->crearUsuario();

        try {
            $r = $this->login(['esquema' => $esquema]);
        } catch (\RuntimeException $e) {
            $this->assertNotInstanceOf(\PDOException::class, $e);
            $this->assertStringContainsString($esquema, $e->getMessage());
            $this->assertStringContainsString('.inc', $e->getMessage(), 'el mensaje delata el fichero de configuración');
            $this->assertSame($this->sesionPrevia['session_auth'], $_SESSION['session_auth']);

            return;
        }
        $this->fail('Se esperaba RuntimeException; respuesta: ' . $this->comoTexto($r));
    }

    /** @return iterable<string, array{string}> */
    public static function esquemasDesconocidos(): iterable
    {
        yield 'sv inexistente' => ['Xx-noexistev'];
        yield 'comun inexistente' => ['Xx-noexiste'];
        yield 'con comilla' => ["a'b"];
        yield 'solo v' => ['v'];
    }

    /**
     * Hallazgo: con un esquema válido de la BD común (sin sufijo «v») la conexión se abre, pero
     * `aux_usuarios` no existe allí: `PDOException` sin controlar con el SQL en el mensaje. Las ramas
     * `=== false` → `server_error` son código muerto (las PDO usan ERRMODE_EXCEPTION).
     */
    #[DataProvider('esquemasDeLaBdComun')]
    public function test_esquema_de_la_bd_comun_lanza_pdoexception_sin_controlar(string $esquema): void
    {
        $this->crearUsuario();

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage('aux_usuarios');

        $this->login(['esquema' => $esquema]);
    }

    /** @return iterable<string, array{string}> */
    public static function esquemasDeLaBdComun(): iterable
    {
        yield 'sin sufijo' => ['H-dlb'];
        yield 'sufijo f fuera de sf (se recorta la f)' => ['H-dlbf'];
    }

    // ------------------------------------------------------------------ login correcto

    public function test_login_correcto_responde_solo_con_datos_no_sensibles(): void
    {
        $this->crearUsuario();

        $r = $this->login();

        $this->assertSame(
            [
                'ok' => true,
                'data' => [
                    'id_usuario' => self::ID_USUARIO,
                    'username' => self::USUARIO,
                    'esquema' => self::ESQUEMA,
                    'require_password_change' => false,
                ],
            ],
            $r
        );
    }

    public function test_login_correcto_autentica_la_sesion(): void
    {
        $this->crearUsuario();

        $this->login();

        $auth = $_SESSION['session_auth'];
        $this->assertSame(self::ID_USUARIO, $auth['id_usuario']);
        $this->assertSame(1, $auth['sfsv']);
        $this->assertSame(self::ROL_SIN_DMZ, $auth['id_role']);
        $this->assertSame('', $auth['role_pau']);
        $this->assertSame(self::USUARIO, $auth['username']);
        $this->assertSame(self::ESQUEMA, $auth['esquema']);
        $this->assertSame('', $auth['expire']);
        $this->assertSame('tstu_login@example.test', $auth['mail']);
        $this->assertSame('', $auth['idioma']);
        $this->assertSame('', $auth['ordenApellidos']);
        $this->assertSame((string) $this->sesionPrevia['session_auth']['mi_id_schema'], (string) $auth['mi_id_schema']);
        $this->assertInstanceOf(Usuario::class, $auth['MiUsuario']);
        $this->assertSame(self::ID_USUARIO, $auth['MiUsuario']->getId_usuario());

        $config = $_SESSION['config'];
        $this->assertSame(self::ROL_SIN_DMZ, $config['id_role']);
        $this->assertSame(self::USUARIO, $config['username']);
        $this->assertNotEmpty($config['a_apps']);
        $this->assertNotEmpty($config['a_mods']);
        $this->assertNotEmpty($config['mod_installed']);
        $this->assertNotEmpty($config['app_installed']);
        $this->assertContainsOnly('int', $config['app_installed']);
        $this->assertSame(array_values(array_unique($config['app_installed'])), $config['app_installed']);

        $this->assertSame('sv', $_SESSION['sfsv']);
    }

    /**
     * Hallazgo: la contraseña en claro se guarda en `$_SESSION['session_auth']` y en
     * `$_SESSION['config']` (fichero de sesión del servidor); el hash va dentro de `MiUsuario`.
     */
    public function test_la_contrasena_en_claro_queda_en_la_sesion(): void
    {
        $this->crearUsuario();

        $this->login();

        $this->assertSame(self::CLAVE, $_SESSION['session_auth']['password']);
        $this->assertSame(self::CLAVE, $_SESSION['config']['password']);
        $this->assertSame($this->hashGuardado(), $_SESSION['session_auth']['MiUsuario']->getPasswordAsString());
    }

    public function test_rol_con_pau_se_copia_a_la_sesion(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_CON_DMZ]);

        $this->assertTrue($this->login()['ok']);
        $this->assertSame('sacd', $_SESSION['session_auth']['role_pau']);
        $this->assertSame('sacd', $_SESSION['config']['role_pau']);
    }

    public function test_preferencias_de_idioma_y_orden_de_apellidos(): void
    {
        $this->crearUsuario();
        $this->guardarPreferencia('idioma', 'ca_ES.UTF-8');
        $this->guardarPreferencia('ordenApellidos', 'ap_nom');

        $this->assertTrue($this->login()['ok']);
        $this->assertSame('ca_ES.UTF-8', $_SESSION['session_auth']['idioma']);
        $this->assertSame('ca_ES.UTF-8', $_SESSION['config']['idioma']);
        $this->assertSame('ap_nom', $_SESSION['session_auth']['ordenApellidos']);
    }

    // ------------------------------------------------------------------ cambio de contraseña obligatorio

    public function test_cambio_de_contrasena_obligatorio_se_avisa_a_la_app(): void
    {
        $this->crearUsuario(['cambio_password' => true]);

        $r = $this->login();

        $this->assertTrue($r['ok']);
        $this->assertTrue($r['data']['require_password_change']);
        $this->assertSame(1, $_SESSION['session_auth']['expire']);
        $this->assertSame(1, $_SESSION['config']['expire']);
    }

    public function test_la_contrasena_inicial_obliga_a_cambiarla(): void
    {
        $inicial = '1ªVegada';
        $this->crearUsuario(['password' => (new PasswordHasher())->encode($inicial)]);

        $r = $this->login(['password' => $inicial]);

        $this->assertTrue($r['ok']);
        $this->assertTrue($r['data']['require_password_change']);
    }

    // ------------------------------------------------------------------ 2FA

    public function test_2fa_activado_sin_secreto_devuelve_la_url_de_ayuda(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => null]);

        $r = $this->login();

        $this->assertRechazadoSinFugas($r, 'need_2fa_setup');
        $this->assertSame(_("Debe completar la configuración 2FA"), $r['mensaje']);
        $url = $r['data']['ayuda_url'];
        $this->assertStringContainsString('frontend/usuarios/controller/ayuda_2fa_reset.php?', $url);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame(self::USUARIO, $query['username']);
        $this->assertSame(self::ESQUEMA, $query['esquema']);
        $this->assertSame('sv', $query['ubicacion']);
        $this->assertArrayNotHasKey('password', $query);
    }

    /** Sin la contraseña correcta no se revela que el usuario tiene 2FA (ni pendiente ni activo). */
    public function test_con_contrasena_incorrecta_no_se_revela_el_estado_2fa(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => null]);
        $this->assertSame(self::rechazo(), $this->login(['password' => 'mala']));

        $this->borrarUsuarioDePrueba();
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $this->assertSame(self::rechazo(), $this->login(['password' => 'mala']));
        $this->assertSame(
            self::rechazo(),
            $this->login(['password' => 'mala', 'verification_code' => $this->codigoTotp(self::SECRETO_2FA)])
        );
    }

    public function test_2fa_sin_codigo_pide_el_codigo(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $r = $this->login();

        $this->assertSame(['ok' => false, 'code' => 'need_2fa', 'mensaje' => _("Código 2FA requerido")], $r);
        $this->assertSame($r, $this->login(['verification_code' => '   ']));
        $this->assertRechazadoSinFugas($r, 'need_2fa');
    }

    #[DataProvider('codigosInvalidos')]
    public function test_2fa_con_codigo_incorrecto_se_rechaza(string $codigo): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $r = $this->login(['verification_code' => $codigo]);

        $this->assertSame(['ok' => false, 'code' => 'invalid_2fa', 'mensaje' => _("Código 2FA inválido")], $r);
        $this->assertSame($this->sesionPrevia['session_auth'], $_SESSION['session_auth']);
    }

    /** @return iterable<string, array{string}> */
    public static function codigosInvalidos(): iterable
    {
        yield 'letras' => ['abcdef'];
        yield 'corto' => ['12345'];
        yield 'largo' => ['1234567'];
        yield 'el propio secreto' => [self::SECRETO_2FA];
    }

    public function test_2fa_con_codigo_numerico_no_valido_se_rechaza(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $r = $this->login(['verification_code' => $this->codigoTotpIncorrecto(self::SECRETO_2FA)]);

        $this->assertRechazadoSinFugas($r, 'invalid_2fa');
    }

    public function test_2fa_con_codigo_caducado_se_rechaza(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $viejo = $this->codigoTotp(self::SECRETO_2FA, -10);
        if (in_array($viejo, [$this->codigoTotp(self::SECRETO_2FA, -1), $this->codigoTotp(self::SECRETO_2FA), $this->codigoTotp(self::SECRETO_2FA, 1)], true)) {
            $this->markTestSkipped('Colisión casual con un código vigente.');
        }

        $this->assertSame('invalid_2fa', $this->login(['verification_code' => $viejo])['code']);
    }

    public function test_2fa_con_codigo_valido_entra(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $r = $this->login(['verification_code' => ' ' . $this->codigoTotp(self::SECRETO_2FA) . ' ']);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ID_USUARIO, $_SESSION['session_auth']['id_usuario']);
        $this->assertStringNotContainsString(self::SECRETO_2FA, $this->comoTexto($r));
    }

    /** Con `has_2fa` desactivado el secreto guardado no obliga a nada. */
    public function test_secreto_guardado_con_2fa_desactivado_no_pide_codigo(): void
    {
        $this->crearUsuario(['has_2fa' => false, 'secret_2fa' => self::SECRETO_2FA]);

        $this->assertTrue($this->login()['ok']);
    }

    // ------------------------------------------------------------------ rol y DMZ

    /**
     * Hallazgo menor: con la contraseña correcta y un rol que no existe (o sin rol) la respuesta es
     * `server_error` / «Rol no encontrado», distinta de la de credenciales incorrectas.
     */
    #[DataProvider('rolesQueNoExisten')]
    public function test_usuario_sin_rol_valido_se_rechaza(?int $idRole): void
    {
        $this->crearUsuario(['id_role' => $idRole]);

        $r = $this->login();

        $this->assertSame(['ok' => false, 'code' => 'server_error', 'mensaje' => _("Rol no encontrado")], $r);
        $this->assertRechazadoSinFugas($r, 'server_error');
    }

    /** @return iterable<string, array{?int}> */
    public static function rolesQueNoExisten(): iterable
    {
        yield 'sin rol' => [null];
        yield 'rol inexistente' => [self::ROL_INEXISTENTE];
    }

    public function test_en_la_dmz_un_rol_sin_permiso_dmz_se_rechaza(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = true;

        $r = $this->login();

        $this->assertSame(['ok' => false, 'code' => 'dmz_denied', 'mensaje' => _("Acceso no permitido")], $r);
        $this->assertRechazadoSinFugas($r, 'dmz_denied');
    }

    public function test_en_la_dmz_con_contrasena_incorrecta_el_error_es_el_generico(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = true;

        $this->assertSame(self::rechazo(), $this->login(['password' => 'mala']));
    }

    public function test_en_la_dmz_un_rol_con_permiso_dmz_entra(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_CON_DMZ]);
        ConfigGlobal::$dmz = true;

        $this->assertTrue($this->login()['ok']);
    }

    /** La entrada privada «sf» (variable PRIVATE, que el login copia a la sesión) anula la DMZ. */
    public function test_en_la_dmz_la_entrada_privada_sf_no_exige_permiso_dmz(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = true;
        putenv('PRIVATE=sf');

        $this->assertTrue($this->login()['ok']);
        $this->assertSame('sf', $_SESSION['private']);
    }
}
