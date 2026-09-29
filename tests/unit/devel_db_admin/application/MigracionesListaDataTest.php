<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\application;

use src\devel_db_admin\application\MigracionesListaData;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\domain\entity\MigracionAplicada;
use src\shared\security\HashB;
use Tests\myTest;

final class MigracionesListaDataTest extends myTest
{
    private string $tmpDir = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->tmpDir = sys_get_temp_dir() . '/orbix_mig_lista_' . uniqid('', true);
        mkdir($this->tmpDir);
        file_put_contents(
            $this->tmpDir . '/202601010000_demo_comun__comun.sql',
            "UPDATE public.x SET a = 1;\n",
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

    public function test_filas_emiten_capsulas_hashb_por_id(): void
    {
        $data = (new MigracionesListaData($this->repoVacio(), $this->tmpDir))->build();
        $this->assertNotEmpty($data['a_valores']);
        $row = $data['a_valores'][0];
        $id = (string) $row['sel'];
        $this->assertNotSame('', $id);

        $ctxEjecutar = HashB::open((string) $row['ctx_ejecutar'], 'migraciones_ejecutar');
        $this->assertSame($id, $ctxEjecutar['id']);

        $ctxQuitar = HashB::open((string) $row['ctx_quitar'], 'migraciones_quitar_registro');
        $this->assertSame($id, $ctxQuitar['id']);
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
