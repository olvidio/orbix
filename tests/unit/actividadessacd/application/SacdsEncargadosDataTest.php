<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\SacdsEncargadosData;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\PermAccion;

final class SacdsEncargadosDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_id_activ_no_positivo_devuelve_vacio_sin_tocar_container(): void
    {
        $GLOBALS['container'] = new class {
            public function get(string $id): never
            {
                throw new \RuntimeException('container no deberia usarse: ' . $id);
            }
        };

        $out = (new SacdsEncargadosData(
            $this->createMock(CargoRepositoryInterface::class),
            $this->createMock(ActividadCargoRepositoryInterface::class)
        ))->execute(['id_activ' => 0]);
        $this->assertSame([
            'id_activ' => 0,
            'permite_ver' => false,
            'permite_modificar' => false,
            'sacds' => [],
        ], $out);
    }

    public function test_sin_permiso_ver_no_consulta_cargos(): void
    {
        $_SESSION['config']['a_apps']['procesos'] = 42;
        $_SESSION['config']['app_installed'] = [42];
        $oPerm = $this->createMock(PermisosActividades::class);
        $oPerm->method('setActividad')->willReturnSelf();
        $oPerm->method('getPermisoActual')->willReturnCallback(static function (string $sAfecta): PermAccion {
            if ($sAfecta === 'sacd') {
                return new PermAccion(0);
            }
            return new PermAccion(15);
        });
        $_SESSION['oPermActividades'] = $oPerm;

        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->expects($this->never())->method('getArrayCargos');
        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->expects($this->never())->method('getActividadCargos');

        $out = (new \src\actividadessacd\application\SacdsEncargadosData($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 77,
            'id_tipo_activ' => '160000',
            'dl_org' => 'dl',
        ]);
        $this->assertSame(77, $out['id_activ']);
        $this->assertFalse($out['permite_ver']);
        $this->assertFalse($out['permite_modificar']);
        $this->assertSame([], $out['sacds']);
    }

    public function test_con_permiso_ver_y_lista_cargos_vacia_sacds_vacio(): void {
        $cargoRepo = $this->createMock(CargoRepositoryInterface::class);
        $cargoRepo->method('getArrayCargos')->with('sacd')->willReturn([88 => 'sacd1']);

        $activCargoRepo = $this->createMock(ActividadCargoRepositoryInterface::class);
        $activCargoRepo->method('getActividadCargos')->willReturn([]);

        $out = (new \src\actividadessacd\application\SacdsEncargadosData($cargoRepo, $activCargoRepo))->execute([
            'id_activ' => 77,
            'id_tipo_activ' => '160000',
            'dl_org' => 'dl',
        ]);
        $this->assertTrue($out['permite_ver']);
        $this->assertTrue($out['permite_modificar']);
        $this->assertSame([], $out['sacds']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
