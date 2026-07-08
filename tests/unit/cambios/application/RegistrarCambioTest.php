<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\cambios\application\RegistrarCambio;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

final class RegistrarCambioTest extends TestCase
{
    /** @var list<Cambio> */
    private array $guardadosDl = [];

    /** @var list<Cambio> */
    private array $guardadosPublic = [];

    /** @var array<string, mixed> */
    private array $configBackup = [];

    protected function setUp(): void
    {
        $_SESSION['session_auth'] = [
            'id_usuario' => 443,
            'sfsv' => 1,
        ];
        $this->configBackup = $_SESSION['config'] ?? [];
        $_SESSION['config'] = [
            'a_apps' => [],
            'app_installed' => [],
        ];
        $this->guardadosDl = [];
        $this->guardadosPublic = [];
    }

    protected function tearDown(): void
    {
        if ($this->configBackup !== []) {
            $_SESSION['config'] = $this->configBackup;
        }
    }

    /**
     * @param array{
     *     cambiosInstalados?: bool,
     *     procesosInstalados?: bool,
     * } $options
     */
    private function createUseCase(array $options = []): RegistrarCambio
    {
        $cambiosInstalados = $options['cambiosInstalados'] ?? false;
        $procesosInstalados = $options['procesosInstalados'] ?? false;

        if ($cambiosInstalados) {
            $_SESSION['config']['a_apps']['cambios'] = 99002;
            $_SESSION['config']['app_installed'] = [99002];
            if ($procesosInstalados) {
                $_SESSION['config']['a_apps']['procesos'] = 99003;
                $_SESSION['config']['app_installed'][] = 99003;
            }
        } else {
            $_SESSION['config']['a_apps'] = [];
            $_SESSION['config']['app_installed'] = [];
        }

        $nextId = 900001;
        $cambioRepository = $this->createMock(CambioRepositoryInterface::class);
        $cambioRepository->method('getNewId')->willReturnCallback(function () use (&$nextId): int {
            return $nextId++;
        });
        $cambioRepository->expects($cambiosInstalados ? $this->never() : $this->any())
            ->method('Guardar')
            ->willReturnCallback(function (Cambio $cambio) use ($cambiosInstalados): bool {
                if (!$cambiosInstalados) {
                    $this->guardadosPublic[] = $cambio;
                }

                return true;
            });

        $cambioDlRepository = $this->createMock(CambioDlRepositoryInterface::class);
        $cambioDlRepository->method('getNewId')->willReturnCallback(function () use (&$nextId): int {
            return $nextId++;
        });
        $cambioDlRepository->expects($cambiosInstalados ? $this->any() : $this->never())
            ->method('Guardar')
            ->willReturnCallback(function (Cambio $cambio) use ($cambiosInstalados): bool {
                if ($cambiosInstalados) {
                    $this->guardadosDl[] = $cambio;
                }

                return true;
            });

        $actividadRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $procesoRepository = $this->createMock(ActividadProcesoTareaRepositoryInterface::class);
        if ($procesosInstalados) {
            $procesoRepository->method('getFasesCompletadas')->willReturn([2, 4]);
        }

        return new RegistrarCambio(
            $actividadRepository,
            $cambioDlRepository,
            $cambioRepository,
            $procesoRepository,
        );
    }

    /** @return array<string, mixed> */
    private function datosBaseActividad(): array
    {
        return [
            'id_tipo_activ' => 112401,
            'status' => 2,
            'dl_org' => 'dlb',
            'nom_activ' => 'Curso test',
        ];
    }

    public function test_update_serializa_datetime_local_en_valor_old_y_new(): void
    {
        $useCase = $this->createUseCase();
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'Actividad',
            'UPDATE',
            300123817,
            $datosBase + ['f_ini' => new DateTimeLocal('2026-07-07')],
            $datosBase + ['f_ini' => new DateTimeLocal('2026-01-15')],
        );

