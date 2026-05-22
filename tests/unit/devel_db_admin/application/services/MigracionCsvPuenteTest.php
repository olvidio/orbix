<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application\services;

use src\devel_db_admin\application\services\MigracionCsvPuente;
use Tests\myTest;

final class MigracionCsvPuenteTest extends myTest
{
    private MigracionCsvPuente $puente;

    public function setUp(): void
    {
        parent::setUp();
        $this->puente = new MigracionCsvPuente();
    }

    public function test_parse_export_e_import(): void
    {
        $sql = <<<'SQL'
-- @orbix_export_csv: log/db/locales.csv
-- @orbix_export_query_begin
SELECT id_locale FROM public.x_locales;
-- @orbix_export_query_end

CREATE TABLE publicv.x_locale_tmp (id_locale varchar(12));

-- @orbix_import_csv: log/db/locales.csv
-- @orbix_import_into: publicv.x_locale_tmp(id_locale)
-- @orbix_import_here

ALTER TABLE global.personas RENAME COLUMN lengua TO idioma_preferido;
SQL;

        $plan = $this->puente->parse($sql);

        $this->assertSame('SELECT id_locale FROM public.x_locales;', $plan['export_query']);
        $this->assertSame('log/db/locales.csv', $plan['export_path']);
        $this->assertSame('log/db/locales.csv', $plan['import_path']);
        $this->assertSame('publicv.x_locale_tmp', $plan['import_table']);
        $this->assertSame(['id_locale'], $plan['import_columns']);
        $this->assertSame('CREATE TABLE publicv.x_locale_tmp (id_locale varchar(12));', $plan['sql_before_import']);
        $this->assertSame(
            'ALTER TABLE global.personas RENAME COLUMN lengua TO idioma_preferido;',
            $plan['sql_after_import'],
        );
    }

    public function test_resolve_relative_path(): void
    {
        $path = $this->puente->resolveRelativePath('log/db/locales.csv');

        $this->assertStringEndsWith('/log/db/locales.csv', $path);
    }
}
