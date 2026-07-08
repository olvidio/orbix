<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\application\CambioUsuarioObjetoPrefFasesData;

final class CambioUsuarioObjetoPrefFasesDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $configBackup = [];

    protected function setUp(): void
    {
        $this->configBackup = $_SESSION['config'] ?? [];
        $_SESSION['config'] = [
            'a_apps' => [],
            'app_installed' => [],
        ];
    }

    protected function tearDown(): void
    {
        if ($this->configBackup !== []) {
            $_SESSION['config'] = $this->configBackup;
        }
    }

    /**
     * @param array{procesosInstalados?: bool, fases?: array<int|string, string>} $options
     */
    private function createUseCase(array $options = []): CambioUsuarioObjetoPrefFasesData
    {
        if (!empty($options['procesosInstalados'])) {
            $_SESSION['config']['a_apps']['procesos'] = 99003;
            $_SESSION['config']['app_installed'] = [99003];
        }

        $tipoDeActividadRepository = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipoDeActividadRepository->method('getTiposDeProcesos')->willReturn(['tipo-a']);

        $actividadFaseRepository = $this->createMock(\src\procesos\domain\contracts\ActividadFaseRepositoryInterface::class);
        $actividadFaseRepository->method('getArrayActividadFases')
            ->willReturn($options['fases'] ?? [3 => 'fase proceso']);

        return new CambioUsuarioObjetoPrefFasesData(
            $tipoDeActividadRepository,
            $actividadFaseRepository,
        );
    }

    public function test_sin_objeto_devuelve_error(): void
    {
        $result = $this->createUseCase()->execute([]);

        $this->assertNotSame('', $result['error']);
        $this->assertSame([], $result['aFases']);
    }

    public function test_sin_procesos_devuelve_estados_para_todos_los_objetos(): void
    {
        $expected = array_diff_key(
            StatusId::getArrayStatus(),
            [StatusId::ALL => true]
        );

        $objetos = [
            'Actividad',
            'Asistente',
            'ActividadCargoSacd',
            'ActividadCargoNoSacd',
            'CentroEncargado',
        ];

        foreach ($objetos as $objeto) {
            $result = $this->createUseCase()->execute([
                'objeto' => $objeto,
                'id_tipo_activ' => '1.....',
                'dl_propia' => 'true',
            ]);

            $this->assertSame('', $result['error'], "objeto=$objeto");
            $this->assertFalse($result['fases_usa_procesos'], "objeto=$objeto");
            $this->assertSame($expected, $result['aFases'], "objeto=$objeto");
            foreach ($expected as $id => $label) {
                $this->assertIsInt($id, "objeto=$objeto");
                $this->assertIsString($label, "objeto=$objeto");
                $this->assertNotSame((string) $id, $label, "objeto=$objeto");
            }
        }
    }

    public function test_con_procesos_devuelve_fases_del_repositorio(): void
    {
        $fases = [7 => 'aprobada', 9 => 'publicada'];
        $result = $this->createUseCase([
            'procesosInstalados' => true,
            'fases' => $fases,
        ])->execute([
            'objeto' => 'Actividad',
            'id_tipo_activ' => '1.....',
            'dl_propia' => 'false',
        ]);

        $this->assertSame('', $result['error']);
        $this->assertTrue($result['fases_usa_procesos']);
        $this->assertSame($fases, $result['aFases']);
    }
}