        $this->assertCount(1, $this->guardadosPublic);
        $this->assertSame('f_ini', $this->guardadosPublic[0]->getPropiedad());
        $this->assertSame('2026-01-15', $this->guardadosPublic[0]->getValor_old());
        $this->assertSame('2026-07-07', $this->guardadosPublic[0]->getValor_new());
    }

    public function test_update_serializa_time_local_en_valor_old_y_new(): void
    {
        $useCase = $this->createUseCase();
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'Actividad',
            'UPDATE',
            300123817,
            $datosBase + ['h_ini' => TimeLocal::fromString('10:30:00')],
            $datosBase + ['h_ini' => TimeLocal::fromString('09:00:00')],
        );

        $this->assertCount(1, $this->guardadosPublic);
        $this->assertSame('h_ini', $this->guardadosPublic[0]->getPropiedad());
        $this->assertSame('09:00:00', $this->guardadosPublic[0]->getValor_old());
        $this->assertSame('10:30:00', $this->guardadosPublic[0]->getValor_new());
    }

    public function test_update_no_registra_cambio_si_datetime_local_es_igual(): void
    {
        $useCase = $this->createUseCase();
        $datos = $this->datosBaseActividad() + ['f_ini' => new DateTimeLocal('2026-07-07')];

        $useCase->execute(
            'Actividad',
            'UPDATE',
            300123817,
            $datos,
            $datos + ['f_ini' => new DateTimeLocal('2026-07-07')],
        );

        $this->assertSame([], $this->guardadosPublic);
        $this->assertSame([], $this->guardadosDl);
    }

    public function test_update_no_deja_valores_null_con_datetime_local(): void
    {
        $useCase = $this->createUseCase();
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'Actividad',
            'UPDATE',
            300123817,
            $datosBase + ['f_ini' => new DateTimeLocal('2026-01-01')],
            $datosBase + ['f_ini' => new DateTimeLocal('2026-07-07')],
        );

        $this->assertCount(1, $this->guardadosPublic);
        $this->assertNotNull($this->guardadosPublic[0]->getValor_old());
        $this->assertNotNull($this->guardadosPublic[0]->getValor_new());
    }

    public function test_con_cambios_instalados_persiste_en_repositorio_dl(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => true]);
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'ActividadDl',
            'UPDATE',
            300123817,
            array_merge($datosBase, ['nom_activ' => 'Nuevo nombre']),
            array_merge($datosBase, ['nom_activ' => 'Nombre anterior']),
        );

        $this->assertSame([], $this->guardadosPublic);
        $this->assertCount(1, $this->guardadosDl);
        $this->assertSame('ActividadDl', $this->guardadosDl[0]->getObjeto());
        $this->assertSame('nom_activ', $this->guardadosDl[0]->getPropiedad());
    }

    public function test_sin_cambios_instalados_persiste_en_repositorio_public(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => false]);
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'Actividad',
            'UPDATE',
            300123817,
            array_merge($datosBase, ['nom_activ' => 'Nuevo nombre']),
            array_merge($datosBase, ['nom_activ' => 'Nombre anterior']),
        );

        $this->assertCount(1, $this->guardadosPublic);
        $this->assertSame([], $this->guardadosDl);
        $this->assertSame('Actividad', $this->guardadosPublic[0]->getObjeto());
    }

    public function test_update_registra_un_cambio_por_cada_propiedad_modificada(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => true]);
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'ActividadDl',
            'UPDATE',
            300123817,
            array_merge($datosBase, [
                'f_ini' => new DateTimeLocal('2026-07-07'),
                'f_fin' => new DateTimeLocal('2026-08-15'),
                'h_ini' => TimeLocal::fromString('10:00:00'),
                'nom_activ' => 'Curso modificado',
            ]),
            array_merge($datosBase, [
                'f_ini' => new DateTimeLocal('2026-01-01'),
                'f_fin' => new DateTimeLocal('2026-06-01'),
                'h_ini' => TimeLocal::fromString('09:00:00'),
                'nom_activ' => 'Curso test',
            ]),
        );

        $this->assertCount(4, $this->guardadosDl);

        $propiedades = array_map(
            static fn (Cambio $cambio): string => (string) $cambio->getPropiedad(),
            $this->guardadosDl,
        );
        sort($propiedades);
        $this->assertSame(['f_fin', 'f_ini', 'h_ini', 'nom_activ'], $propiedades);

        $porPropiedad = [];
        foreach ($this->guardadosDl as $cambio) {
            $porPropiedad[(string) $cambio->getPropiedad()] = $cambio;
        }

        $this->assertSame('2026-01-01', $porPropiedad['f_ini']->getValor_old());
        $this->assertSame('2026-07-07', $porPropiedad['f_ini']->getValor_new());
        $this->assertSame('2026-06-01', $porPropiedad['f_fin']->getValor_old());
        $this->assertSame('2026-08-15', $porPropiedad['f_fin']->getValor_new());
        $this->assertSame('09:00:00', $porPropiedad['h_ini']->getValor_old());
        $this->assertSame('10:00:00', $porPropiedad['h_ini']->getValor_new());
    }

    public function test_insert_asistente_registra_id_nom_en_repositorio_dl(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => true]);

        $useCase->execute(
            'Asistente',
            'INSERT',
            300123817,
            [
                'id_nom' => 445566,
                'id_tipo_activ' => 112401,
                'status' => 2,
                'dl_org' => 'dlb',
            ],
            [],
        );

        $this->assertCount(1, $this->guardadosDl);
        $this->assertSame('Asistente', $this->guardadosDl[0]->getObjeto());
        $this->assertSame('id_nom', $this->guardadosDl[0]->getPropiedad());
        $this->assertSame('445566', $this->guardadosDl[0]->getValor_new());
    }

    public function test_insert_actividad_cargo_sacd_registra_id_nom_en_repositorio_dl(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => true]);

        $useCase->execute(
            'ActividadCargoSacd',
            'INSERT',
            300123817,
            [
                'id_nom' => 778899,
                'id_tipo_activ' => 112401,
                'status' => 2,
                'dl_org' => 'dlb',
            ],
            [],
        );

        $this->assertCount(1, $this->guardadosDl);
        $this->assertSame('ActividadCargoSacd', $this->guardadosDl[0]->getObjeto());
        $this->assertSame('id_nom', $this->guardadosDl[0]->getPropiedad());
        $this->assertSame('778899', $this->guardadosDl[0]->getValor_new());
    }

    public function test_insert_actividad_cargo_no_sacd_registra_id_nom_en_repositorio_dl(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => true]);

        $useCase->execute(
            'ActividadCargoNoSacd',
            'INSERT',
            300123817,
            [
                'id_nom' => 889900,
                'id_tipo_activ' => 112401,
                'status' => 2,
                'dl_org' => 'dlb',
            ],
            [],
        );

        $this->assertCount(1, $this->guardadosDl);
        $this->assertSame('ActividadCargoNoSacd', $this->guardadosDl[0]->getObjeto());
        $this->assertSame('id_nom', $this->guardadosDl[0]->getPropiedad());
        $this->assertSame('889900', $this->guardadosDl[0]->getValor_new());
    }

    public function test_insert_centro_encargado_registra_id_ubi_en_repositorio_public(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => false]);

        $useCase->execute(
            'CentroEncargado',
            'INSERT',
            300123817,
            [
                'id_ubi' => 7788,
                'id_tipo_activ' => 112401,
                'status' => 2,
                'dl_org' => 'dlb',
            ],
            [],
        );

        $this->assertCount(1, $this->guardadosPublic);
        $this->assertSame('CentroEncargado', $this->guardadosPublic[0]->getObjeto());
        $this->assertSame('id_ubi', $this->guardadosPublic[0]->getPropiedad());
        $this->assertSame('7788', $this->guardadosPublic[0]->getValor_new());
    }

    public function test_actividad_ex_conserva_objeto_en_update(): void
    {
        $useCase = $this->createUseCase(['cambiosInstalados' => true]);
        $datosBase = $this->datosBaseActividad();

        $useCase->execute(
            'ActividadEx',
            'UPDATE',
            300123817,
            $datosBase + ['f_ini' => new DateTimeLocal('2026-12-01')],
            $datosBase + ['f_ini' => new DateTimeLocal('2026-11-01')],
        );

        $this->assertCount(1, $this->guardadosDl);
        $this->assertSame('ActividadEx', $this->guardadosDl[0]->getObjeto());
        $this->assertSame('f_ini', $this->guardadosDl[0]->getPropiedad());
    }
}
