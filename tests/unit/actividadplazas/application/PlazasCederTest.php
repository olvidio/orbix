<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasCeder;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionId;

final class PlazasCederTest extends TestCase
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

    public function test_faltan_parametros(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $this->createMock(DelegacionRepositoryInterface::class),
            ActividadPlazasDlRepositoryInterface::class => $this->createMock(ActividadPlazasDlRepositoryInterface::class),
        ]);

        $msg = PlazasCeder::execute(['id_activ' => 0, 'region_dl' => 'R-x']);
        $this->assertNotSame('', $msg);
    }

    public function test_sin_registro_plazas(): void
    {
        $deleg = new Delegacion();
        $deleg->setIdDlVo(new DelegacionId(5));
        $deleg->setDl('dl');

        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->with(['dl' => 'dl'])->willReturn([$deleg]);

        $plazasRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasRepo->method('getActividadesPlazas')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasRepo,
        ]);

        $msg = PlazasCeder::execute([
            'id_activ' => 99,
            'region_dl' => 'H-dlx',
            'num_plazas' => 2,
        ]);
        $this->assertNotSame('', $msg);
    }

    public function test_num_plazas_cero_quita_cedida_y_guarda(): void
    {
        $deleg = new Delegacion();
        $deleg->setIdDlVo(new DelegacionId(5));
        $deleg->setDl('dl');

        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->willReturn([$deleg]);

        $oPlazas = $this->createMock(ActividadPlazas::class);
        $oPlazas->method('getArrayCedidas')->willReturn(['dlx' => 3]);

        $plazasRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasRepo->method('getActividadesPlazas')->willReturn([$oPlazas]);
        $oPlazas->expects($this->once())->method('setCedidas')->with([]);
        $plazasRepo->expects($this->once())->method('Guardar')->with($oPlazas)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasRepo,
        ]);

        $msg = PlazasCeder::execute([
            'id_activ' => 1,
            'region_dl' => 'X-dlx',
            'num_plazas' => 0,
        ]);
        $this->assertSame('', $msg);
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
