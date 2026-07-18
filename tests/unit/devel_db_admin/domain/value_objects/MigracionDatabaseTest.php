<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\domain\value_objects;

use InvalidArgumentException;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use Tests\myTest;

final class MigracionDatabaseTest extends myTest
{
    public function test_valores_validos(): void
    {
        foreach (MigracionDatabase::validValues() as $value) {
            $this->assertSame($value, (new MigracionDatabase($value))->value());
        }
        $this->assertContains(MigracionDatabase::SF, MigracionDatabase::validValues());
    }

    public function test_valor_invalido_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MigracionDatabase('otra');
    }

    public function test_serie_sf_solo_incluye_sf(): void
    {
        $this->assertSame([MigracionDatabase::SF], MigracionDatabase::databasesDeSerie(MigracionDatabase::SERIE_SF));
        $this->assertSame([MigracionDatabase::SF], MigracionDatabase::archivosDeSerie(MigracionDatabase::SERIE_SF));
        $this->assertTrue(MigracionDatabase::perteneceASerie(MigracionDatabase::SF, MigracionDatabase::SERIE_SF));
        $this->assertFalse(MigracionDatabase::perteneceASerie(MigracionDatabase::COMUN, MigracionDatabase::SERIE_SF));
    }

    public function test_serie_sv_excluye_sf(): void
    {
        $dbs = MigracionDatabase::databasesDeSerie(MigracionDatabase::SERIE_SV);
        $this->assertContains(MigracionDatabase::COMUN, $dbs);
        $this->assertContains(MigracionDatabase::SV, $dbs);
        $this->assertContains(MigracionDatabase::SV_E, $dbs);
        $this->assertNotContains(MigracionDatabase::SF, $dbs);
        $this->assertFalse(MigracionDatabase::perteneceASerie(MigracionDatabase::SF, MigracionDatabase::SERIE_SV));
    }
}
