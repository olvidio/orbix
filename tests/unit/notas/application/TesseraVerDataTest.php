<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Tesera;
use src\notas\application\TesseraVerData;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaEx;
use src\ubis\domain\RegionStgrAviso;

final class TesseraVerDataTest extends TestCase
{
    /** @var mixed */
    private $prevConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prevConfig = $_SESSION['oConfig'] ?? null;
        $_SESSION['oConfig'] = new ConfigSnapshot(
            null, null, null, null, null, null, null, null, null, null, null, null, null,
            ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8],
            null,
        );
    }

    protected function tearDown(): void
    {
        if ($this->prevConfig === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->prevConfig;
        }
        parent::tearDown();
    }

    public function test_id_nom_cero_sigue_siendo_invalido(): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->never())->method('findPersonaEnGlobalIncluyendoNoActivos');

        $data = $this->useCase($finder)->execute(0);
        $this->assertSame(RegionStgrAviso::mensajePersonaNoValida(), $data['aviso']);
        $this->assertSame('', $data['ap_nom']);
    }

    public function test_asistente_de_paso_puede_ver_tessera(): void
    {
        $idNomPaso = -1001123;
        $persona = $this->createStub(PersonaEx::class);
        $persona->method('getId_schema')->willReturn(0);
        $persona->method('getPrefApellidosNombre')->willReturn('Pérez, Juan');
        $persona->method('getCentro_o_dl')->willReturn('resto');
        $persona->method('getDl')->willReturn('');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('findPersonaEnGlobalIncluyendoNoActivos')
            ->with($idNomPaso)
            ->willReturn($persona);

        $data = $this->useCase($finder)->execute($idNomPaso);
        $this->assertArrayNotHasKey('aviso', $data);
        $this->assertSame('Pérez, Juan', $data['ap_nom']);
        $this->assertSame('resto', $data['centro']);
        $this->assertIsArray($data['tabla']);
    }

    private function useCase(PersonaFinderService $finder): TesseraVerData
    {
        $notaRepo = $this->createStub(PersonaNotaRepositoryInterface::class);
        $notaRepo->method('getPersonaNotas')->willReturn([]);
        $asigRepo = $this->createStub(AsignaturaRepositoryInterface::class);
        $asigRepo->method('getAsignaturas')->willReturn([]);
        $tesera = new Tesera(
            new ExpedienteNotasPersona($notaRepo),
            $asigRepo,
            new PlanEstudiosDePersona($notaRepo),
        );

        return new TesseraVerData($tesera, $finder);
    }
}
