<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\AsignaturaName;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Tesera;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;

final class TeseraFilasImpresionTest extends TestCase
{
    public function test_opcional_v_no_oculta_pendientes_anteriores(): void
    {
        $tesera = $this->tesera();
        $filas = $tesera->filasParaImpresion(
            [
                $this->asignatura(2433, 'Op. IV'),
                $this->asignatura(2434, 'Op. V'),
            ],
            [
                2434 => [
                    'id_nivel_asig' => 2434,
                    'id_nivel' => 2434,
                    'id_asignatura' => 3501,
                    'nombre_asignatura' => 'Seminarium',
                    'nombre_corto' => 'Sem.',
                    'fecha' => null,
                    'id_situacion' => 1,
                    'bAprobada' => 't',
                    'nota' => 'probatus',
                    'acta' => '12',
                ],
            ],
        );

        $this->assertCount(2, $filas);
        $this->assertTrue($filas[0]['pendiente']);
        $this->assertSame(2433, $filas[0]['id_nivel']);
        $this->assertSame('Op. IV', $filas[0]['nombre']);
        $this->assertFalse($filas[1]['pendiente']);
        $this->assertTrue($filas[1]['opcional']);
        $this->assertSame(2434, $filas[1]['id_nivel']);
        $this->assertSame('probatus', $filas[1]['nota']);
        $this->assertStringContainsString('Seminarium', $filas[1]['nombre']);
    }

    public function test_opcional_historica_fuera_del_plan_2026_usa_el_nivel_de_la_nota(): void
    {
        $concreta = $this->asignatura(3241, 'De Arte Sacra');
        $concreta->setActive(false);
        $hueco = $this->asignatura(2430, 'Op. I');
        $hueco->setActive(true);

        $nota = $this->createStub(PersonaNota::class);
        $nota->method('getIdNivelVo')->willReturn(new NivelId(2430));
        $nota->method('getId_asignatura')->willReturn(3241);
        $nota->method('getF_acta')->willReturn(null);
        $nota->method('getId_situacion')->willReturn(3);
        $nota->method('isAprobada')->willReturn(true);
        $nota->method('getNota_txt')->willReturn('probatus');
        $nota->method('getActaVo')->willReturn(null);

        $notaRepo = $this->createStub(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturn([$nota]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->willReturnCallback(
            static function (int $id, int|array|null $plan = null) use ($concreta, $hueco): ?Asignatura {
                if ($id === 3241 && $plan === Tesera::PLAN_NUEVO) {
                    return null;
                }
                if ($id === 3241) {
                    return $concreta;
                }
                if ($id === 2430 && $plan === Tesera::PLAN_NUEVO) {
                    return $hueco;
                }

                return null;
            }
        );

        $tesera = new Tesera(
            new ExpedienteNotasPersona($notaRepo),
            $asigRepo,
            new PlanEstudiosDePersona($notaRepo),
        );

        $aprobadas = $tesera->getAsignaturasAprobadas(100517149, Tesera::PLAN_NUEVO);

        $this->assertArrayHasKey(2430, $aprobadas);
        $this->assertSame(3241, $aprobadas[2430]['id_asignatura']);
        $this->assertSame('De Arte Sacra', $aprobadas[2430]['nombre_asignatura']);
    }

    private function tesera(): Tesera
    {
        $notaRepo = $this->createStub(PersonaNotaRepositoryInterface::class);

        return new Tesera(
            new ExpedienteNotasPersona($notaRepo),
            $this->createStub(AsignaturaRepositoryInterface::class),
            new PlanEstudiosDePersona($notaRepo),
        );
    }

    private function asignatura(int $idNivel, string $nombre): Asignatura
    {
        $asignatura = new Asignatura();
        $asignatura->setId_asignatura($idNivel);
        $asignatura->setIdNivelVo(new NivelId($idNivel));
        $asignatura->setNombreAsignaturaVo(new AsignaturaName($nombre));

        return $asignatura;
    }
}
