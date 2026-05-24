<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application;

use PDO;
use PDOException;
use PDOStatement;
use src\devel_db_admin\application\MigracionEjecucionUtiles;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
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

    public function test_es_esquema_region_stgr_comun(): void
    {
        $this->assertTrue(MigracionEjecucionUtiles::esEsquemaRegionStgrComun('H-H'));
        $this->assertTrue(MigracionEjecucionUtiles::esEsquemaRegionStgrComun('M-M'));
        $this->assertFalse(MigracionEjecucionUtiles::esEsquemaRegionStgrComun('H-dlbv'));
        $this->assertFalse(MigracionEjecucionUtiles::esEsquemaRegionStgrComun('H-crH'));
    }

    public function test_es_replica_select(): void
    {
        $this->assertTrue(MigracionEjecucionUtiles::esReplicaSelect(MigracionDatabase::COMUN_SELECT));
        $this->assertFalse(MigracionEjecucionUtiles::esReplicaSelect(MigracionDatabase::COMUN));
    }

    public function test_tiene_sql_ejecutable(): void
    {
        $this->assertFalse(MigracionEjecucionUtiles::tieneSqlEjecutable("-- solo comentario\n"));
        $this->assertTrue(MigracionEjecucionUtiles::tieneSqlEjecutable("SELECT 1;\n-- fin\n"));
    }

    public function test_split_sql_statements_respeta_dollar_quotes(): void
    {
        $sql = <<<'SQL'
SELECT 1;

DO $$
BEGIN
    PERFORM 1;
END $$;

SELECT migracion_aviso('hola');
SQL;

        $parts = MigracionEjecucionUtiles::splitSqlStatements($sql);

        $this->assertCount(3, $parts);
        $this->assertStringContainsString('SELECT 1', $parts[0]);
        $this->assertStringContainsString('DO $$', $parts[1]);
        $this->assertStringContainsString('END $$', $parts[1]);
        $this->assertStringContainsString("migracion_aviso('hola')", $parts[2]);
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
