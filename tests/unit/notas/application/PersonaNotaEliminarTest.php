<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\PersonaNotaEliminar;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\application\support\SiglaActaPermitida;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

final class PersonaNotaEliminarTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $input = [
        'id_pau' => 9,
        'id_asignatura' => 1002,
        'id_nivel' => 2100,
        'tipo_acta' => TipoActa::FORMATO_ACTA,
    ];

    public function test_examinado_sin_acta_se_borra(): void
    {
        $existente = $this->nota(NotaSituacion::EXAMINADO);
        $repo = $this->repoQueEncuentra($existente);
        $repo->expects($this->once())->method('Eliminar')->with($existente)->willReturn(true);

        $useCase = $this->useCase($repo, $this->policyFirmada('dlb 1/26'));
        $this->assertSame('', $useCase->execute($this->input));
    }

    public function test_examinado_con_acta_firmada_se_borra(): void
    {
        $existente = $this->nota(NotaSituacion::EXAMINADO, 'dlb 1/26');
        $repo = $this->repoQueEncuentra($existente);
        $repo->expects($this->once())->method('Eliminar')->with($existente)->willReturn(true);

        $useCase = $this->useCase($repo, $this->policyFirmada('dlb 1/26'));
        $this->assertSame('', $useCase->execute($this->input));
    }

    public function test_nota_numerica_con_acta_firmada_no_se_borra(): void
    {
        $existente = $this->nota(NotaSituacion::NUMERICA, 'dlb 1/26');
        $repo = $this->repoQueEncuentra($existente);
        $repo->expects($this->never())->method('Eliminar');

        $useCase = $this->useCase($repo, $this->policyFirmada('dlb 1/26'));
        $this->assertNotSame('', $useCase->execute($this->input));
    }

    private function nota(int $situacion, ?string $acta = null): PersonaNota
    {
        $nota = new PersonaNota();
        $nota->setId_nom(9);
        $nota->setId_nivel(2100);
        $nota->setId_asignatura(1002);
        $nota->setTipoActaVo(TipoActa::FORMATO_ACTA);
        $nota->setIdSituacionVo($situacion);
        if ($acta !== null) {
            $nota->setActaVo($acta);
        }

        return $nota;
    }

    private function repoQueEncuentra(PersonaNota $existente): PersonaNotaRepositoryInterface
    {
        $repo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repo->method('findById')->with(9, 2100, TipoActa::FORMATO_ACTA)->willReturn($existente);

        return $repo;
    }

    private function policyFirmada(string $actaId): ActaFirmadaPolicy
    {
        $acta = new Acta();
        $acta->setActa($actaId);
        $acta->setPdf('%PDF-fake');
        $repo = $this->createMock(ActaRepositoryInterface::class);
        $repo->method('findById')->with($actaId)->willReturn($acta);

        return new ActaFirmadaPolicy($repo);
    }

    private function useCase(
        PersonaNotaRepositoryInterface $repo,
        ActaFirmadaPolicy $policy,
    ): PersonaNotaEliminar {
        $planRepo = $this->createMock(PersonaNotaRepositoryInterface::class);
        $planRepo->method('getPersonaNotas')->willReturn([]);

        $parser = new PersonaNotaInputParser(
            $this->createMock(AsignaturaRepositoryInterface::class),
            new PlanEstudiosDePersona($planRepo),
            $this->createMock(SiglaActaPermitida::class),
        );

        return new PersonaNotaEliminar(
            $parser,
            $repo,
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(DbSchemaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
            $this->createMock(PersonaNotaDlRepositoryInterface::class),
            $policy,
        );
    }
}
