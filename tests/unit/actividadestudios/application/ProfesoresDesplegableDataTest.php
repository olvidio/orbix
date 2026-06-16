<?php

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ProfesoresDesplegableData;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\profesores\domain\ProfesorActividad;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;

final class ProfesoresDesplegableDataTest extends TestCase
{
    private function useCase(
        ?ProfesorAsignaturaService $asigSvc = null,
        ?ProfesorStgrService $stgrSvc = null,
        ?PersonaDlRepositoryInterface $personaRepo = null,
        ?ProfesorActividad $profesorActividad = null,
    ): ProfesoresDesplegableData {
        return new ProfesoresDesplegableData(
            $asigSvc ?? $this->createMock(ProfesorAsignaturaService::class),
            $stgrSvc ?? $this->createMock(ProfesorStgrService::class),
            $personaRepo ?? $this->createMock(PersonaDlRepositoryInterface::class),
            $profesorActividad ?? $this->createMock(ProfesorActividad::class),
        );
    }

    public function test_salida_desconocida_devuelve_opciones_vacias(): void
    {
        $out = $this->useCase()->execute(['salida' => 'otro']);
        $this->assertSame([
            'id' => 'id_profesor',
            'opciones' => [],
            'blanco' => true,
            'val_blanco' => '',
            'selected' => -1,
        ], $out);
    }

    public function test_salida_asignatura_usa_profesor_asignatura_service(): void
    {
        $opciones = [5 => 'Prof. X'];
        $svc = $this->createMock(ProfesorAsignaturaService::class);
        $svc->expects($this->once())
            ->method('getArrayTodosProfesoresAsignatura')
            ->with($this->callback(fn (AsignaturaId $id) => $id->value() === 1000))
            ->willReturn($opciones);

        $out = $this->useCase($svc)->execute([
            'salida' => 'asignatura',
            'id_asignatura' => 1000,
        ]);
        $this->assertSame([['5', 'Prof. X']], $out['opciones']);
    }

    public function test_opciones_preservan_orden_del_backend_no_por_id_en_json(): void
    {
        $opciones = [456 => 'Alvarez', 12 => 'Zapata'];
        $svc = $this->createMock(ProfesorStgrService::class);
        $svc->method('getArrayProfesoresPub')->willReturn($opciones);

        $out = $this->useCase(stgrSvc: $svc)->execute(['salida' => 'todos']);
        $this->assertSame([['456', 'Alvarez'], ['12', 'Zapata']], $out['opciones']);
    }

    public function test_antepone_profesor_asignado_si_no_esta_en_filtro(): void
    {
        $opciones = [5 => 'Prof. X'];
        $asigSvc = $this->createMock(ProfesorAsignaturaService::class);
        $asigSvc->method('getArrayTodosProfesoresAsignatura')->willReturn($opciones);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('Álvarez, Pedro');
        $personaRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $personaRepo->method('findById')->with(99)->willReturn($oPersona);

        $out = $this->useCase($asigSvc, personaRepo: $personaRepo)->execute([
            'salida' => 'asignatura',
            'id_asignatura' => 1000,
            'id_profesor' => 99,
        ]);

        $this->assertSame(99, $out['selected']);
        $this->assertSame([
            ['99', 'Álvarez, Pedro'],
            ['0', '----------'],
            ['5', 'Prof. X'],
        ], $out['opciones']);
    }

    public function test_id_profesor_negativo_se_antepone_y_queda_seleccionado(): void
    {
        $opciones = [5 => 'Prof. X'];
        $asigSvc = $this->createMock(ProfesorAsignaturaService::class);
        $asigSvc->method('getArrayTodosProfesoresAsignatura')->willReturn($opciones);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getPrefApellidosNombre')->willReturn('Externo, Ana');
        $personaRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $personaRepo->method('findById')->with(-42)->willReturn($oPersona);

        $out = $this->useCase($asigSvc, personaRepo: $personaRepo)->execute([
            'salida' => 'asignatura',
            'id_asignatura' => 1000,
            'id_profesor' => -42,
        ]);

        $this->assertSame(-42, $out['selected']);
        $this->assertSame([
            ['-42', 'Externo, Ana'],
            ['0', '----------'],
            ['5', 'Prof. X'],
        ], $out['opciones']);
    }

    public function test_con_profesor_asignado_no_duplica_si_ya_esta_en_lista(): void
    {
        $useCase = $this->useCase();
        $merged = $useCase->conProfesorAsignadoSiFalta([5 => 'Prof. X'], 5);
        $this->assertSame([5 => 'Prof. X'], $merged);
    }

    public function test_salida_todos_usa_profesor_stgr_service(): void
    {
        $opciones = [1 => 'Pub 1'];
        $svc = $this->createMock(ProfesorStgrService::class);
        $svc->expects($this->once())->method('getArrayProfesoresPub')->willReturn($opciones);

        $out = $this->useCase(stgrSvc: $svc)->execute(['salida' => 'todos']);
        $this->assertSame([['1', 'Pub 1']], $out['opciones']);
    }
}
