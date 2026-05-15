<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application\services;

use src\devel_db_admin\application\services\MigracionSqlAnalyzer;
use src\devel_db_admin\domain\value_objects\MigracionTipo;
use Tests\myTest;

final class MigracionSqlAnalyzerTest extends myTest
{
    private MigracionSqlAnalyzer $analyzer;

    public function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new MigracionSqlAnalyzer();
    }

    public function test_detecta_estructura(): void
    {
        $tipo = $this->analyzer->tipoDe('ALTER TABLE *.du_camasa_dl ADD COLUMN larga boolean;');

        $this->assertSame(MigracionTipo::ESTRUCTURA, $tipo->value());
    }

    public function test_detecta_datos(): void
    {
        $tipo = $this->analyzer->tipoDe("UPDATE aux SET valor = 'x';");

        $this->assertSame(MigracionTipo::DATOS, $tipo->value());
    }

    public function test_mixto_es_estructura(): void
    {
        $tipo = $this->analyzer->tipoDe("ALTER TABLE aux ADD COLUMN valor text;\nUPDATE aux SET valor = 'x';");

        $this->assertSame(MigracionTipo::ESTRUCTURA, $tipo->value());
    }

    public function test_detecta_comodin(): void
    {
        $this->assertTrue($this->analyzer->usaComodin('ALTER TABLE *.du_camasa_dl ADD COLUMN larga boolean;'));
        $this->assertFalse($this->analyzer->usaComodin("SELECT '*' AS literal;"));
    }

    public function test_expande_comodin_con_schema_con_guion(): void
    {
        $sql = $this->analyzer->expandirComodin(
            'ALTER TABLE *.du_camasa_dl ALTER COLUMN larga DEFAULT TRUE;',
            'Usca-crUscav',
        );

        $this->assertSame('ALTER TABLE "Usca-crUscav".du_camasa_dl ALTER COLUMN larga DEFAULT TRUE;', $sql);
    }
}
