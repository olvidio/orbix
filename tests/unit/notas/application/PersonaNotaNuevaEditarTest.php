<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\PersonaNotaEditar;
use src\notas\application\PersonaNotaNueva;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\application\support\LiberarHuecoNivelNota;
use src\notas\application\support\NivelCatalogoAsignaturaEnPlan;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\application\support\SiglaActaPermitida;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\value_objects\TipoActa;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;

/**
 * Orquestación de «nota nueva» y «editar nota» hasta justo antes de escribir (EditarPersonaNota, que construyen
 * con `new` y ya tiene sus tests): errores de entrada, acta firmada y reubicación del hueco de opcional.
 */
final class PersonaNotaNuevaEditarTest extends myTest
{
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private PersonaNotaDlRepositoryInterface&MockObject $notasDl;
    private ActaRepositoryInterface&MockObject $actas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PlanEstudiosDePersona $plan;
    private string $miDl = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturn([]);
        $this->notasDl = $this->createMock(PersonaNotaDlRepositoryInterface::class);
        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        // Toda asignatura obligatoria existe en el plan y su nivel es su propio id (lo que usa el parser).
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): \src\asignaturas\domain\entity\Asignatura {
            $a = $this->createMock(\src\asignaturas\domain\entity\Asignatura::class);
            $a->method('isActive')->willReturn(true);
            $a->method('getId_nivel')->willReturn($id);

            return $a;
        });
        $this->plan = new PlanEstudiosDePersona($this->notas);
        $this->miDl = ConfigGlobal::mi_delef();
    }

    private function parser(): PersonaNotaInputParser
    {
        $listas = $this->createMock(SiglaActaPermitida::class);
        $listas->method('siglaPermitidaEnActa')->willReturnCallback(fn (string $s): bool => $s === $this->miDl);

        return new PersonaNotaInputParser($this->asignaturas, $this->plan, $listas, new NivelCatalogoAsignaturaEnPlan($this->asignaturas, $this->plan));
    }

    private function editar(): PersonaNotaEditar
    {
        return new PersonaNotaEditar(
            $this->parser(), $this->notas,
            $this->createMock(DelegacionRepositoryInterface::class), $this->createMock(DbSchemaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class), $this->notasDl,
            $this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class),
            new ActaFirmadaPolicy($this->actas)
        );
    }

    private function nueva(?HuecoOpcionalDePlan $hueco = null): PersonaNotaNueva
    {
        return new PersonaNotaNueva(
            $this->parser(), $this->notas,
            $this->createMock(DelegacionRepositoryInterface::class), $this->createMock(DbSchemaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class), $this->notasDl,
            $this->createMock(MapaPrefijoActaEsquemaRepositoryInterface::class),
            new LiberarHuecoNivelNota($this->asignaturas, $this->plan),
            new ActaFirmadaPolicy($this->actas),
            $hueco ?? new HuecoOpcionalDePlan($this->asignaturas, $this->plan)
        );
    }

    /** @return array<string, mixed> */
    private function input(array $extra = []): array
    {
        return array_merge([
            'id_pau' => '9001', 'id_asignatura' => '1101', 'id_nivel' => '1101', 'tipo_acta' => (string) TipoActa::FORMATO_ACTA,
            'acta' => "{$this->miDl} 12/24", 'id_situacion' => '10', 'nota_num' => '7', 'nota_max' => '10',
        ], $extra);
    }

    private function actaFirmada(): void
    {
        $a = $this->createMock(Acta::class);
        $a->method('tienePdfFirmado')->willReturn(true);
        $this->actas->method('findById')->willReturn($a);
    }

    private function assertError(array $r, string $fragmento = ''): void
    {
        $this->assertSame(['error', 'mensaje', 'esquema'], array_keys($r));
        $this->assertNotSame('', $r['error']);
        $this->assertSame('', $r['mensaje']);
        $this->assertSame('', $r['esquema']);
        if ($fragmento !== '') {
            $this->assertStringContainsString($fragmento, $r['error']);
        }
    }

    // ---- errores de entrada (editar y nueva) ----------------------------------------------------------

    public function test_una_seleccion_no_valida_devuelve_error_en_lugar_de_excepcion(): void
    {
        $this->assertError($this->editar()->execute($this->input(['sel' => [[1, 2]]])));
        $this->assertError($this->nueva()->execute($this->input(['sel' => [[1, 2]]])));
    }

    public function test_un_numero_de_acta_con_formato_incorrecto_devuelve_error(): void
    {
        $in = $this->input(['acta' => '', 'acta_num' => '12']);

        $this->assertError($this->editar()->execute($in), 'num/aa');
        $this->assertError($this->nueva()->execute($in), 'num/aa');
    }

    public function test_un_acta_de_otra_delegacion_pide_el_formato_certificado(): void
    {
        $in = $this->input(['acta' => 'zzz 12/24']);

        $this->assertError($this->editar()->execute($in));
        $this->assertError($this->nueva()->execute($in));
    }

    // ---- acta firmada ------------------------------------------------------------------------------------

    public function test_con_el_acta_firmada_no_se_escribe_nada(): void
    {
        $this->actaFirmada();
        $this->notasDl->expects($this->never())->method('Guardar');
        $this->notas->expects($this->never())->method('Guardar');

        $this->assertError($this->editar()->execute($this->input()));
        $this->assertError($this->nueva()->execute($this->input()));
    }

    public function test_el_mensaje_de_acta_firmada_es_el_de_la_politica(): void
    {
        $this->actaFirmada();

        $esperado = (new ActaFirmadaPolicy($this->actas))->mensajeSiFirmada("{$this->miDl} 12/24");

        $this->assertSame($esperado, $this->editar()->execute($this->input())['error']);
        $this->assertSame($esperado, $this->nueva()->execute($this->input())['error']);
    }

    // ---- reubicación del hueco de opcional (solo en nueva) -----------------------------------------------------

    private function opcionalConcreta(): int
    {
        return HuecoOpcionalDePlan::UMBRAL_OPCIONAL + 33;
    }

    public function test_editar_no_reubica_aunque_sea_una_opcional_concreta(): void
    {
        $this->actaFirmada();
        $this->notas->expects($this->never())->method('matching');
        $this->asignaturas->method('matching')->willReturn([]);

        $this->nueva()->execute($this->input(['id_asignatura' => (string) $this->opcionalConcreta(), 'mod' => 'editar']));
    }

    public function test_una_opcional_concreta_nueva_consulta_las_notas_de_la_persona(): void
    {
        $this->actaFirmada();
        $this->asignaturas->method('matching')->willReturn([]);
        $this->notas->expects($this->atLeastOnce())->method('matching');

        $this->nueva()->execute($this->input(['id_asignatura' => (string) $this->opcionalConcreta()]));
    }

    /** @return list<\src\notas\domain\entity\PersonaNota> notas que ocupan todos los niveles sobrantes 2435..2499 */
    private function notasConTodosLosSobrantesOcupados(): array
    {
        $lista = [];
        for ($nivel = HuecoOpcionalDePlan::SOBRA_DESDE; $nivel <= HuecoOpcionalDePlan::SOBRA_HASTA; $nivel++) {
            $n = $this->createMock(\src\notas\domain\entity\PersonaNota::class);
            $n->method('getId_asignatura')->willReturn(3000 + $nivel);
            $n->method('getIdNivelVo')->willReturn(\src\asignaturas\domain\value_objects\NivelId::fromNullableInt($nivel));
            $lista[] = $n;
        }

        return $lista;
    }

    public function test_si_no_queda_ningun_hueco_de_opcional_libre_devuelve_error(): void
    {
        $this->asignaturas->method('matching')->willReturn([]);
        $notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notas->method('matching')->willReturn($this->notasConTodosLosSobrantesOcupados());
        $this->notas = $notas;
        $this->plan = new PlanEstudiosDePersona($notas);

        $r = $this->nueva()->execute($this->input(['id_asignatura' => (string) $this->opcionalConcreta(), 'id_nivel' => '2435']));

        $this->assertError($r, 'hueco');
    }

    public function test_con_huecos_libres_la_opcional_se_reubica_y_el_flujo_sigue(): void
    {
        // Sin plazos en el plan, la opcional va al primer nivel sobrante libre y se llega a la comprobación del acta.
        $this->actaFirmada();
        $this->asignaturas->method('matching')->willReturn([]);

        $r = $this->nueva()->execute($this->input(['id_asignatura' => (string) $this->opcionalConcreta()]));

        $this->assertError($r);
        $this->assertStringNotContainsString('hueco', $r['error'], 'el error es el del acta firmada, no el de falta de hueco');
    }

    public function test_las_marcas_de_fin_de_ciclo_no_son_opcionales_concretas(): void
    {
        $this->assertFalse(HuecoOpcionalDePlan::esOpcionalConcreta(HuecoOpcionalDePlan::ID_FIN_CUADRIENIO));
        $this->assertFalse(HuecoOpcionalDePlan::esOpcionalConcreta(HuecoOpcionalDePlan::ID_FIN_BIENIO));
        $this->assertFalse(HuecoOpcionalDePlan::esOpcionalConcreta(HuecoOpcionalDePlan::UMBRAL_OPCIONAL));
        $this->assertTrue(HuecoOpcionalDePlan::esOpcionalConcreta(HuecoOpcionalDePlan::UMBRAL_OPCIONAL + 1));
        $this->assertFalse(HuecoOpcionalDePlan::esOpcionalConcreta(HuecoOpcionalDePlan::MAX_OPCIONAL));
    }
}
