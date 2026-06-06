<?php

declare(strict_types=1);

namespace Tests\unit\actividadcargos\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\application\FormCargosPersonasEnActividadData;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Payload JSON de {@see FormCargosPersonasEnActividadData::build} (dossier 1302, vista por persona).
 */
final class FormCargosPersonasEnActividadDataTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $savedSession = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedSession = $_SESSION ?? [];
        if (!isset($_SESSION['session_auth']) || !is_array($_SESSION['session_auth'])) {
            $_SESSION['session_auth'] = [];
        }
        $_SESSION['session_auth']['sfsv'] = 1;
        $_SESSION['session_auth']['esquema'] = 'Rg-uv';
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->savedSession;
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

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('getActividades')
            ->willReturnCallback(function (array $aWhere, array $aOperators = []) use ($capture): array {
                $capture->where = $aWhere;
                $capture->op = $aOperators;
                return [];
            });

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([1 => 'C1']);

        $builder = new FormCargosPersonasEnActividadData(
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $actividadRepo,
            $cargoRepo,
        );

        $data = $builder->build([
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
        $cargoRow = $this->createMock(ActividadCargo::class);
        $cargoRow->method('getId_activ')->willReturn(100);
        $cargoRow->method('getId_cargo')->willReturn(2);
        $cargoRow->method('isPuede_agd')->willReturn(false);
        $cargoRow->method('getObserv')->willReturn('obs');

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('Nombre act');

        $cargoRepoMock = $this->createMock(ActividadCargoRepositoryInterface::class);
        $cargoRepoMock->method('findById')->willReturnCallback(
            fn (int $id_item) => $id_item === 55 ? $cargoRow : null
        );

        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('findById')->willReturnCallback(
            fn (int $id) => $id === 100 ? $actividad : null
        );

        $cargosRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargosRepo->method('getArrayCargos')->willReturn([2 => 'Cargo dos']);

        $builder = new FormCargosPersonasEnActividadData(
            $cargoRepoMock,
            $actividadRepo,
            $cargosRepo,
        );

        $data = $builder->build([
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
        $actividadRepo = $this->createMock(ActividadRepositoryInterface::class);
        $actividadRepo->method('getActividades')->willReturn([]);

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->willReturn([]);

        $builder = new FormCargosPersonasEnActividadData(
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $actividadRepo,
            $cargoRepo,
        );

        $data = $builder->build([
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
}
