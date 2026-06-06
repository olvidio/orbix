<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ListaActivTabla;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;
use src\ubis\domain\contracts\CasaRepositoryInterface;
final class ListaActivTablaApplicationTest extends TestCase
{
    private mixed $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'idioma' => 'es_ES'];
    }

    protected function tearDown(): void
    {
        if ($this->previousSession === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->previousSession;
        }
        parent::tearDown();
    }

    public function test_sin_actividades_devuelve_estructura_sin_html(): void
    {
        $actRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actRepo->method('getActividades')->willReturn([]);

        $out = (new ListaActivTabla(
            $actRepo,
            $this->emptyCasaRepo(),
            $this->emptyTarifaRepo(),
            $this->emptyCentroEncRepo(),
            $this->emptyCargoRepo(),
        ))->execute(
            [
                'que' => 'list_activ',
                'periodo' => 'actual',
                'id_tipo_activ' => '......',
            ],
            [
                'mi_sfsv' => 1,
                'perm_vcsd' => false,
                'perm_des' => false,
                'perm_sg' => false,
                'perm_admin' => false,
                'is_dmz' => true,
            ]
        );

        $this->assertArrayHasKey('a_valores', $out);
        $this->assertSame([], $out['a_valores']);
        $this->assertArrayNotHasKey('html_tabla', $out);
    }

    public function test_actividad_sin_casa_no_falla(): void
    {
        $activ = new class {
            public function getId_activ(): int
            {
                return 1;
            }

            public function getId_tipo_activ(): string
            {
                return 'sv......';
            }

            public function getId_ubi(): int
            {
                return 99999;
            }

            public function getF_ini(): DateTimeLocal
            {
                return DateTimeLocal::createFromLocal('01/05/2026');
            }

            public function getF_fin(): DateTimeLocal
            {
                return DateTimeLocal::createFromLocal('02/05/2026');
            }

            public function getH_ini(): ?\DateTimeInterface
            {
                return null;
            }

            public function getH_fin(): ?\DateTimeInterface
            {
                return null;
            }

            public function getTarifa(): ?int
            {
                return 1;
            }

            public function getObserv(): string
            {
                return '';
            }
        };

        $actRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actRepo->method('getActividades')->willReturn([$activ]);

        $casaRepo = $this->createMock(CasaRepositoryInterface::class);
        $casaRepo->method('findById')->with(99999)->willReturn(null);

        $tipoTarifa = $this->createMock(TipoTarifa::class);
        $tipoTarifa->method('getLetra')->willReturn('A');
        $tarifaRepo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $tarifaRepo->method('findById')->with(1)->willReturn($tipoTarifa);

        $centroEncRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $centroEncRepo->method('getCentrosEncargadosActividad')->willReturn([]);

        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('getActividadSacds')->willReturn([]);

        $out = (new ListaActivTabla($actRepo, $casaRepo, $tarifaRepo, $centroEncRepo, $cargoRepo))->execute(
            [
                'que' => 'list_activ_compl',
                'periodo' => 'actual',
                'empiezamin' => '01/05/2026',
                'empiezamax' => '31/05/2026',
                'id_tipo_activ' => '......',
            ],
            [
                'mi_sfsv' => 1,
                'perm_vcsd' => false,
                'perm_des' => false,
                'perm_sg' => false,
                'perm_admin' => true,
                'is_dmz' => true,
            ]
        );

        $this->assertCount(1, $out['a_valores']);
        $this->assertSame('', $out['a_valores'][1][1]);
        $this->assertSame('', $out['a_valores'][1][10]);
    }

    public function test_fechas_personalizadas_con_periodo_distinto_de_otro(): void
    {
        $actRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actRepo->expects($this->once())
            ->method('getActividades')
            ->with(
                $this->callback(static function (array $where): bool {
                    return $where['f_ini'] === "'2026-05-01','2026-05-31'";
                }),
                $this->callback(static function (array $operador): bool {
                    return ($operador['f_ini'] ?? '') === 'BETWEEN';
                }),
            )
            ->willReturn([]);

        (new ListaActivTabla(
            $actRepo,
            $this->emptyCasaRepo(),
            $this->emptyTarifaRepo(),
            $this->emptyCentroEncRepo(),
            $this->emptyCargoRepo(),
        ))->execute(
            [
                'que' => 'list_activ',
                'periodo' => 'tot_any',
                'empiezamin' => '01/05/2026',
                'empiezamax' => '31/05/2026',
                'id_tipo_activ' => '......',
            ],
            [
                'mi_sfsv' => 1,
                'perm_vcsd' => false,
                'perm_des' => false,
                'perm_sg' => false,
                'perm_admin' => false,
                'is_dmz' => true,
            ]
        );
    }

    private function emptyCasaRepo(): CasaRepositoryInterface
    {
        $repo = $this->createMock(CasaRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        return $repo;
    }

    private function emptyTarifaRepo(): TipoTarifaRepositoryInterface
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        return $repo;
    }

    private function emptyCentroEncRepo(): CentroEncargadoRepositoryInterface
    {
        $repo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $repo->method('getCentrosEncargadosActividad')->willReturn([]);

        return $repo;
    }

    private function emptyCargoRepo(): ActividadCargoRepositoryInterface
    {
        $repo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $repo->method('getActividadSacds')->willReturn([]);

        return $repo;
    }
}
