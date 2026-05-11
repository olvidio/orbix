<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\MatriculasPendientesData;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;

final class MatriculasPendientesDataTest extends TestCase
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

    public function test_lista_vacia(): void
    {
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->expects($this->never())->method('findById');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->expects($this->never())->method('findById');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->never())->method('findPersonaEnGlobal');

        $GLOBALS['container'] = $this->containerFromMap([
            MatriculaDlRepositoryInterface::class => $matRepo,
            AsignaturaRepositoryInterface::class => $asigRepo,
            ActividadAllRepositoryInterface::class => $actRepo,
            PersonaFinderService::class => $finder,
        ]);

        $out = MatriculasPendientesData::execute();
        $this->assertSame('', $out['msg_err']);
        $this->assertSame([], $out['a_valores']);
    }

    public function test_una_fila_completa(): void
    {
        $oMat = $this->createMock(Matricula::class);
        $oMat->method('getId_nom')->willReturn(100);
        $oMat->method('getId_activ')->willReturn(200);
        $oMat->method('getId_asignatura')->willReturn(300);
        $oMat->method('isPreceptor')->willReturn(false);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('getMatriculasPendientes')->willReturn([$oMat]);

        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA X');

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(200)->willReturn($oActiv);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('García, Ana');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobal')->with(100)->willReturn($oPersona);

        $oAsig = $this->createMock(Asignatura::class);
        $oAsig->method('getNombre_corto')->willReturn('MAT1');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->with(300)->willReturn($oAsig);

        $GLOBALS['container'] = $this->containerFromMap([
            MatriculaDlRepositoryInterface::class => $matRepo,
            AsignaturaRepositoryInterface::class => $asigRepo,
            ActividadAllRepositoryInterface::class => $actRepo,
            PersonaFinderService::class => $finder,
        ]);

        $out = MatriculasPendientesData::execute();
        $this->assertSame('', $out['msg_err']);
        $this->assertCount(1, $out['a_valores']);
        $row = $out['a_valores'][1];
        $this->assertSame('200#300#100', $row['sel']);
        $this->assertSame('CA X', $row[1]);
        $this->assertSame('MAT1', $row[2]);
        $this->assertSame('García, Ana', $row[3]);
        $this->assertSame('', $row[4]);
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
