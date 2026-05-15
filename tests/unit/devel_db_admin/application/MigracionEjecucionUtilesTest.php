<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application;

use PDO;
use PDOException;
use PDOStatement;
use src\devel_db_admin\application\MigracionEjecucionUtiles;
use Tests\myTest;

final class MigracionEjecucionUtilesTest extends myTest
{
    public function test_es_esquema_resto(): void
    {
        $this->assertTrue(MigracionEjecucionUtiles::esEsquemaResto('restov'));
        $this->assertTrue(MigracionEjecucionUtiles::esEsquemaResto('RESTOF'));
        $this->assertTrue(MigracionEjecucionUtiles::esEsquemaResto('resto'));
        $this->assertFalse(MigracionEjecucionUtiles::esEsquemaResto('H-dlbv'));
    }

    public function test_es_error_esquema_inexistente_3f000(): void
    {
        $e = new PDOException('schema "x" does not exist');
        $e->errorInfo = ['3F000', 7, 'ERROR:  schema "x" does not exist'];

        $this->assertTrue(MigracionEjecucionUtiles::esErrorEsquemaInexistente($e));
    }

    public function test_es_error_esquema_inexistente_rechaza_42p01(): void
    {
        $e = new PDOException('relation does not exist');
        $e->errorInfo = ['42P01', 7, 'ERROR:  relation "foo" does not exist'];

        $this->assertFalse(MigracionEjecucionUtiles::esErrorEsquemaInexistente($e));
    }

    public function test_es_omitible_42p01_si_el_esquema_no_esta_en_pg_namespace(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->with(['Acme-crAcmev'])->willReturn(true);
        $stmt->expects($this->once())->method('fetchColumn')->willReturn(false);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())->method('prepare')->willReturn($stmt);

        $e = new PDOException('relation "Acme-crAcmev.aux_menus" does not exist');
        $e->errorInfo = ['42P01', 7, 'ERROR:  relation "Acme-crAcmev.aux_menus" does not exist'];

        $this->assertTrue(MigracionEjecucionUtiles::esOmitiblePorAusenciaDeEsquema($e, $pdo, 'Acme-crAcmev'));
    }

    public function test_no_omitible_42p01_si_el_esquema_existe(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchColumn')->willReturn('1');

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $e = new PDOException('relation "Acme-crAcmev.aux_menus" does not exist');
        $e->errorInfo = ['42P01', 7, 'ERROR:  relation "Acme-crAcmev.aux_menus" does not exist'];

        $this->assertFalse(MigracionEjecucionUtiles::esOmitiblePorAusenciaDeEsquema($e, $pdo, 'Acme-crAcmev'));
    }

    public function test_es_omitible_3f000_sin_consultar_pg(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->never())->method('prepare');

        $e = new PDOException('schema "x" does not exist');
        $e->errorInfo = ['3F000', 7, 'ERROR:  schema "x" does not exist'];

        $this->assertTrue(MigracionEjecucionUtiles::esOmitiblePorAusenciaDeEsquema($e, $pdo, 'x'));
    }
}
