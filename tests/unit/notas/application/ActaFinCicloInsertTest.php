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
}
