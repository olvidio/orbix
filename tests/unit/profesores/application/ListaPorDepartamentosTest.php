<?php

declare(strict_types=1);

namespace Tests\unit\profesores\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\profesores\application\ListaPorDepartamentos;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\entity\ProfesorDirector;
use src\profesores\domain\entity\ProfesorTipo;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class ListaPorDepartamentosTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_rstgr_filtro_no_uno_devuelve_modo_filtro(): void
    {
        $this->stubOConfigAmbito('rstgr');
        $_SESSION['session_auth']['esquema'] = 'x-testregv';

        $repoDl = $this->createMock(DelegacionRepositoryInterface::class);
        $repoDl->expects($this->once())
            ->method('getArrayDlRegionStgr')
            ->with(['testreg'])
            ->willReturn(['d1' => 'Dl uno']);

        $GLOBALS['container'] = $this->containerFromMap([
            DelegacionRepositoryInterface::class => $repoDl,
        ]);

        $out = ListaPorDepartamentos::getData(['d1'], 2);

        $this->assertSame('filtro', $out['modo']);
        $this->assertTrue($out['rstgr']);
        $this->assertSame(['d1'], $out['a_checked']);
        $this->assertSame(['d1' => 'Dl uno'], $out['a_delegaciones']);
    }

    public function test_lista_basica_con_director_y_sin_tipos(): void
    {
        $this->stubOConfigAmbito('dl');

        $oTipoRepo = $this->createMock(ProfesorTipoRepositoryInterface::class);
        $oTipoRepo->method('getProfesorTipos')->willReturn([]);

        $oDept = $this->createMock(Departamento::class);
        $oDept->method('getId_departamento')->willReturn(10);
        $oDept->method('getDepartamento')->willReturn('Mates');

        $oDeptRepo = $this->createMock(DepartamentoRepositoryInterface::class);
        $oDeptRepo->method('getDepartamentos')->willReturn([$oDept]);

        $oDir = $this->createMock(ProfesorDirector::class);
        $oDir->method('getId_nom')->willReturn(100);

        $oDirRepo = $this->createMock(ProfesorDirectorRepositoryInterface::class);
        $oDirRepo->method('getProfesoresDirectores')->willReturn([$oDir]);

        $oPersona = $this->createMock(PersonaDl::class);
        $oPersona->method('getSituacion')->willReturn('A');
        $oPersona->method('getDl')->willReturn('dlz');
        $oPersona->method('getApellido1')->willReturn('Zapata');
        $oPersona->method('getApellido2')->willReturn('');
        $oPersona->method('getNom')->willReturn('Pedro');
        $oPersona->method('getPrefApellidosNombre')->willReturn('Zapata, Pedro');
        $oPersona->method('getCentro_o_dl')->willReturn('Centro');

        $oPersonaRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $oPersonaRepo->method('findById')->with(100)->willReturn($oPersona);

        $oStgrRepo = $this->createMock(ProfesorStgrRepositoryInterface::class);

        $GLOBALS['container'] = $this->containerFromMap([
            ProfesorTipoRepositoryInterface::class => $oTipoRepo,
            DepartamentoRepositoryInterface::class => $oDeptRepo,
            ProfesorDirectorRepositoryInterface::class => $oDirRepo,
            ProfesorStgrRepositoryInterface::class => $oStgrRepo,
            PersonaDlRepositoryInterface::class => $oPersonaRepo,
        ]);

        $out = ListaPorDepartamentos::getData([], 1);

        $this->assertSame('lista', $out['modo']);
        $this->assertFalse($out['rstgr']);
        $this->assertCount(1, $out['aClaustro']);
        $this->assertSame(10, $out['aClaustro'][0]['id_departamento']);
        $this->assertSame('Mates', $out['aClaustro'][0]['departamento']);
        $this->assertArrayHasKey('director', $out['aClaustro'][0]['profesores']);
        $this->assertNotEmpty($out['aClaustro'][0]['profesores']['director']);
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

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }

                return $this->services[$id];
            }
        };
    }
}
