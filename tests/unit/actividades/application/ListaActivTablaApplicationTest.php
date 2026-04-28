<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ListaActivTabla;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

final class ListaActivTablaApplicationTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
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
}
