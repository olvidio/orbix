<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\MatriculaEliminar;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\dossiers\domain\contracts\DossierRepositoryInterface;

final class MatriculaEliminarTest extends TestCase
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

    public function test_pau_vacio_devuelve_cadena_vacia(): void
    {
        $GLOBALS['container'] = $this->containerFromMap($this->minimalRepos());

        $this->assertSame('', MatriculaEliminar::execute([]));
    }

    public function test_p_a_sin_matricula(): void
    {
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->with(1, 2, 3)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            ...$this->minimalRepos(),
            MatriculaDlRepositoryInterface::class => $matRepo,
        ]);

        $msg = MatriculaEliminar::execute([
            'pau' => 'a',
            'id_activ' => 1,
            'id_asignatura' => 2,
            'id_nom' => 3,
        ]);
        $this->assertStringContainsString('no encuentro la matricula', $msg);
    }

    public function test_p_a_exito_y_cierra_dossier_cuando_existe(): void
    {
        $oMat = $this->createMock(Matricula::class);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->with(1, 2, 3)->willReturn($oMat);
        $matRepo->method('Eliminar')->with($oMat)->willReturn(true);

        $dossierRepo = $this->createMock(DossierRepositoryInterface::class);
        $dossierRepo->expects($this->once())->method('findByPk');
        $dossierRepo->expects($this->never())->method('Guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadAsignaturaDlRepositoryInterface::class => $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            MatriculaDlRepositoryInterface::class => $matRepo,
            DossierRepositoryInterface::class => $dossierRepo,
        ]);

        $msg = MatriculaEliminar::execute([
            'pau' => 'a',
            'id_activ' => 1,
            'id_asignatura' => 2,
            'id_nom' => 3,
        ]);
        $this->assertSame('', $msg);
    }

    public function test_p_a_error_al_eliminar(): void
    {
        $oMat = $this->createMock(Matricula::class);
        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->willReturn($oMat);
        $matRepo->method('Eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadAsignaturaDlRepositoryInterface::class => $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            MatriculaDlRepositoryInterface::class => $matRepo,
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $msg = MatriculaEliminar::execute([
            'pau' => 'a',
            'id_activ' => 1,
            'id_nom' => 3,
            'id_asignatura' => 2,
        ]);
        $this->assertStringContainsString('no se ha borrado', $msg);
    }

    public function test_p_p_borra_asignatura_impartida_si_queda_huerfana(): void
    {
        $oMat = $this->createMock(Matricula::class);
        $oAa = $this->createMock(ActividadAsignatura::class);

        $matRepo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $matRepo->method('findById')->with(10, 20, 30)->willReturn($oMat);
        $matRepo->method('Eliminar')->with($oMat)->willReturn(true);
        $matRepo->method('getMatriculas')->with([
            'id_activ' => 10,
            'id_asignatura' => 20,
        ])->willReturn([]);

        $aaRepo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->with([
            'id_activ' => 10,
            'id_asignatura' => 20,
        ])->willReturn([$oAa]);
        $aaRepo->expects($this->once())->method('Eliminar')->with($oAa)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadAsignaturaDlRepositoryInterface::class => $aaRepo,
            MatriculaDlRepositoryInterface::class => $matRepo,
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $msg = MatriculaEliminar::execute([
            'pau' => 'p',
            'sel' => ['10#20#30'],
        ]);
        $this->assertSame('', $msg);
    }

    /**
     * @return array<class-string, object>
     */
    private function minimalRepos(): array
    {
        return [
            ActividadAsignaturaDlRepositoryInterface::class => $this->createMock(ActividadAsignaturaDlRepositoryInterface::class),
            MatriculaDlRepositoryInterface::class => $this->createMock(MatriculaDlRepositoryInterface::class),
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ];
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
