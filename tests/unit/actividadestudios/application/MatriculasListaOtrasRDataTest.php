<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\application\MatriculasListaOtrasRData;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class MatriculasListaOtrasRDataTest extends TestCase
{
    public function test_lista_personas_de_paso_con_nombre_y_omite_fin_de_ciclo(): void
    {
        $paso = $this->nota(-5, 2222);
        $finBienio = $this->nota(-5, 9999);
        $sinFicha = $this->nota(-7, 1111);

        $notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notas->expects($this->once())
            ->method('getNotasPersonasDePasoDeRegion')
            ->with('H-Hv')
            ->willReturn([$paso, $finBienio, $sinFicha]);

        $ficha = $this->createMock(PersonaEx::class);
        $ficha->method('getId_nom')->willReturn(-5);
        $ficha->method('getPrefApellidosNombre')->willReturn('Burgos, Jesús');
        $ficha->method('getDl')->willReturn('dlal');

        $personasEx = $this->createMock(PersonaExRepositoryInterface::class);
        $personasEx->expects($this->once())
            ->method('getPersonas')
            ->with(['id_nom' => [-5, -7]], ['id_nom' => 'IN'])
            ->willReturn([$ficha]);

        $asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $asignaturas->method('getArrayAsignaturas')->willReturn([
            1111 => 'Latín',
            2222 => 'Griego',
            9999 => 'fin bienio',
        ]);

        $useCase = new MatriculasListaOtrasRData(
            $this->createMock(PersonaPubRepositoryInterface::class),
            $asignaturas,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(ActaRepositoryInterface::class),
            $this->createMock(DelegacionRepositoryInterface::class),
            $notas,
            $personasEx,
        );

        $out = $useCase->execute([
            'apellido1' => '',
            'esquema_region_stgr' => 'H-Hv',
        ]);

        $this->assertSame('', $out['msg_err']);
        $this->assertSame(_('Personas de paso pendientes de certificado'), $out['titulo']);

        $porId = [];
        foreach ($out['a_valores'] as $fila) {
            $porId[$fila[5]] = $fila;
        }
        $this->assertSame(['Burgos, Jesús', 'dlal', 'Griego'], [$porId[-5][1], $porId[-5][2], $porId[-5][4]]);
        $this->assertSame(sprintf(_('sin ficha (%d)'), -7), $porId[-7][1]);
        $this->assertSame('Latín', $porId[-7][4]);
        $this->assertArrayNotHasKey(10, $porId);
        $this->assertCount(2, $porId);
    }

    private function nota(int $idNom, int $idAsignatura): PersonaNota
    {
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn($idNom);
        $nota->method('getId_asignatura')->willReturn($idAsignatura);
        $nota->method('getId_activ')->willReturn(null);
        $nota->method('getActa')->willReturn(null);

        return $nota;
    }
}
