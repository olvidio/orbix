<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasCeder;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
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
            ActividadPlazasRepositoryInterface::class => $this->createMock(ActividadPlazasRepositoryInterface::class),
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

        $plazasDlRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasDlRepo->method('getActividadesPlazas')->willReturn([]);
        $calRepo = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $calRepo->method('getActividadesPlazas')->willReturn([]);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasDlRepo,
            ActividadPlazasRepositoryInterface::class => $calRepo,
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

        $plazasDlRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasDlRepo->method('getActividadesPlazas')->willReturn([$oPlazas]);
        $calRepo = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $oPlazas->expects($this->once())->method('setCedidas')->with([]);
        $plazasDlRepo->expects($this->once())->method('Guardar')->with($oPlazas)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasDlRepo,
            ActividadPlazasRepositoryInterface::class => $calRepo,
        ]);

        $msg = PlazasCeder::execute([
            'id_activ' => 1,
            'region_dl' => 'X-dlx',
            'num_plazas' => 0,
        ]);
        $this->assertSame('', $msg);
    }

    public function test_no_tiene_plazas_para_ceder(): void
    {
        $deleg = new Delegacion();
        $deleg->setIdDlVo(new DelegacionId(5));
        $deleg->setDl('dl');

        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->willReturn([$deleg]);

        $oPlazas = $this->createMock(ActividadPlazas::class);
        $oPlazas->method('getArrayCedidas')->willReturn([]);

        $plazasDlRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasDlRepo->method('getActividadesPlazas')->willReturn([$oPlazas]);

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->expects($this->once())->method('setId_activ')->with(1);
        $resumenSvc->method('getPlazasCalendario')->willReturn(0);

        $calRepo = $this->createMock(ActividadPlazasRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasDlRepo,
            ActividadPlazasRepositoryInterface::class => $calRepo,
            ResumenPlazasService::class => $resumenSvc,
        ]);

        $msg = PlazasCeder::execute([
            'id_activ' => 1,
            'region_dl' => 'X-dlx',
            'num_plazas' => 2,
        ]);
        $this->assertNotSame('', $msg);
    }

    public function test_no_tiene_plazas_suficientes_para_ceder(): void
    {
        $deleg = new Delegacion();
        $deleg->setIdDlVo(new DelegacionId(5));
        $deleg->setDl('dl');

        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->willReturn([$deleg]);

        $oPlazas = $this->createMock(ActividadPlazas::class);
        $oPlazas->method('getArrayCedidas')->willReturn(['dlx' => 1]);

        $plazasDlRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasDlRepo->method('getActividadesPlazas')->willReturn([$oPlazas]);
        $plazasDlRepo->expects($this->never())->method('Guardar');

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->method('getPlazasCalendario')->willReturn(3);

        $calRepo = $this->createMock(ActividadPlazasRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasDlRepo,
            ActividadPlazasRepositoryInterface::class => $calRepo,
            ResumenPlazasService::class => $resumenSvc,
        ]);

        $msg = PlazasCeder::execute([
            'id_activ' => 1,
            'region_dl' => 'X-dly',
            'num_plazas' => 3,
        ]);
        $this->assertNotSame('', $msg);
    }

    public function test_cede_plazas_cuando_hay_disponibles(): void
    {
        $deleg = new Delegacion();
        $deleg->setIdDlVo(new DelegacionId(5));
        $deleg->setDl('dl');

        $delegRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegRepo->method('getDelegaciones')->willReturn([$deleg]);

        $oPlazas = $this->createMock(ActividadPlazas::class);
        $oPlazas->method('getArrayCedidas')->willReturn(['dlx' => 1]);

        $plazasDlRepo = $this->createMock(ActividadPlazasDlRepositoryInterface::class);
        $plazasDlRepo->method('getActividadesPlazas')->willReturn([$oPlazas]);
        $oPlazas->expects($this->once())->method('setCedidas')->with(['dlx' => 1, 'dly' => 2]);
        $plazasDlRepo->expects($this->once())->method('Guardar')->with($oPlazas)->willReturn(true);

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->method('getPlazasCalendario')->willReturn(5);

        $calRepo = $this->createMock(ActividadPlazasRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $delegRepo,
            ActividadPlazasDlRepositoryInterface::class => $plazasDlRepo,
            ActividadPlazasRepositoryInterface::class => $calRepo,
            ResumenPlazasService::class => $resumenSvc,
        ]);

        $msg = PlazasCeder::execute([
            'id_activ' => 1,
            'region_dl' => 'X-dly',
            'num_plazas' => 2,
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
