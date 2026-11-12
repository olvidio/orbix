<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use src\notas\domain\criteria\PersonaNotaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\notas\application\ActaVerAddPersonaFormData;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\value_objects\DateTimeLocal;

final class ActaVerAddPersonaFormDataTest extends TestCase
{
    private mixed $configAnterior;
    /** @var array<string, mixed> */
    private array $authAnterior;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->authAnterior = $_SESSION['session_auth'] ?? [];
        $_SESSION['session_auth'] = array_merge($this->authAnterior, ['esquema' => 'H-dlbv', 'sfsv' => 1]);
    }

    protected function tearDown(): void
    {
        $_SESSION['oConfig'] = $this->configAnterior;
        $_SESSION['session_auth'] = $this->authAnterior;
        parent::tearDown();
    }

    public function test_error_sin_acta(): void
    {
        $uc = new ActaVerAddPersonaFormData(
            $this->createMock(ActaRepositoryInterface::class),
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaPubRepositoryInterface::class),
            $this->createMock(PersonaNotaRepositoryInterface::class),
        );

        $r = $uc->execute([]);
        $this->assertFalse($r['puede_anadir'] ?? true);
        $this->assertNotEmpty($r['error'] ?? '');
    }

    public function test_lista_excluye_repaso_y_quien_ya_tiene_nota(): void
    {
        $_SESSION['oConfig'] = new class {
            public function getNotaMax(): int
            {
                return 10;
            }
        };

        $acta = $this->createMock(Acta::class);
        $acta->method('getId_asignatura')->willReturn(100);
        $acta->method('getId_activ')->willReturn(5);
        $acta->method('getF_acta')->willReturn(DateTimeLocal::createFromLocal('15/01/2026'));

        $actaRepo = $this->createMock(ActaRepositoryInterface::class);
        $actaRepo->method('findById')->with('dlb 1/26')->willReturn($acta);

        $asig = $this->createMock(Asignatura::class);
        $asig->method('getId_nivel')->willReturn(1100);
        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->with(100, PlanEstudios::PLAN_2026)->willReturn($asig);

        $ok = $this->createMock(PersonaDl::class);
        $ok->method('getId_nom')->willReturn(7);
        $ok->method('getPrefApellidosNombre')->willReturn('Pérez, Juan');
        $ok->method('getNivel_stgr')->willReturn(NivelStgrId::N);

        $repaso = $this->createMock(PersonaDl::class);
        $repaso->method('getId_nom')->willReturn(8);
        $repaso->method('getPrefApellidosNombre')->willReturn('Repaso, Ana');
        $repaso->method('getNivel_stgr')->willReturn(NivelStgrId::R);

        $yaNota = $this->createMock(PersonaDl::class);
        $yaNota->method('getId_nom')->willReturn(9);
        $yaNota->method('getPrefApellidosNombre')->willReturn('ConNota, Luis');
        $yaNota->method('getNivel_stgr')->willReturn(NivelStgrId::N);

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(
                Criteria::create()
                    ->where(Filter::eq(PersonaField::Situacion, 'A'))
                    ->andWhere(Filter::neq(PersonaField::NivelStgr, NivelStgrId::R))
                    ->orderBy(PersonaField::Apellido1)
                    ->addOrderBy(PersonaField::Apellido2)
                    ->addOrderBy(PersonaField::Nom)
            ))
            ->willReturn([$ok, $repaso, $yaNota]);

        $pubRepo = $this->createMock(PersonaPubRepositoryInterface::class);
        $pubRepo->method('matching')->willReturn([]);

        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn(9);
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(Criteria::create()->where(Filter::eq(F::IdAsignatura, 100))))
            ->willReturn([$nota]);

        $uc = new ActaVerAddPersonaFormData($actaRepo, $asigRepo, $dlRepo, $pubRepo, $notaRepo);
        $r = $uc->execute(['acta' => 'dlb 1/26']);

        $this->assertTrue($r['puede_anadir'] ?? false);
        $this->assertSame([7 => 'Pérez, Juan'], $r['opciones_personas'] ?? []);
    }

    public function test_acta_inexistente_o_sin_asignatura(): void
    {
        $sinAsignatura = $this->createStub(Acta::class);
        $sinAsignatura->method('getId_asignatura')->willReturn(null);
        $actaRepo = $this->createStub(ActaRepositoryInterface::class);
        $actaRepo->method('findById')->willReturnCallback(static fn (string $a): ?Acta => $a === 'dlb 2/26' ? $sinAsignatura : null);
        $uc = new ActaVerAddPersonaFormData(
            $actaRepo,
            $this->createStub(AsignaturaRepositoryInterface::class),
            $this->createStub(PersonaDlRepositoryInterface::class),
            $this->createStub(PersonaPubRepositoryInterface::class),
            $this->createStub(PersonaNotaRepositoryInterface::class),
        );

        $this->assertSame(['error' => 'No se encuentra el acta', 'puede_anadir' => false], $uc->execute(['acta' => 'dlb 9/26']));
        $this->assertSame(['error' => 'El acta no tiene asignatura', 'puede_anadir' => false], $uc->execute(['acta' => 'dlb 2/26']));
    }

    private function pub(int $id, string $nombre, mixed $publicadoPara, string $dl = 'dlz', int $nivel = NivelStgrId::N): \src\personas\domain\entity\PersonaPub
    {
        $p = $this->createStub(\src\personas\domain\entity\PersonaPub::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);
        $p->method('getNivel_stgr')->willReturn($nivel);
        $p->method('getPublicado_para')->willReturn($publicadoPara);
        $p->method('getDl')->willReturn($dl);

        return $p;
    }

    /**
     * @param list<\src\personas\domain\entity\PersonaPub>|\Throwable $publicadas
     */
    private function conPublicadas(array|\Throwable $publicadas): ActaVerAddPersonaFormData
    {
        $_SESSION['oConfig'] = new class {
            public function getNotaMax(): int
            {
                return 10;
            }
        };
        $acta = $this->createStub(Acta::class);
        $acta->method('getId_asignatura')->willReturn(100);
        $actaRepo = $this->createStub(ActaRepositoryInterface::class);
        $actaRepo->method('findById')->willReturn($acta);

        $local = $this->createStub(PersonaDl::class);
        $local->method('getId_nom')->willReturn(7);
        $local->method('getPrefApellidosNombre')->willReturn('Pérez, Juan');
        $local->method('getNivel_stgr')->willReturn(NivelStgrId::N);
        $dlRepo = $this->createStub(PersonaDlRepositoryInterface::class);
        $dlRepo->method('matching')->willReturn([$local]);

        $pubRepo = $this->createStub(PersonaPubRepositoryInterface::class);
        if ($publicadas instanceof \Throwable) {
            $pubRepo->method('matching')->willThrowException($publicadas);
        } else {
            $pubRepo->method('matching')->willReturn($publicadas);
        }
        $notaRepo = $this->createStub(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('matching')->willReturn([]);

        return new ActaVerAddPersonaFormData($actaRepo, $this->createStub(AsignaturaRepositoryInterface::class), $dlRepo, $pubRepo, $notaRepo);
    }

    public function test_publicadas_solo_si_lo_estan_para_mi_dl_y_vigentes(): void
    {
        $r = $this->conPublicadas([
            $this->pub(20, 'Abad, Pedro', '{"dlb": null}'),
            $this->pub(21, 'Zamora, Luis', ['dlb' => '2999-01-01']),
            $this->pub(22, 'Caducada, Eva', ['dlb' => '2001-01-01']),
            $this->pub(23, 'Otra, Ana', ['dlmE' => null]),
            $this->pub(24, 'Todas, Ruth', ['*' => null]),
            $this->pub(25, 'Repaso, Iván', ['dlb' => null], 'dlz', NivelStgrId::R),
            $this->pub(7, 'Pérez, Juan (duplicado)', ['dlb' => null]),
            $this->pub(26, 'Sin Dl, Rosa', ['dlb' => null], ''),
        ])->execute(['acta' => 'dlb 1/26']);

        $this->assertSame(
            [20 => 'Abad, Pedro (dlz)', 7 => 'Pérez, Juan', 26 => 'Sin Dl, Rosa', 21 => 'Zamora, Luis (dlz)'],
            $r['opciones_personas'],
            'ordenadas por nombre; sin caducadas, de otra dl, «*», repaso ni duplicados'
        );
        $this->assertSame(10, $r['nota_max_default']);
        $this->assertSame(0, $r['id_nivel'], 'asignatura fuera del plan 2026');
    }

    public function test_error_al_leer_las_publicadas(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No se han podido cargar las personas publicadas: sin conexión');

        $this->conPublicadas(new \RuntimeException('sin conexión'))->execute(['acta' => 'dlb 1/26']);
    }
}
