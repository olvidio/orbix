<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\FormCargosPersonasEnActividadData;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Payload JSON de {@see FormCargosPersonasEnActividadData::build} (dossier 1302, vista por persona).
 */
final class FormCargosPersonasEnActividadDataTest extends TestCase
{
    private mixed $previousContainer = null;
    private bool $hadSessionSfsv = false;
    private mixed $previousSessionSfsv = null;
    private bool $hadSessionEsquema = false;
    private mixed $previousSessionEsquema = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $this->hadSessionSfsv = array_key_exists('sfsv', $_SESSION['session_auth']);
        $this->previousSessionSfsv = $this->hadSessionSfsv ? $_SESSION['session_auth']['sfsv'] : null;
        $_SESSION['session_auth']['sfsv'] = 1;

        $this->hadSessionEsquema = array_key_exists('esquema', $_SESSION['session_auth']);
        $this->previousSessionEsquema = $this->hadSessionEsquema ? $_SESSION['session_auth']['esquema'] : null;
        $_SESSION['session_auth']['esquema'] = 'Rg-uv';
    }

    protected function tearDown(): void
    {
        if ($this->hadSessionSfsv) {
            $_SESSION['session_auth']['sfsv'] = $this->previousSessionSfsv;
        } else {
            unset($_SESSION['session_auth']['sfsv']);
        }
        if ($this->hadSessionEsquema) {
            $_SESSION['session_auth']['esquema'] = $this->previousSessionEsquema;
        } else {
            unset($_SESSION['session_auth']['esquema']);
        }
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_actividadesToRows_mapea_filas(): void
    {
        $act = new class {
            public function getId_activ(): int
            {
                return 3;
            }
            public function getNom_activ(): string
            {
                return 'Act A';
            }
        };

        $rows = FormCargosPersonasEnActividadData::actividadesToRows([$act]);

        $this->assertSame([['id_activ' => 3, 'nom_activ' => 'Act A']], $rows);
    }

    public function test_build_sin_item_llama_lista_actividades(): void
    {
        $capture = new class {
            /** @var array<string, mixed>|null */
            public ?array $where = null;
            /** @var array<string, mixed>|null */
            public ?array $op = null;
        };

        $GLOBALS['container'] = $this->containerMap([
            ActividadRepositoryInterface::class => new class($capture) {
                public function __construct(private readonly object $capture) {}

                public function getActividades(array $aWhere, array $aOperators = []): array
                {
                    $this->capture->where = $aWhere;
                    $this->capture->op = $aOperators;

                    return [];
                }
            },
            CargoRepositoryInterface::class => new class {
                public function getArrayCargos(): array
                {
                    return [1 => 'C1'];
                }
            },
        ]);

        $data = FormCargosPersonasEnActividadData::build([
            'mod' => 'nuevo',
            'id_pau' => 9,
            'permiso' => 0,
            'id_dossier' => 1302,
            'id_tipo' => 0,
            'que_dl' => '',
        ]);

        $this->assertSame([], $data['aActividades']);
        $this->assertNotNull($capture->where);
        $this->assertSame('^1', $capture->where['id_tipo_activ']);
        $this->assertSame('~', $capture->op['id_tipo_activ']);
        $this->assertStringContainsString('select', $data['desplegable_cargos_html']);
    }

    public function test_build_con_item_carga_actividad_y_hash_con_id_activ(): void
    {
        $cargoRow = new class {
            public function getId_activ(): int
            {
                return 100;
            }
            public function getId_cargo(): int
            {
                return 2;
            }
            public function isPuede_agd(): bool
            {
                return false;
            }
            public function getObserv(): ?string
            {
                return 'obs';
            }
        };
        $actividad = new class {
            public function getNom_activ(): string
            {
                return 'Nombre act';
            }
        };

        $GLOBALS['container'] = $this->containerMap([
            ActividadCargoRepositoryInterface::class => new class($cargoRow) {
                public function __construct(private readonly object $cargoRow) {}

                public function findById(int $id_item): ?object
                {
                    return $id_item === 55 ? $this->cargoRow : null;
                }
            },
            ActividadRepositoryInterface::class => new class($actividad) {
                public function __construct(private readonly object $actividad) {}

                public function findById(int $id): ?object
                {
                    return $id === 100 ? $this->actividad : null;
                }
            },
            CargoRepositoryInterface::class => new class {
                public function getArrayCargos(): array
                {
                    return [2 => 'Cargo dos'];
                }
            },
        ]);

        $data = FormCargosPersonasEnActividadData::build([
            'sel' => ['55'],
            'mod' => 'editar',
            'id_pau' => 999,
            'permiso' => 1,
            'id_dossier' => 1302,
        ]);

        $this->assertSame(100, $data['id_activ_real']);
        $this->assertSame('Nombre act', $data['nom_activ']);
        $this->assertSame([], $data['aActividades']);
        $this->assertSame('obs', $data['observ']);
        $this->assertSame(
            [
                'campos_form' => 'id_cargo!observ',
                'campos_no' => 'puede_agd',
                'campos_hidden' => [
                    'id_item' => 55,
                    'id_nom' => 999,
                    'mod' => 'editar',
                    'id_activ' => 100,
                ],
            ],
            $data['hash_form_config']
        );
    }

    public function test_build_cumple_contrato_de_claves_modo_lista(): void
    {
        $GLOBALS['container'] = $this->containerMap([
            ActividadRepositoryInterface::class => new class {
                public function getActividades(array $_w, array $_o = []): array
                {
                    return [];
                }
            },
            CargoRepositoryInterface::class => new class {
                public function getArrayCargos(): array
                {
                    return [];
                }
            },
        ]);

        $data = FormCargosPersonasEnActividadData::build([
            'mod' => 'nuevo',
            'id_pau' => 1,
            'permiso' => 2,
            'id_dossier' => 1302,
        ]);

        $expectedKeys = [
            'obj',
            'Qpermiso',
            'id_activ_real',
            'nom_activ',
            'aActividades',
            'desplegable_cargos_html',
            'hash_form_config',
            'chk',
            'observ',
            'Qmod',
            'url_cargo_nuevo',
            'url_cargo_editar',
        ];
        $this->assertSame($expectedKeys, array_keys($data));

        $this->assertSame('actividadcargos\\model\\entity\\ActividadCargo', $data['obj']);
        $this->assertSame(2, $data['Qpermiso']);
        $base = rtrim(ConfigGlobal::getWeb(), '/');
        $this->assertSame($base . '/src/actividadcargos/cargo_nuevo', $data['url_cargo_nuevo']);
        $this->assertSame($base . '/src/actividadcargos/cargo_editar', $data['url_cargo_editar']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $key): object
            {
                if (!isset($this->services[$key])) {
                    throw new \RuntimeException('Servicio no registrado en test: ' . $key);
                }

                return $this->services[$key];
            }
        };
    }
}
