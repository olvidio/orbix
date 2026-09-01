<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\LiberarHuecoNivelNota;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\TipoActa;

final class LiberarHuecoNivelNotaTest extends TestCase
{
    public function test_reubica_latin_iii_del_hueco_2212_al_2112(): void
    {
        $latin3 = $this->asignatura(2211, 2112, 'Latín III');
        $latin4 = $this->asignatura(2312, 2212, 'Latín IV');
        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->willReturnCallback(
            static function (int $id) use ($latin3, $latin4): ?Asignatura {
                return match ($id) {
                    2211 => $latin3,
                    2312 => $latin4,
                    default => null,
                };
            }
        );

        $ocupante = $this->nota(1004199, 2211, 2212, 'dlmO 35/13');
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturnCallback(
            static function (array $where) use ($ocupante): array {
                $nivel = (int) ($where['id_nivel'] ?? 0);
                if ($nivel === 2212) {
                    return [$ocupante];
                }

                return [];
            }
        );
        $notaRepo->expects($this->once())
            ->method('actualizarIdNivel')
            ->with(1004199, 2212, TipoActa::FORMATO_ACTA, 2112)
            ->willReturn(true);

        $this->liberar($asigRepo)->asegurarLibre($notaRepo, 1004199, 2212, 2312);
    }

    public function test_no_hace_nada_si_el_hueco_esta_libre(): void
    {
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturn([]);
        $notaRepo->expects($this->never())->method('actualizarIdNivel');

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->liberar($asigRepo)->asegurarLibre($notaRepo, 1, 2212, 2312);
    }

    public function test_lanza_si_el_destino_del_ocupante_ya_esta_ocupado(): void
    {
        $latin3 = $this->asignatura(2211, 2112, 'Latín III');
        $latin4 = $this->asignatura(2312, 2212, 'Latín IV');
        $hebreo = $this->asignatura(2112, 2112, 'Hebraica');
        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->willReturnCallback(
            static function (int $id) use ($latin3, $latin4, $hebreo): ?Asignatura {
                return match ($id) {
                    2211 => $latin3,
                    2312 => $latin4,
                    2112 => $hebreo,
                    default => null,
                };
            }
        );

        $latin3Nota = $this->nota(9, 2211, 2212, 'dlmO 35/13');
        $hebreoNota = $this->nota(9, 2112, 2112, 'dlb 1/10');
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturnCallback(
            static function (array $where) use ($latin3Nota, $hebreoNota): array {
                return ((int) ($where['id_nivel'] ?? 0) === 2212) ? [$latin3Nota] : [$hebreoNota];
            }
        );
        $notaRepo->expects($this->never())->method('actualizarIdNivel');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('2112');
        $this->liberar($asigRepo)->asegurarLibre($notaRepo, 9, 2212, 2312);
    }

    public function test_lanza_si_el_ocupante_no_esta_en_el_plan(): void
    {
        $latin4 = $this->asignatura(2312, 2212, 'Latín IV');
        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->willReturnCallback(
            static function (int $id) use ($latin4): ?Asignatura {
                return $id === 2312 ? $latin4 : null;
            }
        );

        $hebreoNota = $this->nota(3, 2112, 2112, 'x');
        $notaRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturn([$hebreoNota]);
        $notaRepo->expects($this->never())->method('actualizarIdNivel');

        $this->expectException(\RuntimeException::class);
        $this->liberar($asigRepo)->asegurarLibre($notaRepo, 3, 2112, 2211);
    }

    private function liberar(AsignaturaRepositoryInterface $asigRepo): LiberarHuecoNivelNota
    {
        $planRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $planRepo->method('getPersonaNotas')->willReturn([]);

        return new LiberarHuecoNivelNota($asigRepo, new PlanEstudiosDePersona($planRepo));
    }

    private function asignatura(int $id, int $nivel, string $nombre): Asignatura
    {
        $asig = $this->createMock(Asignatura::class);
        $asig->method('getId_asignatura')->willReturn($id);
        $asig->method('getId_nivel')->willReturn($nivel);
        $asig->method('getNombre_corto')->willReturn($nombre);
        $asig->method('isActive')->willReturn(true);

        return $asig;
    }

    private function nota(int $idNom, int $idAsig, int $idNivel, string $acta): PersonaNota
    {
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn($idNom);
        $nota->method('getId_asignatura')->willReturn($idAsig);
        $nota->method('getId_nivel')->willReturn($idNivel);
        $nota->method('getActa')->willReturn($acta);
        $nota->method('getTipo_acta')->willReturn(TipoActa::FORMATO_ACTA);

        return $nota;
    }
}
