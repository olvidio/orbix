<?php

declare(strict_types=1);

namespace Tests\unit\cambios\infrastructure\cli;

use PHPUnit\Framework\TestCase;
use src\cambios\infrastructure\cli\AvisosGenerarTablaLanzadorExec;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\LoginCliTicketRepositoryInterface;

/**
 * El generador de avisos se lanza en segundo plano con `exec`. Antes llevaba la contraseña del
 * usuario (sin escapar) en la línea de órdenes: se veía con `ps` y una contraseña con `;` ejecutaba
 * órdenes en el servidor. Ahora inicia sesión con un ticket de un solo uso que va en el entorno.
 */
final class AvisosGenerarTablaLanzadorExecTest extends TestCase
{
    private const TICKET = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';

    /** @var array<string, mixed>|null */
    private ?array $sesionPrevia = null;

    protected function setUp(): void
    {
        $this->sesionPrevia = $_SESSION ?? null;
        $_SESSION = ['session_auth' => [
            'username' => "o'brien; touch /tmp/pwned",
            'password' => 'secreto$(id)',
            'esquema' => 'H-dlbv',
        ]];
    }

    protected function tearDown(): void
    {
        if ($this->sesionPrevia === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->sesionPrevia;
        }
    }

    private function comando(): string
    {
        $tickets = $this->createMock(LoginCliTicketRepositoryInterface::class);
        $tickets->expects($this->once())
            ->method('emitir')
            ->with("o'brien; touch /tmp/pwned", 'H-dlbv')
            ->willReturn(self::TICKET);

        return (new AvisosGenerarTablaLanzadorExec($tickets))->comando();
    }

    public function test_el_ticket_va_en_el_entorno_y_no_la_contrasena(): void
    {
        $comando = $this->comando();

        $this->assertStringStartsWith("ORBIX_LOGIN_CLI_TICKET='" . self::TICKET . "' nohup ", $comando);
        $this->assertStringNotContainsString('secreto', $comando);
    }

    public function test_todos_los_argumentos_van_escapados(): void
    {
        $comando = $this->comando();

        $programa = ConfigGlobal::$directorio . '/src/cambios/infrastructure/cli/avisos_generar_tabla.php';
        $this->assertStringContainsString(
            escapeshellarg($programa) . ' ' . escapeshellarg("o'brien; touch /tmp/pwned") . " '-' ",
            $comando
        );
        $this->assertStringContainsString(escapeshellarg('H-dlbv'), $comando);
        $this->assertStringEndsWith(' < /dev/null &', $comando);
    }
}
