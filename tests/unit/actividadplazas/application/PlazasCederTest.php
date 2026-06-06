<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\PlazasCeder;
use src\actividadplazas\application\PlazasDlEdicion;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionId;

final class PlazasCederTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_faltan_parametros(): void
    {
        $useCase = $this->makeUseCase(
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(ActividadPlazasDlRepositoryInterface::class),
            $this->createMock(ActividadPlazasRepositoryInterface::class),
        );

        $msg = $useCase->execute(['id_activ' => 0, 'region_dl' => 'R-x']);
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

        $msg = $this->makeUseCase($delegRepo, $plazasDlRepo, $calRepo)->execute([
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

        $msg = $this->makeUseCase($delegRepo, $plazasDlRepo, $calRepo)->execute([
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

        $msg = $this->makeUseCase($delegRepo, $plazasDlRepo, $calRepo, $resumenSvc)->execute([
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

        $msg = $this->makeUseCase($delegRepo, $plazasDlRepo, $calRepo, $resumenSvc)->execute([
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

        $msg = $this->makeUseCase($delegRepo, $plazasDlRepo, $calRepo, $resumenSvc)->execute([
            'id_activ' => 1,
            'region_dl' => 'X-dly',
            'num_plazas' => 2,
        ]);
        $this->assertSame('', $msg);
    }

    private function makeUseCase(
        DelegacionRepositoryInterface $delegRepo,
        ActividadPlazasDlRepositoryInterface $plazasDlRepo,
        ActividadPlazasRepositoryInterface $calRepo,
        ?ResumenPlazasService $resumenSvc = null,
    ): PlazasCeder {
        $resumenSvc ??= $this->createMock(ResumenPlazasService::class);

        return new PlazasCeder(
            $delegRepo,
            $plazasDlRepo,
            $resumenSvc,
            new PlazasDlEdicion($plazasDlRepo, $calRepo),
        );
    }
}
