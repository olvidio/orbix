<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\usuarios\application\LoginProcesar;

/**
 * Validaciones de `LoginProcesar` (login web) que se resuelven ANTES de abrir ninguna conexión.
 *
 * El resto del caso de uso crea sus conexiones con `new ConfigDB` / `new DBConnection` (no admite
 * mocks) y se prueba en `Tests\integration\usuarios\application\LoginProcesarIntegracionTest`.
 */
final class LoginProcesarTest extends TestCase
{
    /** @var array<string, mixed>|null */
    private ?array $sesionPrevia = null;
    private string|false $privatePrevio = false;

    protected function setUp(): void
    {
        $this->sesionPrevia = $_SESSION ?? null;
        $this->privatePrevio = getenv('PRIVATE');
        putenv('PRIVATE');
        $_SESSION = ['session_auth' => ['id_usuario' => 7, 'esquema' => 'H-dlbv', 'username' => 'previo']];
    }

    protected function tearDown(): void
    {
        putenv($this->privatePrevio === false ? 'PRIVATE' : 'PRIVATE=' . $this->privatePrevio);
        if ($this->sesionPrevia === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->sesionPrevia;
        }
    }

    /**
     * Sin esquema no hay conexión posible: error 1 (usuario o contraseña no válidos), el mismo que
     * con credenciales incorrectas, sin dar pistas.
     *
     * @param array<string, string> $input
     */
    #[DataProvider('sinEsquema')]
    public function test_sin_esquema_utilizable_se_rechaza_con_el_error_generico(array $input, string $esquemaWeb): void
    {
        $antes = $_SESSION;

        $r = (new LoginProcesar())->execute($input, $esquemaWeb, 'sv');

        $this->assertSame(['ok' => false, 'error' => 1], $r);
        $this->assertSame($antes, $_SESSION, 'el caso de uso no toca la sesión');
    }

    /** @return iterable<string, array{array<string, string>, string}> */
    public static function sinEsquema(): iterable
    {
        yield 'petición vacía' => [[], ''];
        yield 'credenciales sin esquema' => [['username' => 'alguien', 'password' => 'secreto'], ''];
        yield 'esquema vacío' => [['username' => 'alguien', 'password' => 'secreto', 'esquema' => ''], ''];
        yield 'esquema «0» cuenta como vacío' => [['username' => 'alguien', 'password' => 'secreto', 'esquema' => '0'], ''];
        yield 'esquema «f» fuera de sf queda vacío' => [['username' => 'alguien', 'password' => 'secreto', 'esquema' => 'f'], ''];
        yield 'esquema del entorno «f» fuera de sf' => [['username' => 'alguien', 'password' => 'secreto'], 'f'];
    }

    #[DataProvider('referenciasDeApp')]
    public function test_resolucion_del_id_de_app_instalada(string $ref, ?int $esperado): void
    {
        $m = new \ReflectionMethod(LoginProcesar::class, 'resolveInstalledAppId');

        $this->assertSame($esperado, $m->invoke(new LoginProcesar(), ['menus' => 4, 'procesos' => 9], $ref));
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
