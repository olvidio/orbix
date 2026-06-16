<?php

declare(strict_types=1);

namespace Tests\unit\profesores\application;

use PHPUnit\Framework\TestCase;
use src\profesores\application\CongresosLista;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\profesores\domain\services\ProfesorStgrService;
use src\shared\domain\value_objects\DateTimeLocal;

final class CongresosListaTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_tabla_con_un_congreso_ambito_no_rstgr(): void
    {
        $this->stubOConfigAmbito('dl');

        $fIni = $this->createMock(DateTimeLocal::class);
        $fIni->method('getFromLocal')->willReturn('01/06/2024');
        $fFin = $this->createMock(DateTimeLocal::class);
        $fFin->method('getFromLocal')->willReturn('03/06/2024');

        $oCongreso = $this->createMock(\src\profesores\domain\entity\ProfesorCongreso::class);
        $oCongreso->method('getTipo')->willReturn('2');
        $oCongreso->method('getLugar')->willReturn('BCN');
        $oCongreso->method('getF_ini')->willReturn($fIni);
        $oCongreso->method('getF_fin')->willReturn($fFin);
        $oCongreso->method('getOrganiza')->willReturn('ACME');

        $repoCongreso = $this->createMock(ProfesorCongresoRepositoryInterface::class);
        $repoCongreso->method('getProfesorCongresos')->willReturn([$oCongreso]);

        $svc = $this->createMock(ProfesorStgrService::class);
        $svc->method('getArrayProfesoresConDl')->willReturn([
            50 => ['ap_nom' => 'García, Juan', 'dl' => 'dl1'],
        ]);

        $useCase = new CongresosLista($svc, $repoCongreso);
        $out = $useCase->getTablaData();

        $this->assertSame('tabla_congreso', $out['id_tabla']);
        $this->assertArrayNotHasKey(1, $out['a_cabeceras']);
        $this->assertSame(_('apellidos, nombre'), $out['a_cabeceras'][2]);
        $this->assertCount(1, $out['a_valores']);
        $this->assertSame('García, Juan', $out['a_valores'][1][2]);
        $this->assertSame(_('congreso'), $out['a_valores'][1][3]);
        $this->assertSame('BCN', $out['a_valores'][1][4]);
        $this->assertSame('01/06/2024', $out['a_valores'][1][5]);
        $this->assertSame('03/06/2024', $out['a_valores'][1][6]);
        $this->assertSame('ACME', $out['a_valores'][1][7]);
    }

    public function test_tabla_rstgr_incluye_columna_dl(): void
    {
        $this->stubOConfigAmbito('rstgr');

        $fIni = $this->createMock(DateTimeLocal::class);
        $fIni->method('getFromLocal')->willReturn('01/01/2025');
        $fFin = $this->createMock(DateTimeLocal::class);
        $fFin->method('getFromLocal')->willReturn('02/01/2025');

        $oCongreso = $this->createMock(\src\profesores\domain\entity\ProfesorCongreso::class);
        $oCongreso->method('getTipo')->willReturn('1');
        $oCongreso->method('getLugar')->willReturn('X');
        $oCongreso->method('getF_ini')->willReturn($fIni);
        $oCongreso->method('getF_fin')->willReturn($fFin);
        $oCongreso->method('getOrganiza')->willReturn('O');

        $repoCongreso = $this->createMock(ProfesorCongresoRepositoryInterface::class);
        $repoCongreso->method('getProfesorCongresos')->willReturn([$oCongreso]);

        $svc = $this->createMock(ProfesorStgrService::class);
        $svc->method('getArrayProfesoresConDl')->willReturn([
            1 => ['ap_nom' => 'Nom', 'dl' => 'reg_dl'],
        ]);

        $useCase = new CongresosLista($svc, $repoCongreso);
        $out = $useCase->getTablaData();

        $this->assertSame(_('dl'), $out['a_cabeceras'][1]);
        $this->assertSame('reg_dl', $out['a_valores'][1][1]);
    }

    private function stubOConfigAmbito(string $ambito): void
    {
        // ConfigGlobal::mi_ambito() sólo reconoce un ConfigSnapshot real (instanceof).
        // Sólo se consulta getAmbito(): el resto de parámetros pueden ir a null.
        $_SESSION['oConfig'] = new ConfigSnapshot(
            null, null, null, null, null, null,
            $ambito,
            null, null, null, null, null, null, null, null,
        );
    }
}
