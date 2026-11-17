<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\infrastructure;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\usuarios\infrastructure\security\Recuperacion2fa;

/**
 * Enlace de recuperación del 2FA: solo un enlace válido (token sha256 sin caducar, en el esquema del
 * usuario) quita el 2FA, y comprobarlo no cambia nada. La parte con la BD real la cubre
 * `e2e/specs/authenticated/recuperar-2fa.spec.ts`.
 */
final class Recuperacion2faTest extends TestCase
{
    /** @var list<array{string, array<string, mixed>}> */
    private array $ejecutadas = [];

    private function pdo(bool $tokenValido): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturnCallback(function (string $sql) use ($tokenValido): PDOStatement {
            $st = $this->createMock(PDOStatement::class);
            $st->method('execute')->willReturnCallback(function (array $params) use ($sql): bool {
                $this->ejecutadas[] = [$sql, $params];

                return true;
            });
            $st->method('fetchColumn')->willReturn($tokenValido ? 7 : false);

            return $st;
        });

        return $pdo;
    }

    /** @param list<string> $esquemas */
    private function recuperacion(?PDO $pdo, array &$esquemas = []): Recuperacion2fa
    {
        return new Recuperacion2fa(function (string $esquema) use ($pdo, &$esquemas): ?PDO {
            $esquemas[] = $esquema;

            return $pdo;
        });
    }

    public function test_enlace_valido_se_comprueba_en_el_esquema_del_usuario_sin_cambiar_nada(): void
    {
        $esquemas = [];
        $this->assertTrue($this->recuperacion($this->pdo(true), $esquemas)->comprobar('H-dlbv', 7, 'tok'));

        $this->assertSame(['H-dlbv'], $esquemas);
        $this->assertCount(1, $this->ejecutadas);
        [$sql, $params] = $this->ejecutadas[0];
        $this->assertStringStartsWith('SELECT', $sql);
        $this->assertSame([':id' => 7, ':hash' => hash('sha256', 'tok')], $params);
    }

    public function test_desactivar_con_enlace_valido_quita_el_2fa_del_usuario(): void
    {
        $this->assertTrue($this->recuperacion($this->pdo(true))->desactivar('H-dlbv', 7, 'tok'));

        $this->assertCount(2, $this->ejecutadas);
        [$sql, $params] = $this->ejecutadas[1];
        $this->assertStringStartsWith('UPDATE aux_usuarios SET has_2fa = false', $sql);
        $this->assertStringContainsString('token_recuperacion_2fa = NULL', $sql);
        $this->assertSame([':id' => 7], $params);
    }

    public function test_enlace_no_valido_no_desactiva(): void
    {
        $this->assertFalse($this->recuperacion($this->pdo(false))->comprobar('H-dlbv', 7, 'tok'));
        $this->assertFalse($this->recuperacion($this->pdo(false))->desactivar('H-dlbv', 7, 'tok'));

        foreach ($this->ejecutadas as [$sql]) {
            $this->assertStringStartsNotWith('UPDATE', $sql);
        }
    }

    public function test_sin_datos_o_sin_conexion_no_vale_y_no_consulta(): void
    {
        $esquemas = [];
        $r = $this->recuperacion($this->pdo(true), $esquemas);
        $this->assertFalse($r->comprobar('', 7, 'tok'));
        $this->assertFalse($r->comprobar('H-dlbv', 0, 'tok'));
        $this->assertFalse($r->desactivar('H-dlbv', 7, ''));
        $this->assertSame([], $esquemas);

        $this->assertFalse($this->recuperacion(null)->desactivar('H-noexiste', 7, 'tok'));
        $this->assertSame([], $this->ejecutadas);
    }
}
