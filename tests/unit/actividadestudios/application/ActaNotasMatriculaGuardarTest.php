<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;

final class ActaNotasMatriculaGuardarTest extends TestCase
{
    public function test_omite_filas_de_acta_firmada(): void
    {
        $matricula = $this->createMock(Matricula::class);
        $matricula->method('getActa')->willReturn('dlb 1/26');
        $matricula->expects($this->never())->method('setNota_num');

        $repo = $this->createMock(MatriculaRepositoryInterface::class);
        $repo->method('findById')->willReturn($matricula);
        $repo->expects($this->never())->method('Guardar');

        $_SESSION['oConfig'] = new class {
            public function getNotaCorte(): float
            {
                return 0.6;
            }
        };

        $uc = new ActaNotasMatriculaGuardar($repo, $this->policyConActaFirmada('dlb 1/26'));
        $err = $uc->execute([
            'id_activ' => 1,
            'id_asignatura' => 1101,
            'id_nom' => [7],
            'nota_num' => ['9'],
            'nota_max' => ['10'],
            'form_preceptor' => [''],
            'acta_nota' => ['dlb 1/26'],
        ]);
        $this->assertSame('', $err);
    }

    public function test_rechaza_asignar_acta_firmada(): void
    {
        $matricula = $this->createMock(Matricula::class);
        $matricula->method('getActa')->willReturn('');

        $repo = $this->createMock(MatriculaRepositoryInterface::class);
        $repo->method('findById')->willReturn($matricula);

        $_SESSION['oConfig'] = new class {
            public function getNotaCorte(): float
            {
                return 0.6;
            }
        };

        $uc = new ActaNotasMatriculaGuardar($repo, $this->policyConActaFirmada('dlb 9/26'));
        $err = $uc->execute([
            'id_activ' => 1,
            'id_asignatura' => 1101,
            'id_nom' => [7],
            'nota_num' => ['9'],
            'nota_max' => ['10'],
            'form_preceptor' => [''],
            'acta_nota' => ['dlb 9/26'],
        ]);
        $this->assertNotSame('', $err);
    }

    private function policyConActaFirmada(string $actaFirmada): ActaFirmadaPolicy
    {
        $firmada = new Acta();
        $firmada->setActa($actaFirmada);
        $firmada->setPdf('%PDF-fake');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->willReturnCallback(
            static function (string $acta) use ($actaFirmada, $firmada): ?Acta {
                return $acta === $actaFirmada ? $firmada : null;
            }
        );

        return new ActaFirmadaPolicy($repo);
    }
}
