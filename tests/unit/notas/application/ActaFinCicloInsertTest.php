<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PDO;
use PHPUnit\Framework\TestCase;
use src\notas\application\ActaFinCicloInsert;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;

final class ActaFinCicloInsertTest extends TestCase
{
    public function testBuildUsaSiglaDeLaDlQueInserta(): void
    {
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchColumn')->willReturn('2024-06-15');
        $pdo->method('prepare')->willReturn($stmt);

        $svc = new ActaFinCicloInsert($pdo, 'publicv.e_notas', 'dlb');
        $d = $svc->build(42, ActaFinCicloInsert::ID_FIN_BIENIO);

        $this->assertSame('dlb', $d['acta']);
        $this->assertSame('fin bienio', $d['detalle']);
        $this->assertSame('2024-06-15', $d['f_acta']);
        $this->assertSame(9999, $d['id_asignatura']);
        $this->assertSame(NotaSituacion::SUPERADA, $d['id_situacion']);
        $this->assertSame(TipoActa::FORMATO_ACTA, $d['tipo_acta']);
    }

    public function testBuildCuadrienioDetalle(): void
    {
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchColumn')->willReturn(false);
        $pdo->method('prepare')->willReturn($stmt);

        $svc = new ActaFinCicloInsert($pdo, 'publicv.e_notas', 'dlmE');
        $d = $svc->build(7, ActaFinCicloInsert::ID_FIN_CUADRIENIO);

        $this->assertSame('dlmE', $d['acta']);
        $this->assertSame('fin cuadrienio', $d['detalle']);
        $this->assertSame(9998, $d['id_asignatura']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $d['f_acta']);
    }

    /**
     * PDO que apunta cada SQL preparado con sus parámetros; el SELECT de la última fecha devuelve `$fActa`.
     *
     * @param \ArrayObject<int, array{string, array<string, mixed>}> $log
     */
    private function pdoQueApunta(\ArrayObject $log, string|false $fActa): PDO
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturnCallback(function (string $sql) use ($log, $fActa): \PDOStatement {
            $stmt = $this->createMock(\PDOStatement::class);
            $stmt->method('execute')->willReturnCallback(static function (array $params) use ($log, $sql): bool {
                $log[] = [$sql, $params];

                return true;
            });
            $stmt->method('fetchColumn')->willReturn($fActa);

            return $stmt;
        });

        return $pdo;
    }

    public function testInsertaEnLaDlLocalConLaUltimaFechaDeActaDelBienio(): void
    {
        $log = new \ArrayObject();
        $svc = new ActaFinCicloInsert($this->pdoQueApunta($log, '2024-06-15 00:00:00'), 'publicv.e_notas', 'dlb');

        $svc->insertIntoDl(42, ActaFinCicloInsert::ID_FIN_BIENIO);

        $this->assertCount(2, $log);
        [$select, $paramsSelect] = $log[0];
        $this->assertStringContainsString('FROM publicv.e_notas a', $select);
        $this->assertStringContainsString('AND a.id_nivel < 2000', $select, 'bienio: solo notas de filosofía');
        $this->assertSame(['id_nom' => 42], $paramsSelect);
        [$insert, $params] = $log[1];
        $this->assertStringStartsWith('INSERT INTO e_notas_dl', trim($insert));
        $this->assertSame([
            'id_nom' => 42,
            'id_nivel' => 9999,
            'id_asignatura' => 9999,
            'f_acta' => '2024-06-15',
            'id_situacion' => NotaSituacion::SUPERADA,
            'acta' => 'dlb',
            'detalle' => 'fin bienio',
            'tipo_acta' => TipoActa::FORMATO_ACTA,
        ], $params);
    }

    public function testCuadrienioMiraTodasLasNotas(): void
    {
        $log = new \ArrayObject();
        (new ActaFinCicloInsert($this->pdoQueApunta($log, false), 'publicf.e_notas', 'dlbf'))->build(42, ActaFinCicloInsert::ID_FIN_CUADRIENIO);

        $this->assertStringNotContainsString('id_nivel < 2000', $log[0][0]);
        $this->assertStringContainsString('FROM publicf.e_notas a', $log[0][0]);
    }

    public function testSoloEscribeEnLaTablaDeLaDl(): void
    {
        $svc = new ActaFinCicloInsert($this->createMock(PDO::class), 'publicv.e_notas', 'dlb');

        $this->expectExceptionMessage('solo se escribe en e_notas_dl local');
        $svc->insertIntoDl(42, ActaFinCicloInsert::ID_FIN_BIENIO, 'publicv.e_notas');
    }

    public function testTablaDeExpedienteNoValida(): void
    {
        $this->expectExceptionMessage('tabla de expediente no válida');
        new ActaFinCicloInsert($this->createMock(PDO::class), 'e_notas; DROP TABLE x');
    }

    public function testSoloMarcasDeFinDeCiclo(): void
    {
        $this->expectExceptionMessage('debe ser 9998 o 9999');
        (new ActaFinCicloInsert($this->createMock(PDO::class), 'publicv.e_notas', 'dlb'))->build(42, 1101);
    }

    public function testSinSiglaDeDl(): void
    {
        $auth = $_SESSION['session_auth'] ?? [];
        $_SESSION['session_auth'] = array_merge($auth, ['esquema' => '']);
        try {
            $this->expectExceptionMessage('sin sigla de DL para id_nom=42 asig=9999');
            (new ActaFinCicloInsert($this->createMock(PDO::class), 'publicv.e_notas', '  '))->build(42, ActaFinCicloInsert::ID_FIN_BIENIO);
        } finally {
            $_SESSION['session_auth'] = $auth;
        }
    }
}
