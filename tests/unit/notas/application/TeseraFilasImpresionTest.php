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
