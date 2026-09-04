<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\MatriculaAutomatica;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaEx;

final class MatriculaAutomaticaTest extends TestCase
{
    public function test_persona_no_encontrada(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->with(-100162294)->willReturn(null);

        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->expects($this->never())->method('Guardar');

        $out = $this->useCase(['finder' => $finder, 'mat' => $mat])->execute([
            'id_pau' => -100162294,
            'id_activ' => 300123813,
        ]);

        $this->assertFalse($out['success']);
        $this->assertStringContainsString('-100162294', $out['msg']);
    }

    public function test_nivel_repaso_no_matricula(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturn(
            $this->personaDePaso(NivelStgrId::R),
        );

        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->expects($this->never())->method('Guardar');

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->expects($this->never())->method('getPersonas');

        $out = $this->useCase(['finder' => $finder, 'mat' => $mat, 'dl' => $dlRepo])->execute([
            'id_pau' => -100162294,
            'id_activ' => 300123813,
        ]);

        $this->assertFalse($out['success']);
        $this->assertSame(_('está de repaso'), $out['msg']);
    }

    public function test_nivel_stgr_vacio_no_es_repaso_y_matricula(): void
    {
        $idNom = -100162294;
        $idActiv = 300123813;

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->with($idNom)->willReturn(
            $this->personaDePaso(null),
        );

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->expects($this->never())->method('getPersonas');

        $asistente = $this->createStub(Asistente::class);
        $asistente->method('getId_activ')->willReturn($idActiv);
        $asistente->method('isEst_ok')->willReturn(false);

        $asistenteEx = $this->createMock(AsistenteExRepositoryInterface::class);
        $asistenteEx->method('findById')->with($idActiv, $idNom)->willReturn($asistente);

        $asistenteDl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $asistenteDl->expects($this->never())->method('findById');

        $asistenteOut = $this->createMock(AsistenteOutRepositoryInterface::class);
        $asistenteOut->expects($this->never())->method('findById');

        $oferta = new ActividadAsignatura();
        $oferta->setId_activ($idActiv);
        $oferta->setIdAsignaturaVo(1101);

        $asignaturas = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $asignaturas->method('getActividadAsignaturas')->willReturn([$oferta]);

        $notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notas->method('getPersonaNotas')->willReturn([]);

        $mat = $this->createMock(MatriculaDlRepositoryInterface::class);
        $mat->method('getMatriculas')->willReturn([]);
        $mat->expects($this->once())->method('Guardar')->willReturn(true);

        $out = $this->useCase([
            'finder' => $finder,
            'dl' => $dlRepo,
            'asistenteDl' => $asistenteDl,
            'asistenteOut' => $asistenteOut,
            'asistenteEx' => $asistenteEx,
            'mat' => $mat,
            'notas' => $notas,
            'asignaturas' => $asignaturas,
        ])->execute([
            'id_pau' => $idNom,
            'id_activ' => $idActiv,
        ]);

        $this->assertTrue($out['success']);
        $this->assertStringContainsString('Alarcón Torres', $out['msg']);
        $this->assertStringContainsString('1', $out['msg']);
        $this->assertStringNotContainsString(_('está de repaso'), $out['msg']);
    }

    /**
     * @param array<string, object> $deps
     */
    private function useCase(array $deps = []): MatriculaAutomatica
    {
        return new MatriculaAutomatica(
            $deps['finder'] ?? $this->createMock(PersonaFinderService::class),
            $deps['dl'] ?? $this->createMock(PersonaDlRepositoryInterface::class),
            $deps['asistenteActividad'] ?? $this->createMock(AsistenteActividadService::class),
            $deps['asistenteDl'] ?? $this->createMock(AsistenteDlRepositoryInterface::class),
            $deps['asistenteOut'] ?? $this->createMock(AsistenteOutRepositoryInterface::class),
            $deps['asistenteEx'] ?? $this->createMock(AsistenteExRepositoryInterface::class),
            $deps['mat'] ?? $this->createMock(MatriculaDlRepositoryInterface::class),
            $deps['notas'] ?? $this->createMock(PersonaNotaRepositoryInterface::class),
            $deps['asignaturas'] ?? $this->createMock(ActividadAsignaturaRepositoryInterface::class),
        );
    }

    private function personaDePaso(?int $nivelStgr): PersonaEx
    {
        $persona = $this->createStub(PersonaEx::class);
        $persona->method('getId_nom')->willReturn(-100162294);
        $persona->method('getNivel_stgr')->willReturn($nivelStgr);
        $persona->method('getPrefApellidosNombre')->willReturn('Alarcón Torres');

        return $persona;
    }
}
