<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application\services;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\actividadplazas\domain\value_objects\DelegacionTablaCode;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\DelegacionId;

/**
 * {@see ResumenPlazasService::getResumen()} debe tolerar filas en da_plazas con plazas NULL.
 */
final class ResumenPlazasServiceTest extends TestCase
{
    private mixed $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1];
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

    public function test_getresumen_no_fatal_cuando_plazas_vo_es_null(): void
    {
        $idActiv = 42;
        $dlOrg = 'testdl';
        $idDl = 7;

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getDl_org')->willReturn($dlOrg);
        $actividad->method('getPlazasVo')->willReturn(null);
        $actividad->method('getId_ubi')->willReturn(null);

        $actividadPlazas = new ActividadPlazas();
        $actividadPlazas->setId_activ($idActiv);
        $actividadPlazas->setId_dl($idDl);
        $actividadPlazas->setPlazasVo(null);
        $actividadPlazas->setDlTablaVo(DelegacionTablaCode::fromNullableString($dlOrg));

        $actividadRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actividadRepo->method('findById')->with($idActiv)->willReturn($actividad);

        $plazasRepo = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $plazasRepo->method('getActividadesPlazas')
            ->with(['id_activ' => $idActiv])
            ->willReturn([$actividadPlazas]);

        $delegacion = $this->createMock(Delegacion::class);
        $delegacion->method('getDlVo')->willReturn(new DelegacionCode($dlOrg));
        $delegacion->method('getIdDlVo')->willReturn(new DelegacionId($idDl));

        $delegacionRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $delegacionRepo->method('getDelegaciones')->willReturn([$delegacion]);

        $asistenteSvc = $this->createMock(AsistenteActividadService::class);
        $asistenteSvc->method('getPlazasOcupadasPorDl')->willReturn(0);

        $svc = new ResumenPlazasService(
            $actividadRepo,
            $plazasRepo,
            $delegacionRepo,
            $asistenteSvc
        );
        $svc->setId_activ($idActiv);

        $resumen = $svc->getResumen();

        $this->assertSame(0, $resumen[$dlOrg]['calendario']);
        $this->assertArrayHasKey('total', $resumen);
    }
}
