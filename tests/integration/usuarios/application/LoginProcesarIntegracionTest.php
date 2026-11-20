<?php

declare(strict_types=1);

namespace Tests\integration\usuarios\application;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\config\ConfigGlobal;
use src\usuarios\application\LoginProcesar;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\infrastructure\persistence\FicheroLoginCliTicketRepository;
use src\usuarios\infrastructure\persistence\postgresql\PgLoginIntentosRepository;
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

    private string $dirTickets;
    private FicheroLoginCliTicketRepository $tickets;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepararEscenarioLogin();
        $this->dirTickets = sys_get_temp_dir() . '/orbix_test_login_cli_' . bin2hex(random_bytes(4));
        $this->tickets = new FicheroLoginCliTicketRepository($this->dirTickets);
    }

    public function tearDown(): void
    {
        foreach (glob($this->dirTickets . '/*') ?: [] as $f) {
            unlink($f);
        }
        if (is_dir($this->dirTickets)) {
            rmdir($this->dirTickets);
        }
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

        return $this->loginProcesar()->execute($input, $esquemaWeb, $ubicacion);
    }

    private function loginProcesar(): LoginProcesar
    {
        return new LoginProcesar($this->tickets, new PgLoginIntentosRepository());
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

    /** Un hash vacío en BD es como no tener contraseña: error 1, sin excepción al hidratar el usuario. */
    public function test_usuario_con_hash_vacio_en_bd_se_rechaza(): void
    {
        $this->crearUsuario(['password' => '']);

        $this->assertSame(self::RECHAZO, $this->login(['password' => '']));
        $this->assertSame(self::RECHAZO, $this->login(['password' => self::CLAVE]));
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

    // ------------------------------------------------------------------ bloqueo por intentos fallidos

    private const BLOQUEADO = ['ok' => false, 'error' => 5];

    /** Tras 5 fallos seguidos se rechaza durante 15 min aunque la contraseña sea correcta (error 5). */
    public function test_cinco_fallos_seguidos_bloquean_aunque_la_contrasena_sea_correcta(): void
    {
        $this->crearUsuario();
        for ($i = 0; $i < 5; $i++) {
            $this->assertSame(self::RECHAZO, $this->login(['password' => 'intento-' . $i]));
        }

        $this->assertSame(self::BLOQUEADO, $this->login());
        $this->assertSame(self::BLOQUEADO, $this->login(['password' => 'otro-intento']));
        $this->assertGreaterThan(14 * 60, $this->segundosDeBloqueo());
        $this->assertLessThanOrEqual(15 * 60, $this->segundosDeBloqueo());
    }

    /** Un acierto pone el contador a cero: los fallos tienen que ser seguidos. */
    public function test_un_acierto_pone_el_contador_a_cero(): void
    {
        $this->crearUsuario();
        for ($vuelta = 0; $vuelta < 2; $vuelta++) {
            for ($i = 0; $i < 4; $i++) {
                $this->assertSame(self::RECHAZO, $this->login(['password' => 'intento-' . $i]));
            }
            $this->assertTrue($this->login()['ok']);
        }
        $this->assertSame(0, $this->falloDeLogin());
    }

    /** Pasado el bloqueo se puede entrar, y hacen falta otros 5 fallos para volver a bloquear. */
    public function test_el_bloqueo_caduca(): void
    {
        $this->crearUsuario();
        for ($i = 0; $i < 5; $i++) {
            $this->login(['password' => 'intento-' . $i]);
        }
        $this->caducarBloqueo();

        $this->assertSame(self::RECHAZO, $this->login(['password' => 'uno-mas']));
        $this->assertTrue($this->login()['ok']);
    }

    /** Los códigos 2FA erróneos cuentan como fallos (si no, se podría probar el código a fuerza bruta). */
    public function test_los_codigos_2fa_erroneos_cuentan_para_el_bloqueo(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        for ($i = 0; $i < 5; $i++) {
            $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $this->codigoTotpIncorrecto(self::SECRETO_2FA)]));
        }

        $this->assertSame(self::BLOQUEADO, $this->login(['verification_code' => $this->codigoTotp(self::SECRETO_2FA)]));
    }

    /** Pedir el código 2FA (contraseña correcta, sin código) no es un fallo. */
    public function test_pedir_el_codigo_2fa_no_cuenta_como_fallo(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        for ($i = 0; $i < 6; $i++) {
            $this->assertSame(['ok' => false, 'error' => 3], $this->login());
        }

        $this->assertSame(0, $this->falloDeLogin());
    }

    // ------------------------------------------------------------------ esquema

    public function test_esquema_del_formulario_vacio_usa_el_del_entorno(): void
    {
        $this->crearUsuario();

        $r = $this->login(['esquema' => ''], self::ESQUEMA);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['esquema']);
    }

    /** Si la instalación fija el esquema con ESQUEMA, el del formulario no cuenta. */
    public function test_el_esquema_del_entorno_prevalece_sobre_el_del_formulario(): void
    {
        $this->crearUsuario();

        $r = $this->login(['esquema' => 'H-Hv'], self::ESQUEMA);

        $this->assertTrue($r['ok']);
        $this->assertSame(self::ESQUEMA, $r['esquema']);
    }

    public function test_con_esquema_del_entorno_no_se_entra_en_otra_dl_por_el_formulario(): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['esquema' => self::ESQUEMA], 'Xx-noexistev'));
    }

    /** Un esquema que no está en los ficheros de conexión es error 1, sin excepción ni rutas del servidor. */
    #[DataProvider('esquemasDesconocidos')]
    public function test_esquema_desconocido_se_rechaza(string $esquema): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['esquema' => $esquema]));
    }

    /** @return iterable<string, array{string}> */
    public static function esquemasDesconocidos(): iterable
    {
        yield 'sv inexistente' => ['Xx-noexistev'];
        yield 'comun inexistente' => ['Xx-noexiste'];
        yield 'con comilla' => ["a'b"];
        yield 'solo v' => ['v'];
    }

    /** Un esquema de la BD común abre conexión pero no tiene `aux_usuarios`: error 1, sin `PDOException`. */
    #[DataProvider('esquemasDeLaBdComun')]
    public function test_esquema_de_la_bd_comun_se_rechaza(string $esquema): void
    {
        $this->crearUsuario();

        $this->assertSame(self::RECHAZO, $this->login(['esquema' => $esquema]));
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

    /** La contraseña en claro no va a la sesión (acabaría en el fichero de sesión del servidor). */
    public function test_la_sesion_no_guarda_la_contrasena(): void
    {
        $this->crearUsuario();

        $r = $this->login();

        $this->assertTrue($r['ok']);
        $this->assertArrayNotHasKey('password', $r['session_auth']);
        $this->assertArrayNotHasKey('password', $r['session_config']);
        $this->assertStringNotContainsString(self::CLAVE, serialize([$r['session_auth'], $r['session_config']]));
        // Tampoco el hash: el usuario de la sesión va sin credenciales.
        $this->assertNull($r['session_auth']['MiUsuario']->getPasswordAsString());
        $this->assertStringNotContainsString($this->hashGuardado(), serialize($r['session_auth']));
    }

    // ------------------------------------------------------------------ login con ticket (procesos CLI)

    public function test_un_ticket_valido_inicia_la_sesion_del_usuario_sin_contrasena(): void
    {
        $this->crearUsuario();
        $ticket = $this->tickets->emitir(self::USUARIO, self::ESQUEMA);

        $r = $this->loginProcesar()->executeConTicketCli($ticket, 'sv');

        $this->assertTrue($r['ok']);
        $this->assertSame(self::USUARIO, $r['session_auth']['username']);
        $this->assertSame(self::ID_USUARIO, $r['session_auth']['id_usuario']);
        $this->assertSame(self::ESQUEMA, $r['esquema']);
        $this->assertArrayNotHasKey('password', $r['session_auth']);
    }

    /** El ticket es de un solo uso; uno desconocido o vacío no inicia sesión. */
    public function test_un_ticket_usado_o_desconocido_se_rechaza(): void
    {
        $this->crearUsuario();
        $ticket = $this->tickets->emitir(self::USUARIO, self::ESQUEMA);
        $this->assertTrue($this->loginProcesar()->executeConTicketCli($ticket, 'sv')['ok']);

        $this->assertSame(self::RECHAZO, $this->loginProcesar()->executeConTicketCli($ticket, 'sv'));
        $this->assertSame(self::RECHAZO, $this->loginProcesar()->executeConTicketCli(str_repeat('0', 64), 'sv'));
        $this->assertSame(self::RECHAZO, $this->loginProcesar()->executeConTicketCli('', 'sv'));
    }

    /** El ticket no sirve para un usuario que ya no existe. */
    public function test_un_ticket_de_un_usuario_borrado_se_rechaza(): void
    {
        $ticket = $this->tickets->emitir(self::USUARIO, self::ESQUEMA);

        $this->assertSame(self::RECHAZO, $this->loginProcesar()->executeConTicketCli($ticket, 'sv'));
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

    /** Un código TOTP aceptado no sirve otra vez (ni uno de una ventana anterior), y el intento cuenta como fallo. */
    public function test_2fa_un_codigo_no_sirve_dos_veces(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);
        $codigo = $this->codigoTotp(self::SECRETO_2FA);

        $this->assertTrue($this->login(['verification_code' => $codigo])['ok']);
        $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $codigo]));
        $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $this->codigoTotp(self::SECRETO_2FA, -1)]));
        $this->assertSame(2, $this->falloDeLogin());
    }

    /** El código de la ventana siguiente (reloj del móvil adelantado) se acepta, y después ya no el actual. */
    public function test_2fa_tras_aceptar_un_codigo_solo_vale_uno_posterior(): void
    {
        $this->crearUsuario(['has_2fa' => true, 'secret_2fa' => self::SECRETO_2FA]);

        $this->assertTrue($this->login(['verification_code' => $this->codigoTotp(self::SECRETO_2FA, 1)])['ok']);
        $this->assertSame(['ok' => false, 'error' => 4], $this->login(['verification_code' => $this->codigoTotp(self::SECRETO_2FA)]));
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
