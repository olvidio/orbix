<?php

declare(strict_types=1);

namespace Tests\unit\profesores\application;

use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\services\TelecoPersonaService;
use src\profesores\application\ProfesoresAsignaturaLista;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\services\ProfesorAsignaturaService;

final class ProfesoresAsignaturaListaTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_un_profesor_departamento_sin_ampliacion(): void
    {
        $this->stubOConfigAmbito('dl');

        $asigSvc = $this->createMock(ProfesorAsignaturaService::class);
        $asigSvc->method('getArrayProfesoresAsignatura')->willReturn([
            'departamento' => [200 => 'Martínez, Luis'],
            'ampliacion' => [],
        ]);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getCentro_o_dl')->willReturn('Centro Sur');

        $oPersonaRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $oPersonaRepo->method('findById')->with(200)->willReturn($oPersona);

        $oDocRepo = $this->createMock(ProfesorDocenciaStgrRepositoryInterface::class);
        $oDocRepo->method('getProfesorDocenciasStgr')->willReturn([]);

        $oTelecoRepo = $this->createMock(TelecoPersonaDlRepositoryInterface::class);

        $telecoSvc = $this->createMock(TelecoPersonaService::class);
        $telecoSvc->method('getTelecosPorTipo')->willReturn('');

        $useCase = new ProfesoresAsignaturaLista(
            $asigSvc,
            $oPersonaRepo,
            $oDocRepo,
            $oTelecoRepo,
            $telecoSvc,
        );

        $out = $useCase->getTablaData(1001);

        $this->assertSame('list_profe_asig', $out['id_tabla']);
        $this->assertCount(1, $out['a_valores']);
        $this->assertSame('200', $out['a_valores'][1]['sel']);
        $this->assertSame('Martínez, Luis', $out['a_valores'][1][1]['valor']);
        $this->assertSame('Centro Sur', $out['a_valores'][1][2]);
        $this->assertSame('', $out['a_valores'][1][3]);
    }

    private function stubOConfigAmbito(string $ambito): void
    {
        $_SESSION['oConfig'] = new class ($ambito) {
            public function __construct(private readonly string $ambito) {}

            public function getAmbito(): string
            {
                return $this->ambito;
            }
        };
    }

}
