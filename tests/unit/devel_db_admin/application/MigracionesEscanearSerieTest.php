<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application;

use src\devel_db_admin\application\MigracionesEscanear;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use Tests\myTest;

final class MigracionesEscanearSerieTest extends myTest
{
    private string $tmpDir = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->tmpDir = sys_get_temp_dir() . '/orbix_mig_serie_' . uniqid('', true);
        mkdir($this->tmpDir);
        file_put_contents(
            $this->tmpDir . '/202601010000_demo_comun__comun.sql',
            "UPDATE public.x SET a = 1;\n",
        );
        file_put_contents(
            $this->tmpDir . '/202601010001_demo_sve__sv-e.sql',
            "ALTER TABLE *.t ADD COLUMN IF NOT EXISTS c int;\n",
        );
        file_put_contents(
            $this->tmpDir . '/202601010002_demo_sf__sf.sql',
            "ALTER TABLE *.t ADD COLUMN IF NOT EXISTS c int;\n",
        );
    }

    public function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/*.sql') ?: [] as $file) {
            unlink($file);
        }
        if (is_dir($this->tmpDir)) {
            rmdir($this->tmpDir);
        }
        parent::tearDown();
    }

    public function test_serie_sv_omite_sf(): void
    {
        $scan = (new MigracionesEscanear($this->repoVacio(), $this->tmpDir, null, MigracionDatabase::SERIE_SV))
            ->escanear();

        $ids = array_column($scan['migraciones'], 'id');
        $this->assertContains('202601010000_demo_comun', $ids);
        $this->assertContains('202601010001_demo_sve', $ids);
        $this->assertNotContains('202601010002_demo_sf', $ids);
        $this->assertSame(MigracionDatabase::SERIE_SV, $scan['serie']);
    }

    public function test_serie_sf_solo_sf(): void
    {
        $scan = (new MigracionesEscanear($this->repoVacio(), $this->tmpDir, null, MigracionDatabase::SERIE_SF))
            ->escanear();

        $ids = array_column($scan['migraciones'], 'id');
        $this->assertSame(['202601010002_demo_sf'], $ids);
        $this->assertSame(MigracionDatabase::SERIE_SF, $scan['serie']);
        $this->assertSame(
            [MigracionDatabase::SF],
            array_column($scan['migraciones'][0]['aplicaciones'], 'database'),
        );
    }

    public function test_destinos_sf_sin_replica(): void
    {
        $this->assertSame(
            [MigracionDatabase::SF],
            MigracionesEscanear::destinosPara(MigracionDatabase::SF, 'estructura'),
        );
        $this->assertSame(
            [MigracionDatabase::SF],
            MigracionesEscanear::destinosPara(MigracionDatabase::SF, 'datos'),
        );
    }

    private function repoVacio(): MigracionAplicadaRepositoryInterface
    {
        return new class implements MigracionAplicadaRepositoryInterface {
            public function ensureTabla(): void
            {
            }

            public function aplicadas(): array
            {
                return [];
            }

            public function findByKey(string $prefijo, string $descripcion, string $database): ?MigracionAplicada
            {
                return null;
            }

            public function existe(string $prefijo, string $descripcion, string $database): bool
            {
                return false;
            }

            public function registrar(MigracionAplicada $migracion): bool
            {
                return true;
            }

            public function Eliminar(MigracionAplicada $migracion): bool
            {
                return false;
            }
        };
    }
}
