<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\ActaVerNotasListadoData;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;

final class ActaVerNotasListadoDataTest extends TestCase
{
    public function test_sin_acta_devuelve_vacio(): void
    {
        $uc = new ActaVerNotasListadoData(
            $this->createMock(PersonaNotaRepositoryInterface::class),
            $this->createMock(PersonaFinderService::class),
        );

        $r = $uc->execute([]);
        $this->assertSame([], $r['filas']);
        $this->assertSame([], $r['avisos']);
    }

    public function test_lista_alumnos_con_nota_ordenados(): void
    {
        $notaA = $this->createMock(PersonaNota::class);
        $notaA->method('getId_nom')->willReturn(2);
        $notaA->method('getNota_txt')->willReturn('8/10');
        $notaA->method('getId_situacion')->willReturn(NotaSituacion::NUMERICA);

        $notaB = $this->createMock(PersonaNota::class);
        $notaB->method('getId_nom')->willReturn(1);
        $notaB->method('getNota_txt')->willReturn('Non probatus (4/10)');
        $notaB->method('getId_situacion')->willReturn(NotaSituacion::EXAMINADO);

        $repo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getPersonaNotas')
            ->with([
                'acta' => 'dlb 1/26',
                'tipo_acta' => TipoActa::FORMATO_ACTA,
            ])
            ->willReturn([$notaA, $notaB]);

        $personaA = $this->createMock(PersonaDl::class);
        $personaA->method('getApellidosUpperNombre')->willReturn('ZETA, Ana');
        $personaB = $this->createMock(PersonaDl::class);
        $personaB->method('getApellidosUpperNombre')->willReturn('ALFA, Juan');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(
            static function (int $id) use ($personaA, $personaB) {
                return match ($id) {
                    2 => $personaA,
                    1 => $personaB,
                    default => null,
                };
            }
        );

        $uc = new ActaVerNotasListadoData($repo, $finder);
        $r = $uc->execute(['acta' => 'dlb 1/26']);

        $this->assertCount(2, $r['filas']);
        $this->assertSame('ALFA, Juan', $r['filas'][0]['nombre']);
        $this->assertSame('Non probatus (4/10)', $r['filas'][0]['nota']);
        $this->assertSame('ZETA, Ana', $r['filas'][1]['nombre']);
        $this->assertSame([], $r['avisos']);
    }

    public function test_aviso_si_no_se_encuentra_persona(): void
    {
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn(99);
        $nota->method('getNota_txt')->willReturn('7/10');
        $nota->method('getId_situacion')->willReturn(NotaSituacion::NUMERICA);

        $repo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repo->method('getPersonaNotas')->willReturn([$nota]);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->with(99)->willReturn(null);

        $uc = new ActaVerNotasListadoData($repo, $finder);
        $r = $uc->execute(['acta' => 'x']);

        $this->assertSame([], $r['filas']);
        $this->assertCount(1, $r['avisos']);
    }

    public function test_incluye_persona_de_paso_con_id_negativo(): void
    {
        $idDePaso = -100162859;
        $nota = $this->createMock(PersonaNota::class);
        $nota->method('getId_nom')->willReturn($idDePaso);
        $nota->method('getNota_txt')->willReturn('9/10');
        $nota->method('getId_situacion')->willReturn(NotaSituacion::NUMERICA);

        $repo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repo->method('getPersonaNotas')->willReturn([$nota]);

        $personaEx = $this->createMock(PersonaEx::class);
        $personaEx->method('getApellidosUpperNombre')->willReturn('PASO, Luis');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('findPersonaEnGlobalODePaso')
            ->with($idDePaso)
            ->willReturn($personaEx);

        $uc = new ActaVerNotasListadoData($repo, $finder);
        $r = $uc->execute(['acta' => 'dlb 1/26']);

        $this->assertCount(1, $r['filas']);
        $this->assertSame($idDePaso, $r['filas'][0]['id_nom']);
        $this->assertSame('PASO, Luis', $r['filas'][0]['nombre']);
        $this->assertSame('9/10', $r['filas'][0]['nota']);
        $this->assertSame([], $r['avisos']);
    }
}
