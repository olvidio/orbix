<?php

declare(strict_types=1);

namespace Tests\unit\profesores\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\application\DocenciaLista;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\profesores\domain\services\ProfesorStgrService;
use src\notas\domain\value_objects\ActaNumero;

final class DocenciaListaTest extends TestCase
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

    public function test_tabla_docencia_una_fila(): void
    {
        $this->stubOConfigAmbito('dl');

        $oAsig = $this->createMock(Asignatura::class);
        $oAsig->method('getId_asignatura')->willReturn(1001);
        $oAsig->method('getNombre_corto')->willReturn('ALG');

        $repoAsig = $this->createMock(AsignaturaRepositoryInterface::class);
        $repoAsig->method('getAsignaturas')->willReturn([$oAsig]);

        $tipo = new TipoActividadAsignatura(TipoActividadAsignatura::TIPO_CA);
        $actaVo = new ActaNumero('9');

        $oDoc = $this->createMock(ProfesorDocenciaStgr::class);
        $oDoc->method('getIdAsignaturaVo')->willReturn(new AsignaturaId(1001));
        $oDoc->method('getTipoVo')->willReturn($tipo);
        $oDoc->method('getCurso_inicio')->willReturn(2023);
        $oDoc->method('getActaVo')->willReturn($actaVo);

        $repoDoc = $this->createMock(ProfesorDocenciaStgrRepositoryInterface::class);
        $repoDoc->method('getProfesorDocenciasStgr')->willReturn([$oDoc]);

        $svc = $this->createMock(ProfesorStgrService::class);
        $svc->method('getArrayProfesoresConDl')->willReturn([
            7 => ['ap_nom' => 'López, Ana', 'dl' => 'dlx'],
        ]);

        $useCase = new DocenciaLista($repoAsig, $svc, $repoDoc);
        $out = $useCase->getTablaData();

        $this->assertSame('tabla_docencia', $out['id_tabla']);
        $this->assertCount(1, $out['a_valores']);
        $this->assertSame('López, Ana', $out['a_valores'][1][2]);
        $this->assertSame(2023, $out['a_valores'][1][3]);
        $this->assertSame('ALG', $out['a_valores'][1][4]);
        $this->assertSame(_('ca/cv'), $out['a_valores'][1][5]);
        $this->assertSame('9', $out['a_valores'][1][6]);
    }

    private function stubOConfigAmbito(string $ambito): void
    {
        $_SESSION['oConfig'] = new class ($ambito) {
            public function __construct(private readonly string $ambito) {}

            public function getAmbito(): string
            {
                return $this->ambito;
            }
        };
    }

}
