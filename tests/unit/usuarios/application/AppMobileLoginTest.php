<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\usuarios\application\AppMobileLogin;

/**
 * Validaciones de entrada de `AppMobileLogin` que se resuelven ANTES de abrir ninguna conexión.
 *
 * El resto del caso de uso crea sus conexiones con `new ConfigDB` / `new DBConnection` (no admite
 * mocks) y se prueba en `Tests\integration\usuarios\application\AppMobileLoginIntegracionTest`.
 */
final class AppMobileLoginTest extends TestCase
{
    /** @var array<string, mixed>|null */
    private ?array $sesionPrevia = null;
    /** @var array<string, string|false> */
    private array $entornoPrevio = [];

    protected function setUp(): void
    {
        $this->sesionPrevia = $_SESSION ?? null;
        foreach (['ESQUEMA', 'PRIVATE', 'UBICACION'] as $var) {
            $this->entornoPrevio[$var] = getenv($var);
        }
        putenv('ESQUEMA');
        putenv('PRIVATE');
        putenv('UBICACION=sv');
        $_SESSION = ['session_auth' => ['id_usuario' => 7, 'esquema' => 'H-dlbv', 'username' => 'previo']];
    }

    protected function tearDown(): void
    {
        foreach ($this->entornoPrevio as $var => $valor) {
            putenv($valor === false ? $var : $var . '=' . $valor);
        }
        if ($this->sesionPrevia === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->sesionPrevia;
        }
    }

    /**
     * @param array<string, string> $input
     */
    #[DataProvider('credencialesIncompletas')]
    public function test_sin_usuario_o_sin_contrasena_se_rechaza_sin_consultar_nada(array $input): void
    {
        $r = (new AppMobileLogin())->execute($input);

        $this->assertSame(
            ['ok' => false, 'code' => 'missing_credentials', 'mensaje' => _("Usuario y contraseña obligatorios")],
            $r
        );
        $this->assertSame(7, $_SESSION['session_auth']['id_usuario'], 'la sesión previa no se toca');
        $this->assertArrayNotHasKey('config', $_SESSION);
    }

    /** @return iterable<string, array{array<string, string>}> */
    public static function credencialesIncompletas(): iterable
    {
        yield 'petición vacía' => [[]];
        yield 'sin contraseña' => [['username' => 'alguien', 'esquema' => 'H-dlbv']];
        yield 'contraseña vacía' => [['username' => 'alguien', 'password' => '', 'esquema' => 'H-dlbv']];
        yield 'sin usuario' => [['password' => 'secreto', 'esquema' => 'H-dlbv']];
        yield 'usuario vacío' => [['username' => '', 'password' => 'secreto', 'esquema' => 'H-dlbv']];
        yield 'usuario solo con espacios' => [['username' => "  \t ", 'password' => 'secreto', 'esquema' => 'H-dlbv']];
    }

    /** Las credenciales se validan antes que el esquema: sin ellas no se dice nada del esquema. */
    public function test_las_credenciales_se_validan_antes_que_el_esquema(): void
    {
        $r = (new AppMobileLogin())->execute(['username' => '', 'password' => '', 'esquema' => 'no-existe']);

        $this->assertSame('missing_credentials', $r['code']);
    }

    /**
     * @param array<string, string> $input
     */
    #[DataProvider('sinEsquema')]
    public function test_sin_esquema_en_la_peticion_ni_en_el_entorno_se_rechaza(array $input): void
    {
        $r = (new AppMobileLogin())->execute($input);

        $this->assertSame(['ok' => false, 'code' => 'missing_schema', 'mensaje' => _("Esquema no indicado")], $r);
        $this->assertSame(7, $_SESSION['session_auth']['id_usuario']);
    }

    /** @return iterable<string, array{array<string, string>}> */
    public static function sinEsquema(): iterable
    {
        yield 'sin clave esquema' => [['username' => 'alguien', 'password' => 'secreto']];
        yield 'esquema vacío' => [['username' => 'alguien', 'password' => 'secreto', 'esquema' => '']];
        yield 'esquema solo con espacios' => [['username' => 'alguien', 'password' => 'secreto', 'esquema' => '   ']];
        yield 'contraseña solo con espacios (no se recorta)' => [['username' => 'alguien', 'password' => '   ']];
    }

    /** Fuera de la entrada sf, el sufijo «f» se recorta: un esquema «f» queda vacío y no hay conexión. */
    public function test_esquema_que_queda_vacio_al_quitar_el_sufijo_f_no_es_valido(): void
    {
        $r = (new AppMobileLogin())->execute(['username' => 'alguien', 'password' => 'secreto', 'esquema' => 'f']);

        $this->assertSame(['ok' => false, 'code' => 'invalid_schema', 'mensaje' => _("Esquema no válido")], $r);
        $this->assertSame(7, $_SESSION['session_auth']['id_usuario']);
    }

    /** Ningún rechazo devuelve la contraseña ni el usuario recibidos. */
    public function test_los_rechazos_no_devuelven_los_datos_recibidos(): void
    {
        $login = new AppMobileLogin();
        $respuestas = [
            $login->execute(['username' => 'usuario-zz', 'password' => '']),
            $login->execute(['username' => 'usuario-zz', 'password' => 'clave-zz']),
            $login->execute(['username' => 'usuario-zz', 'password' => 'clave-zz', 'esquema' => 'f']),
        ];

        foreach ($respuestas as $r) {
            $texto = (string) json_encode($r, JSON_UNESCAPED_UNICODE);
            $this->assertFalse($r['ok']);
            $this->assertArrayNotHasKey('data', $r);
            $this->assertStringNotContainsString('usuario-zz', $texto);
            $this->assertStringNotContainsString('clave-zz', $texto);
        }
    }

    /**
     * El login anota la ubicación (`sfsv`) siempre, y la entrada privada (`private`) solo cuando hay
     * credenciales. Hallazgo menor: lo hace aunque el login se rechace (la sesión del visitante
     * anónimo queda con esas dos claves).
     */
    public function test_un_rechazo_deja_ubicacion_y_entrada_privada_en_la_sesion(): void
    {
        putenv('PRIVATE=sf');

        (new AppMobileLogin())->execute([]);
        $this->assertSame('sv', $_SESSION['sfsv']);
        $this->assertArrayNotHasKey('private', $_SESSION);

        (new AppMobileLogin())->execute(['username' => 'alguien', 'password' => 'secreto']);
        $this->assertSame('sf', $_SESSION['private']);
    }

    #[DataProvider('referenciasDeApp')]
    public function test_resolucion_del_id_de_app_instalada(string $ref, ?int $esperado): void
    {
        $m = new \ReflectionMethod(AppMobileLogin::class, 'resolveInstalledAppId');

        $this->assertSame($esperado, $m->invoke(null, ['menus' => 4, 'procesos' => 9], $ref));
    }

    /** @return iterable<string, array{string, ?int}> */
    public static function referenciasDeApp(): iterable
    {
        yield 'id numérico tal cual (aunque no esté en el catálogo)' => ['77', 77];
        yield 'nombre lógico del catálogo' => ['procesos', 9];
        yield 'nombre desconocido' => ['no_existe', null];
        yield 'vacío' => ['', null];
        yield 'número negativo no es un id' => ['-3', null];
    }
}
