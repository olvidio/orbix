<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\notas\application\ActaVerAddPersonaFormData;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\value_objects\DateTimeLocal;

final class ActaVerAddPersonaFormDataTest extends TestCase
{
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
        $asigRepo->method('getAsignaturas')->willReturn([$asig]);

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
            ->method('getPersonas')
            ->with(
                $this->callback(static function (array $where): bool {
                    return ($where['nivel_stgr'] ?? null) === NivelStgrId::R;
                }),
                $this->callback(static function (array $op): bool {
                    return ($op['nivel_stgr'] ?? null) === '!=';
                }),
            )
            ->willReturn([$ok, $repaso, $yaNota]);

        $pubRepo = $this->createMock(PersonaPubRepositoryInterface::class);
        $pubRepo->method('getPersonas')->willReturn([]);

        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn(9);
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->expects($this->once())
            ->method('getPersonaNotas')
            ->with(['id_asignatura' => 100])
            ->willReturn([$nota]);

        $uc = new ActaVerAddPersonaFormData($actaRepo, $asigRepo, $dlRepo, $pubRepo, $notaRepo);
        $r = $uc->execute(['acta' => 'dlb 1/26']);

        $this->assertTrue($r['puede_anadir'] ?? false);
        $this->assertSame([7 => 'Pérez, Juan'], $r['opciones_personas'] ?? []);
    }
}
