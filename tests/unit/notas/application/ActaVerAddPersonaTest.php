<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\ActaVerAddPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\application\support\LiberarHuecoNivelNota;
use src\notas\application\support\NivelCatalogoAsignaturaEnPlan;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/**
 * Alta de una nota desde el acta, hasta justo antes de escribir (EditarPersonaNota, ya probado):
 * validaciones de entrada, acta firmada o sin asignatura y resolución del nivel de la asignatura.
 */
final class ActaVerAddPersonaTest extends myTest
{
    private ActaRepositoryInterface&MockObject $actas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private mixed $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', 'C', '5', null, null, null, null, ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8], null);
        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturn([]);
    }

    public function tearDown(): void
    {
        if ($this->configAnterior === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->configAnterior;
        }
        parent::tearDown();
    }

    private function uc(): ActaVerAddPersona
    {
        $plan = new PlanEstudiosDePersona($this->notas);

        return new ActaVerAddPersona(
            $this->actas, $this->asignaturas, $this->notas,
            $this->createMock(DelegacionRepositoryInterface::class), $this->createMock(DbSchemaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class), $this->createMock(PersonaNotaDlRepositoryInterface::class),
            $this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class),
            new LiberarHuecoNivelNota($this->asignaturas, $plan),
            new NivelCatalogoAsignaturaEnPlan($this->asignaturas, $plan),
            new HuecoOpcionalDePlan($this->asignaturas, $plan)
        );
    }

    private function acta(?int $idAsignatura, bool $firmada = false): Acta
    {
        $a = $this->createMock(Acta::class);
        $a->method('tienePdfFirmado')->willReturn($firmada);
        $a->method('getId_asignatura')->willReturn($idAsignatura);
        $a->method('getId_activ')->willReturn(3001);
        $a->method('getF_acta')->willReturn(null);

        return $a;
    }

    /** @return array<string, mixed> */
    private function input(array $extra = []): array
    {
        return array_merge(['acta' => 'dlb 12/24', 'id_nom' => '9001', 'nota_num' => '7', 'nota_max' => '10'], $extra);
    }

    private function assertFallo(array $r, string $fragmento = ''): void
    {
        $this->assertSame(['success', 'mensaje'], array_keys($r));
        $this->assertFalse($r['success']);
        $this->assertNotSame('', $r['mensaje']);
        if ($fragmento !== '') {
            $this->assertStringContainsString($fragmento, $r['mensaje']);
        }
    }

    // ---- validaciones ----------------------------------------------------------------------------------

    /**
     * @dataProvider entradasIncompletasProvider
     * @param array<string, string> $cambio
     */
    public function test_sin_acta_o_sin_persona_no_se_hace_nada(array $cambio): void
    {
        $this->actas->expects($this->never())->method('findById');

        $this->assertFallo($this->uc()->execute($this->input($cambio)));
    }

    /**
     * @return iterable<string, array{array<string, string>}>
     */
    public static function entradasIncompletasProvider(): iterable
    {
        yield 'sin acta' => [['acta' => '']];
        yield 'sin persona' => [['id_nom' => '']];
        yield 'persona cero' => [['id_nom' => '0']];
        yield 'persona negativa' => [['id_nom' => '-5']];
    }

    public function test_un_acta_inexistente_avisa(): void
    {
        $this->actas->method('findById')->willReturn(null);

        $this->assertFallo($this->uc()->execute($this->input()));
    }

    public function test_un_acta_firmada_no_se_modifica(): void
    {
        $this->actas->method('findById')->willReturn($this->acta(1101, true));
        $this->notas->expects($this->never())->method('Guardar');

        $this->assertFallo($this->uc()->execute($this->input()));
    }

    /**
     * @dataProvider asignaturasAusentesProvider
     */
    public function test_un_acta_sin_asignatura_avisa(?int $idAsignatura): void
    {
        $this->actas->method('findById')->willReturn($this->acta($idAsignatura));

        $this->assertFallo($this->uc()->execute($this->input()));
    }

    /**
     * @return iterable<string, array{?int}>
     */
    public static function asignaturasAusentesProvider(): iterable
    {
        yield 'nula' => [null];
        yield 'cero' => [0];
    }

    // ---- resolución del nivel --------------------------------------------------------------------------------------

    public function test_una_opcional_concreta_sin_hueco_libre_avisa(): void
    {
        $this->actas->method('findById')->willReturn($this->acta(HuecoOpcionalDePlan::UMBRAL_OPCIONAL + 33));
        $existente = [];
        for ($nivel = HuecoOpcionalDePlan::SOBRA_DESDE; $nivel <= HuecoOpcionalDePlan::SOBRA_HASTA; $nivel++) {
            $n = $this->createMock(PersonaNota::class);
            $n->method('getId_asignatura')->willReturn(3000 + $nivel);
            $n->method('getIdNivelVo')->willReturn(\src\asignaturas\domain\value_objects\NivelId::fromNullableInt($nivel));
            $existente[] = $n;
        }
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturn($existente);
        $this->asignaturas->method('matching')->willReturn([]);

        $this->assertFallo($this->uc()->execute($this->input()), 'hueco');
    }

    public function test_una_asignatura_obligatoria_resuelve_su_nivel_con_el_catalogo_del_plan(): void
    {
        $this->actas->method('findById')->willReturn($this->acta(1101));
        $this->asignaturas->expects($this->atLeastOnce())->method('findById')->willReturn(null);

        // Sin la asignatura en el plan del alumno, la resolución del nivel lanza (comportamiento actual, ver nota).
        $this->expectException(\RuntimeException::class);
        $this->uc()->execute($this->input());
    }

    public function test_una_asignatura_fuera_de_catalogo_toma_el_nivel_de_la_asignatura(): void
    {
        $this->actas->method('findById')->willReturn($this->acta(9998));
        $asig = $this->createMock(Asignatura::class);
        $asig->method('getId_nivel')->willReturn(2999);
        $this->asignaturas->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_asignatura eq 9998'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([$asig]);

        // Llega a EditarPersonaNota con los mocks vacíos: el fallo posterior se devuelve como mensaje, no como excepción.
        $r = $this->uc()->execute($this->input());

        $this->assertArrayHasKey('success', $r);
        $this->assertArrayHasKey('mensaje', $r);
    }
}
