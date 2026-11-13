<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\infrastructure;

use Closure;
use PDO;
use PHPUnit\Framework\TestCase;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\infrastructure\security\EnlaceRecuperacionPassword;
use src\usuarios\infrastructure\security\RecuperacionPassword;

/**
 * Recuperar la contraseña con un enlace (duda 3 de la sección 6b del plan):
 * - pedirlo no cambia la contraseña (antes se cambiaba en el acto: cualquiera dejaba sin
 *   acceso a otro usuario) y devuelve el correo enmascarado (antes, entero);
 * - el enlace va firmado, caduca y sirve una sola vez (al cambiar la contraseña);
 * - la contraseña nueva pasa la política y se guarda sin obligar a cambiarla otra vez.
 */
final class RecuperacionPasswordTest extends TestCase
{
    private const ESQUEMA = 'H-dlbv';
    private PDO $pdo;
    /** @var list<array{0: string, 1: string, 2: string}> */
    private array $correos = [];
    private string $hashInicial;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE aux_usuarios (id_usuario INTEGER PRIMARY KEY, usuario TEXT, email TEXT, password TEXT, cambio_password BOOLEAN)');
        $this->hashInicial = (new PasswordHasher())->encode('Antigua-Clave.2026');
        $st = $this->pdo->prepare('INSERT INTO aux_usuarios VALUES (?, ?, ?, ?, ?)');
        $st->execute([7, 'pepe', 'pepe.garcia@example.org', $this->hashInicial, 1]);
        $st->execute([8, 'sinmail', '', $this->hashInicial, 0]);
    }

    private function svc(): RecuperacionPassword
    {
        $pdo = fn (string $esquema): ?PDO => $esquema === self::ESQUEMA ? $this->pdo : null;
        $mail = function (string $to, string $asunto, string $mensaje): bool {
            $this->correos[] = [$to, $asunto, $mensaje];
            return true;
        };

        return new RecuperacionPassword(Closure::fromCallable($pdo), Closure::fromCallable($mail), 'https://orbix.example.org/app/');
    }

    private function password(int $id): string
    {
        return (string) $this->pdo->query("SELECT password FROM aux_usuarios WHERE id_usuario = $id")->fetchColumn();
    }

    private function enlaceDelCorreo(): string
    {
        $this->assertCount(1, $this->correos);
        $this->assertSame(1, preg_match('#https://orbix\.example\.org/app/frontend/usuarios/controller/restablecer_password\.php\?tk=(\S+)#', $this->correos[0][2], $m));

        return urldecode($m[1]);
    }

    public function test_pedirlo_envia_un_enlace_y_no_cambia_la_contrasena(): void
    {
        $r = $this->svc()->solicitar('pepe', self::ESQUEMA);

        $this->assertSame(['success' => true, 'error_txt' => '', 'email' => 'pep********@ex*****.org'], $r);
        $this->assertSame('pepe.garcia@example.org', $this->correos[0][0]);
        $this->assertStringNotContainsString('Antigua', $this->correos[0][2]);
        $this->assertSame($this->hashInicial, $this->password(7), 'la contraseña actual sigue valiendo');
        $this->assertNotNull(EnlaceRecuperacionPassword::abrir($this->enlaceDelCorreo()));
    }

    public function test_errores_al_pedirlo_sin_enviar_correo(): void
    {
        $svc = $this->svc();
        $this->assertSame(['success' => false, 'error_txt' => 'Esquema no válido', 'email' => ''], $svc->solicitar('pepe', 'H-otra'));
        $this->assertSame('No se encontró ningún usuario con ese nombre', $svc->solicitar('nadie', self::ESQUEMA)['error_txt']);
        $this->assertSame('No hay email asociado a este usuario', $svc->solicitar('sinmail', self::ESQUEMA)['error_txt']);
        $this->assertSame([], $this->correos);
    }

    public function test_con_el_enlace_se_elige_la_contrasena_nueva_y_el_enlace_ya_no_vale(): void
    {
        $svc = $this->svc();
        $svc->solicitar('pepe', self::ESQUEMA);
        $tk = $this->enlaceDelCorreo();

        $this->assertSame(['success' => true, 'error_txt' => '', 'username' => 'pepe'], $svc->comprobar($tk));
        $this->assertSame(['success' => true, 'error_txt' => ''], $svc->restablecer($tk, 'Nueva-Clave.Larga!9', 'Nueva-Clave.Larga!9'));

        $hasher = new PasswordHasher();
        $hash = $this->password(7);
        $this->assertSame($hash, $hasher->encode('Nueva-Clave.Larga!9', $hash), 'guarda la contraseña nueva');
        $this->assertSame(0, (int) $this->pdo->query('SELECT cambio_password FROM aux_usuarios WHERE id_usuario = 7')->fetchColumn());

        $this->assertFalse($svc->comprobar($tk)['success'], 'un solo uso');
        $this->assertSame(RecuperacionPassword::enlaceNoValido(), $svc->restablecer($tk, 'Otra-Clave.Larga!9', 'Otra-Clave.Larga!9')['error_txt']);
    }

    public function test_la_contrasena_nueva_tiene_que_coincidir_y_cumplir_la_politica(): void
    {
        $svc = $this->svc();
        $svc->solicitar('pepe', self::ESQUEMA);
        $tk = $this->enlaceDelCorreo();

        $this->assertSame('Las dos contraseñas no coinciden', $svc->restablecer($tk, 'Nueva-Clave.Larga!9', 'Otra')['error_txt']);
        $this->assertFalse($svc->restablecer($tk, 'corta', 'corta')['success']);
        $this->assertSame($this->hashInicial, $this->password(7));
        $this->assertTrue($svc->comprobar($tk)['success'], 'tras un error el enlace sigue valiendo');
    }

    public function test_enlace_manipulado_caducado_o_de_otro_esquema_no_vale(): void
    {
        $svc = $this->svc();
        $buena = EnlaceRecuperacionPassword::emitir(7, self::ESQUEMA, $this->hashInicial);
        $this->assertTrue($svc->comprobar($buena)['success']);

        [$payload, $firma] = explode('.', $buena);
        $otroUsuario = rtrim(strtr(base64_encode((string) json_encode(['e' => self::ESQUEMA, 'p' => 'x', 'u' => 8, 'x' => time() + 99])), '+/', '-_'), '=');
        $this->assertFalse($svc->comprobar("$otroUsuario.$firma")['success'], 'firma de otro payload');
        $this->assertFalse($svc->comprobar($payload)['success']);
        $this->assertFalse($svc->comprobar('')['success']);

        $caducada = EnlaceRecuperacionPassword::emitir(7, self::ESQUEMA, $this->hashInicial, time() - EnlaceRecuperacionPassword::TTL - 1);
        $this->assertFalse($svc->comprobar($caducada)['success']);
        $this->assertFalse($svc->comprobar(EnlaceRecuperacionPassword::emitir(7, 'H-otra', $this->hashInicial))['success']);
        $this->assertFalse($svc->comprobar(EnlaceRecuperacionPassword::emitir(99, self::ESQUEMA, $this->hashInicial))['success']);
    }
}
