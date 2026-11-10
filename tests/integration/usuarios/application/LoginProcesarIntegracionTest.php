<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\application;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\config\ConfigGlobal;
use src\usuarios\application\LoginProcesar;
use src\usuarios\domain\entity\Usuario;
use Tests\myTest;

/**
 * Login web (`LoginProcesar`) contra la BD de desarrollo, con un único usuario de prueba
 * (id 4990021) que se crea y se borra en cada test. Ver {@see UsuarioDePruebaLogin}.
 *
 * Códigos de error del caso de uso: 1 = usuario o contraseña no válidos, 2 = sin permiso DMZ,
 * 3 = falta el código 2FA, 4 = código 2FA inválido.
 */
class LoginProcesarIntegracionTest extends myTest
{
    use UsuarioDePruebaLogin;

    private const RECHAZO = ['ok' => false, 'error' => 1];

    public function setUp(): void
    {
        parent::setUp();
        $this->prepararEscenarioLogin();
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
    private function login(array $input = [], string $esquemaWeb = '', string $ubicacion = 'sv'): array
    {
        $input += ['username' => self::USUARIO, 'password' => self::CLAVE, 'esquema' => self::ESQUEMA];

        return (new LoginProcesar())->execute($input, $esquemaWeb, $ubicacion);
    }

    // ------------------------------------------------------------------ credenciales incorrectas

    public function test_usuario_inexistente_se_rechaza(): void
    {
        $this->assertSame(self::RECHAZO, $this->login(['username' => 'tstu_no_existe_zz']));
    }

    public function test_usuario_vacio_se_rechaza(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['username' => '']));
    }

    public function test_contrasena_incorrecta_se_rechaza(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['password' => 'otra-cosa']));
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

        $this->assertSame(self::RECHAZO, $this->login(['password' => $password]));
    }

    /** @return iterable<string, array{string}> */
    public static function contrasenasCasiCorrectas(): iterable
    {
        yield 'vacía' => [''];
        yield 'en minúsculas' => [strtolower(self::CLAVE)];
        yield 'con espacio final' => [self::CLAVE . ' '];
        yield 'con espacio inicial' => [' ' . self::CLAVE];
        yield 'sin el último carácter' => [substr(self::CLAVE, 0, -1)];
        yield 'con byte nulo' => [self::CLAVE . "\0"];
    }

    public function test_el_hash_guardado_no_sirve_como_contrasena(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['password' => $this->hashGuardado()]));
    }

    public function test_usuario_sin_contrasena_en_bd_no_puede_entrar(): void
    {
        $this->crearUsuario(['password' => null]);

        $this->assertSame(self::RECHAZO, $this->login(['password' => '']));
        $this->assertSame(self::RECHAZO, $this->login(['password' => self::CLAVE]));
    }

    /**
     * Hallazgo: un hash vacío en BD no deja entrar, pero tampoco da el error 1: el VO `Password`
     * lanza `InvalidArgumentException` al hidratar el usuario, ANTES de comparar la contraseña.
     */
    public function test_usuario_con_hash_vacio_en_bd_lanza_excepcion_sin_controlar(): void
    {
        $this->crearUsuario(['password' => '']);

        $this->expectException(\InvalidArgumentException::class);

        $this->login(['password' => '']);
    }

    #[DataProvider('usuariosConInyeccion')]
    public function test_inyeccion_sql_en_el_usuario_no_permite_entrar(string $username): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['username' => $username]));
        $this->assertSame(self::RECHAZO, $this->login(['username' => $username, 'password' => '']));
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

        $this->assertSame(self::RECHAZO, $this->login(['username' => strtoupper(self::USUARIO)]));
    }

    /** El login web no recorta el usuario (la app móvil sí: ver AppMobileLoginIntegracionTest). */
    public function test_el_nombre_de_usuario_con_espacios_se_rechaza(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['username' => ' ' . self::USUARIO . ' ']));
    }

    /**
     * Hallazgo: no hay límite de intentos fallidos ni bloqueo temporal; tras varios fallos seguidos
     * la contraseña correcta entra a la primera (nada frena un ataque de fuerza bruta en esta capa).
     */
    public function test_los_intentos_fallidos_no_bloquean_al_usuario(): void
    {
        $this->crearUsuario();
        for ($i = 0; $i < 6; $i++) {
            $this->assertSame(self::RECHAZO, $this->login(['password' => 'intento-' . $i]));
        }

        $this->assertTrue($this->login()['ok']);
    }

    // ------------------------------------------------------------------ esquema

    public function test_esquema_del_formulario_vacio_usa_el_del_entorno(): void
    {
        $this->crearUsuario();

        $r = $this->login(['esquema' => ''], self::ESQUEMA);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['esquema']);
    }

    /**
     * Hallazgo: si la instalación fija el esquema con la variable de entorno ESQUEMA, el esquema del
     * formulario sigue teniendo prioridad: el cliente puede autenticarse contra otra dl.
     */
    public function test_el_esquema_del_formulario_prevalece_sobre_el_del_entorno(): void
    {
        $this->crearUsuario();

        $r = $this->login(['esquema' => self::ESQUEMA], 'H-Hv');

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['esquema']);
    }

    /**
     * Hallazgo: un esquema que no está en los ficheros de conexión NO devuelve error 1: `ConfigDB`
     * lanza `RuntimeException` (el caso de uso solo captura `PDOException`) y el mensaje incluye la
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
     * `=== false` de prepare/execute/query son código muerto (las PDO usan ERRMODE_EXCEPTION).
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

    public function test_login_correcto_devuelve_los_datos_de_sesion(): void
    {
        $this->crearUsuario();

        $r = $this->login();

        $this->assertSame(['ok', 'session_auth', 'session_config', 'esquema', 'idioma', 'sfsv'], array_keys($r));
        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['esquema']);
        $this->assertSame(1, $r['sfsv']);
        $this->assertSame('', $r['idioma']);

        $auth = $r['session_auth'];
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
        $this->assertSame((string) $_SESSION['session_auth']['mi_id_schema'], (string) $auth['mi_id_schema']);
        $this->assertInstanceOf(Usuario::class, $auth['MiUsuario']);
        $this->assertSame(self::ID_USUARIO, $auth['MiUsuario']->getId_usuario());
        $this->assertSame(self::ROL_SIN_DMZ, $auth['MiUsuario']->getId_role());
        $this->assertSame(self::USUARIO, $auth['MiUsuario']->getUsuarioAsString());

        $config = $r['session_config'];
        $this->assertSame(self::ROL_SIN_DMZ, $config['id_role']);
        $this->assertSame(self::USUARIO, $config['username']);
        $this->assertNotEmpty($config['a_apps']);
        $this->assertNotEmpty($config['a_mods']);
        $this->assertNotEmpty($config['mod_installed']);
        $this->assertNotEmpty($config['app_installed']);
        $this->assertContainsOnly('int', $config['app_installed']);
        $this->assertSame(array_values(array_unique($config['app_installed'])), $config['app_installed']);
        foreach ($config['mod_installed'] as $idMod => $nombre) {
            $this->assertSame($config['a_mods'][$idMod]['nom'], $nombre);
        }
    }

    /** El caso de uso no escribe la sesión: lo hace el controlador frontend. */
    public function test_el_caso_de_uso_no_toca_la_sesion(): void
    {
        $this->crearUsuario();
        $antes = $_SESSION;

        $this->login();
        $this->login(['password' => 'mala']);

        $this->assertSame($antes, $_SESSION);
    }

    /**
     * Hallazgo: la contraseña en claro viaja en `session_auth` y `session_config` (acaba en el fichero
     * de sesión del servidor). El hash guardado va dentro de la entidad `MiUsuario`.
     */
    public function test_la_contrasena_en_claro_se_devuelve_para_la_sesion(): void
    {
        $this->crearUsuario();

        $r = $this->login();

        $this->assertSame(self::CLAVE, $r['session_auth']['password']);
        $this->assertSame(self::CLAVE, $r['session_config']['password']);
        $this->assertSame($this->hashGuardado(), $r['session_auth']['MiUsuario']->getPasswordAsString());
    }

    public function test_rol_con_pau_se_copia_a_la_sesion(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_CON_DMZ]);

        $r = $this->login();

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ROL_CON_DMZ, $r['session_auth']['id_role']);
        $this->assertSame('sacd', $r['session_auth']['role_pau']);
        $this->assertSame('sacd', $r['session_config']['role_pau']);
    }

    public function test_preferencias_de_idioma_y_orden_de_apellidos(): void
    {
        $this->crearUsuario();
        $this->guardarPreferencia('idioma', 'ca_ES.UTF-8');
        $this->guardarPreferencia('ordenApellidos', 'ap_nom');

        $r = $this->login();

        $this->assertSame('ca_ES.UTF-8', $r['idioma']);
        $this->assertSame('ca_ES.UTF-8', $r['session_auth']['idioma']);
        $this->assertSame('ca_ES.UTF-8', $r['session_config']['idioma']);
        $this->assertSame('ap_nom', $r['session_auth']['ordenApellidos']);
    }

    public function test_usuario_sin_email_deja_el_mail_vacio(): void
    {
        $this->crearUsuario(['email' => null]);

        $this->assertSame('', $this->login()['session_auth']['mail']);
    }

    // ------------------------------------------------------------------ cambio de contraseña obligatorio

    public function test_cambio_de_contrasena_obligatorio_marca_expire(): void
    {
        $this->crearUsuario(['cambio_password' => true]);

        $r = $this->login();

        $this->assertTrue($r['ok']);
        $this->assertSame(1, $r['session_auth']['expire']);
        $this->assertSame(1, $r['session_config']['expire']);
    }

    public function test_la_contrasena_inicial_marca_expire(): void
    {
        $inicial = '1ªVegada';
        $this->crearUsuario(['password' => (new \src\usuarios\domain\PasswordHasher())->encode($inicial)]);

        $r = $this->login(['password' => $inicial]);

        $this->assertTrue($r['ok']);
        $this->assertSame(1, $r['session_auth']['expire']);
    }

    // ------------------------------------------------------------------ 2FA

    public function test_2fa_activado_sin_secreto_redirige_a_la_ayuda(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => null]);

        $r = $this->login([], '', 'sv');

        $this->assertSame(
            [
                'ok' => false,
                'redirect_ayuda_2fa' => ['username' => self::USUARIO, 'ubicacion' => 'sv', 'esquema' => self::ESQUEMA],
            ],
            $r
        );
    }

    /** Sin la contraseña correcta no se revela que el usuario tiene 2FA (ni pendiente ni activo). */
    public function test_con_contrasena_incorrecta_no_se_revela_el_estado_2fa(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => null]);
        $this->assertSame(self::RECHAZO, $this->login(['password' => 'mala']));

        $this->borrarUsuarioDePrueba();
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $this->assertSame(self::RECHAZO, $this->login(['password' => 'mala']));
        $this->assertSame(
            self::RECHAZO,
            $this->login(['password' => 'mala', 'verification_code' => $this->codigoTotp(self::SECRETO_2FA)])
        );
    }

    public function test_2fa_sin_codigo_pide_el_codigo(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $this->assertSame(['ok' => false, 'error' => 3], $this->login());
        $this->assertSame(['ok' => false, 'error' => 3], $this->login(['verification_code' => '']));
    }

    #[DataProvider('codigosInvalidos')]
    public function test_2fa_con_codigo_incorrecto_se_rechaza(string $codigo): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $codigo]));
    }

    /** @return iterable<string, array{string}> */
    public static function codigosInvalidos(): iterable
    {
        yield 'letras' => ['abcdef'];
        yield 'corto' => ['12345'];
        yield 'largo' => ['1234567'];
        yield 'espacios' => ['   '];
        yield 'el propio secreto' => [self::SECRETO_2FA];
    }

    public function test_2fa_con_codigo_numerico_no_valido_se_rechaza(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $r = $this->login(['verification_code' => $this->codigoTotpIncorrecto(self::SECRETO_2FA)]);

        $this->assertSame(['ok' => false, 'error' => 4], $r);
    }

    public function test_2fa_con_codigo_de_otro_secreto_se_rechaza(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $ajeno = $this->codigoTotp('GEZDGNBVGY3TQOJQ');
        if (in_array($ajeno, [$this->codigoTotp(self::SECRETO_2FA, -1), $this->codigoTotp(self::SECRETO_2FA), $this->codigoTotp(self::SECRETO_2FA, 1)], true)) {
            $this->markTestSkipped('Colisión casual entre los dos secretos en esta ventana.');
        }

        $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $ajeno]));
    }

    public function test_2fa_con_codigo_caducado_se_rechaza(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $viejo = $this->codigoTotp(self::SECRETO_2FA, -10);
        if (in_array($viejo, [$this->codigoTotp(self::SECRETO_2FA, -1), $this->codigoTotp(self::SECRETO_2FA), $this->codigoTotp(self::SECRETO_2FA, 1)], true)) {
            $this->markTestSkipped('Colisión casual con un código vigente.');
        }

        $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $viejo]));
    }

    public function test_2fa_con_codigo_valido_entra(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $r = $this->login(['verification_code' => $this->codigoTotp(self::SECRETO_2FA)]);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ID_USUARIO, $r['session_auth']['id_usuario']);
    }

    /** Se acepta la ventana anterior (desfase de reloj) y los espacios del código. */
    public function test_2fa_acepta_la_ventana_anterior_y_espacios(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $anterior = $this->codigoTotp(self::SECRETO_2FA, -1);

        $r = $this->login(['verification_code' => substr($anterior, 0, 3) . ' ' . substr($anterior, 3)]);

        $this->assertTrue($r['ok']);
    }

    /**
     * Hallazgo: un código TOTP válido se puede reutilizar dentro de su ventana (hasta ~90 s): no se
     * guarda el último código aceptado.
     */
    public function test_2fa_el_mismo_codigo_sirve_dos_veces(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $codigo = $this->codigoTotp(self::SECRETO_2FA);

        $this->assertTrue($this->login(['verification_code' => $codigo])['ok']);
        $this->assertTrue($this->login(['verification_code' => $codigo])['ok']);
    }

    /** Con `has_2fa` desactivado el secreto guardado no obliga a nada. */
    public function test_secreto_guardado_con_2fa_desactivado_no_pide_codigo(): void
    {
        $this->crearUsuario(['has_2fa' => false, 'secret_2fa' => self::SECRETO_2FA]);

        $this->assertTrue($this->login()['ok']);
    }

    // ------------------------------------------------------------------ rol y DMZ

    public function test_usuario_sin_rol_se_rechaza(): void
    {
        $this->crearUsuario(['id_role' => null]);

        $this->assertSame(self::RECHAZO, $this->login());
    }

    public function test_usuario_con_rol_inexistente_se_rechaza(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_INEXISTENTE]);

        $this->assertSame(self::RECHAZO, $this->login());
    }

    public function test_en_la_dmz_un_rol_sin_permiso_dmz_se_rechaza(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = true;
        unset($_SESSION['private']);

        $this->assertSame(['ok' => false, 'error' => 2], $this->login());
    }

    /** En la DMZ la contraseña se comprueba antes que el rol: sin ella no se sabe si el rol tiene DMZ. */
    public function test_en_la_dmz_con_contrasena_incorrecta_el_error_es_el_generico(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = true;
        unset($_SESSION['private']);

        $this->assertSame(self::RECHAZO, $this->login(['password' => 'mala']));
    }

    public function test_en_la_dmz_un_rol_con_permiso_dmz_entra(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_CON_DMZ]);
        ConfigGlobal::$dmz = true;
        unset($_SESSION['private']);

        $this->assertTrue($this->login()['ok']);
    }

    /** La entrada privada «sf» anula la restricción DMZ (`ConfigGlobal::is_dmz`). */
    public function test_en_la_dmz_la_entrada_privada_sf_no_exige_permiso_dmz(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = true;
        $_SESSION['private'] = 'sf';

        $this->assertTrue($this->login()['ok']);
    }

    public function test_fuera_de_la_dmz_no_se_exige_permiso_dmz(): void
    {
        $this->crearUsuario(['id_role' => self::ROL_SIN_DMZ]);
        ConfigGlobal::$dmz = false;

        $this->assertTrue($this->login()['ok']);
    }
}
