<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ListaActivTabla;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

final class ListaActivTablaApplicationTest extends TestCase
{
    private mixed $previousContainer;
    private mixed $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'idioma' => 'es_ES'];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
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

        $prefRepo = $this->createMock(PreferenciaRepositoryInterface::class);

        $GLOBALS['container'] = new class($actRepo, $prefRepo) {
            public function __construct(
                private readonly object $actRepo,
                private readonly object $prefRepo
            ) {}

            public function get(string $id): object
            {
                return match ($id) {
                    ActividadRepositoryInterface::class => $this->actRepo,
                    CasaRepositoryInterface::class => $this->emptyCasaRepo(),
                    TipoTarifaRepositoryInterface::class => $this->emptyTarifaRepo(),
                    CentroEncargadoRepositoryInterface::class => $this->emptyCentroEncRepo(),
                    ActividadCargoRepositoryInterface::class => $this->emptyCargoRepo(),
                    PreferenciaRepositoryInterface::class => $this->prefRepo,
                    default => throw new \RuntimeException($id),
                };
            }

            private function emptyCasaRepo(): object
            {
                return new class {
                    public function findById(int $id): object
                    {
                        return new class {
                            public function getNombre_ubi(): string
                            {
                                return '';
                            }

                            public function isSv(): bool
                            {
                                return false;
                            }

                            public function isSf(): bool
                            {
                                return false;
                            }
                        };
                    }
                };
            }

            private function emptyTarifaRepo(): object
            {
                return new class {
                    public function findById(?int $id): ?object
                    {
                        return null;
                    }
                };
            }

            private function emptyCentroEncRepo(): object
            {
                return new class {
                    public function getCentrosEncargadosActividad(int $id): array
                    {
                        return [];
                    }
                };
            }

            private function emptyCargoRepo(): object
            {
                return new class {
                    public function getActividadSacds(int $id): array
                    {
                        return [];
                    }
                };
            }
        };

        $out = (new ListaActivTabla())->execute(
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
                return null;
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

        $tarifaRepo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $tarifaRepo->method('findById')->willReturn(new class {
            public function getLetra(): string
            {
                return 'A';
            }
        });

        $centroEncRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $centroEncRepo->method('getCentrosEncargadosActividad')->willReturn([]);

        $cargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepo->method('getActividadSacds')->willReturn([]);

        $GLOBALS['container'] = new class($actRepo, $casaRepo, $tarifaRepo, $centroEncRepo, $cargoRepo) {
            public function __construct(
                private readonly object $actRepo,
                private readonly object $casaRepo,
                private readonly object $tarifaRepo,
                private readonly object $centroEncRepo,
                private readonly object $cargoRepo,
            ) {}

            public function get(string $id): object
            {
                return match ($id) {
                    ActividadRepositoryInterface::class => $this->actRepo,
                    CasaRepositoryInterface::class => $this->casaRepo,
                    TipoTarifaRepositoryInterface::class => $this->tarifaRepo,
                    CentroEncargadoRepositoryInterface::class => $this->centroEncRepo,
                    ActividadCargoRepositoryInterface::class => $this->cargoRepo,
                    default => throw new \RuntimeException($id),
                };
            }
        };

        $out = (new ListaActivTabla())->execute(
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

        $prefRepo = $this->createMock(PreferenciaRepositoryInterface::class);

        $GLOBALS['container'] = new class($actRepo, $prefRepo) {
            public function __construct(
                private readonly object $actRepo,
                private readonly object $prefRepo,
            ) {}

            public function get(string $id): object
            {
                return match ($id) {
                    ActividadRepositoryInterface::class => $this->actRepo,
                    CasaRepositoryInterface::class => $this->emptyCasaRepo(),
                    TipoTarifaRepositoryInterface::class => $this->emptyTarifaRepo(),
                    CentroEncargadoRepositoryInterface::class => $this->emptyCentroEncRepo(),
                    ActividadCargoRepositoryInterface::class => $this->emptyCargoRepo(),
                    PreferenciaRepositoryInterface::class => $this->prefRepo,
                    default => throw new \RuntimeException($id),
                };
            }

            private function emptyCasaRepo(): object
            {
                return new class {
                    public function findById(int $id): ?object
                    {
                        return null;
                    }
                };
            }

            private function emptyTarifaRepo(): object
            {
                return new class {
                    public function findById(?int $id): ?object
                    {
                        return null;
                    }
                };
            }

            private function emptyCentroEncRepo(): object
            {
                return new class {
                    public function getCentrosEncargadosActividad(int $id): array
                    {
                        return [];
                    }
                };
            }

            private function emptyCargoRepo(): object
            {
                return new class {
                    public function getActividadSacds(int $id): array
                    {
                        return [];
                    }
                };
            }
        };

        (new ListaActivTabla())->execute(
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
}
