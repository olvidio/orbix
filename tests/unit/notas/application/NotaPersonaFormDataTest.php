<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\NotaPersonaFormData;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\ActaPersonaFormListas;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Datos del formulario de alta / edición de una nota de persona. */
final class NotaPersonaFormDataTest extends myTest
{
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ProfesorStgrRepositoryInterface&MockObject $profesores;

    /** @var list<PersonaNota> */
    private array $notasPersona = [];
    /** @var list<PersonaNota> */
    private array $notaEditar = [];
    /** @var list<Asignatura> */
    private array $catalogo = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->profesores = $this->createMock(ProfesorStgrRepositoryInterface::class);
        $this->profesores->method('matching')->willReturn([]);
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $filtros = DescribeCriteria::filtros($c);
            foreach ($filtros as $f) {
                if ($f === 'id_asignatura eq 9998') {
                    return [];
                }
            }
            if (in_array('id_asignatura eq 1101', $filtros, true)) {
                return $this->notaEditar;
            }

            return $this->notasPersona;
        });
        $this->asignaturas->method('matching')->willReturnCallback(fn (Criteria $c): array => $this->catalogo);
    }

    private function uc(): NotaPersonaFormData
    {
        $plan = new PlanEstudiosDePersona($this->notas);

        return new NotaPersonaFormData(
            $this->notas, $this->asignaturas, $this->actividades, $this->profesores, $plan,
            new ActaPersonaFormListas($this->createMock(DelegacionRepositoryInterface::class)),
            new HuecoOpcionalDePlan($this->asignaturas, $plan)
        );
    }

    private function asig(int $idNivel, int $idAsignatura, string $corto): Asignatura&MockObject
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getId_nivel')->willReturn($idNivel);
        $a->method('getId_asignatura')->willReturn($idAsignatura);
        $a->method('getNombre_corto')->willReturn($corto);
        $a->method('isActive')->willReturn(true);

        return $a;
    }

    private function notaGuardada(): PersonaNota&MockObject
    {
        $n = $this->createMock(PersonaNota::class);
        $n->method('getId_asignatura')->willReturn(1101);
        $n->method('getIdNivelVo')->willReturn(NivelId::fromNullableInt(1101));
        $n->method('getF_acta')->willReturn(new DateTimeLocal('2024-06-30'));
        $n->method('getId_activ')->willReturn(3001);
        $n->method('getTipo_acta')->willReturn(TipoActa::FORMATO_ACTA);
        $n->method('getActa')->willReturn('dlb 12/24');
        $n->method('getId_situacion')->willReturn(NotaSituacion::NUMERICA);
        $n->method('getNota_num')->willReturn('8');
        $n->method('getNota_max')->willReturn('10');
        $n->method('isPreceptor')->willReturn(true);
        $n->method('getId_preceptor')->willReturn(10050);
        $n->method('getDetalle')->willReturn('repaso');

        return $n;
    }

    // ---- elección de modo ----------------------------------------------------------------------

    /**
     * @dataProvider entradasDeAltaProvider
     * @param array<string, mixed> $input
     */
    public function test_sin_asignatura_real_se_prepara_un_alta(array $input): void
    {
        $this->assertSame('nuevo', $this->uc()->execute($input + ['id_pau' => '10011'])['mod']);
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function entradasDeAltaProvider(): iterable
    {
        yield 'vacía' => [[]];
        yield 'mod nuevo aunque venga asignatura' => [['mod' => 'nuevo', 'id_asignatura_real' => '1101']];
        yield 'selección sin pau p' => [['sel' => ['1101#1101#1'], 'pau' => 'c']];
        yield 'mod editar sin asignatura' => [['mod' => 'editar']];
    }

    public function test_editar_sin_nota_guardada_lanza_excepcion(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->uc()->execute(['id_pau' => '10011', 'mod' => 'editar', 'id_asignatura_real' => '1101']);
    }

    public function test_editar_sin_la_asignatura_lanza_excepcion(): void
    {
        $this->notaEditar = [$this->notaGuardada()];
        $this->asignaturas->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->uc()->execute(['id_pau' => '10011', 'mod' => 'editar', 'id_asignatura_real' => '1101']);
    }

    // ---- edición ----------------------------------------------------------------------

    public function test_editar_devuelve_los_campos_de_la_nota_guardada(): void
    {
        $this->notaEditar = [$this->notaGuardada()];
        $this->asignaturas->method('findById')->willReturn($this->asig(1101, 1101, 'Fil'));
        $act = $this->createMock(ActividadAll::class);
        $act->method('getNom_activ')->willReturn('ca invierno');
        $this->actividades->method('findById')->with(3001)->willReturn($act);

        $r = $this->uc()->execute(['id_pau' => '10011', 'mod' => 'editar', 'id_asignatura_real' => '1101']);

        $this->assertSame('editar', $r['mod']);
        $this->assertSame('1101', $r['id_asignatura_real']);
        $this->assertSame('Fil', $r['nombre_corto']);
        $this->assertSame(1101, $r['id_nivel']);
        $this->assertSame(NotaSituacion::NUMERICA, $r['id_situacion']);
        $this->assertSame('30/6/2024', $r['f_acta']);
        $this->assertSame('2024-06-30', $r['f_acta_iso']);
        $this->assertTrue($r['preceptor']);
        $this->assertSame(10050, $r['id_preceptor']);
        $this->assertSame('repaso', $r['detalle']);
        $this->assertSame('8', $r['nota_num']);
        $this->assertSame('ca invierno', $r['nom_activ']);
        $this->assertSame('dlb', $r['acta_sigla_sel']);
        $this->assertSame('12/24', $r['acta_num']);
        $this->assertSame([], $r['asignaturas_faltan']);
        $this->assertSame([], $r['profesores']);
    }

    public function test_la_seleccion_de_la_lista_da_la_asignatura_a_editar(): void
    {
        $this->notaEditar = [$this->notaGuardada()];
        $this->asignaturas->method('findById')->willReturn($this->asig(1101, 1101, 'Fil'));

        $r = $this->uc()->execute(['id_pau' => '10011', 'pau' => 'p', 'sel' => ['1101#1101#1']]);

        $this->assertSame('editar', $r['mod']);
        $this->assertSame('1101', $r['id_asignatura_real']);
    }

    public function test_si_la_asignatura_no_esta_en_el_plan_se_busca_sin_plan(): void
    {
        $this->notaEditar = [$this->notaGuardada()];
        $this->asignaturas->method('findById')->willReturnCallback(
            fn (int $id, ?int $plan = null): ?Asignatura => $plan === null ? $this->asig(1101, 1101, 'Fil vieja') : null
        );

        $r = $this->uc()->execute(['id_pau' => '10011', 'mod' => 'editar', 'id_asignatura_real' => '1101']);

        $this->assertSame('Fil vieja', $r['nombre_corto']);
    }

    // ---- alta ----------------------------------------------------------------------

    public function test_el_alta_ofrece_las_pendientes_y_las_marcas_de_fin_de_ciclo(): void
    {
        $this->catalogo = [$this->asig(1101, 1101, 'Fil'), $this->asig(1102, 1102, 'Teo')];

        $r = $this->uc()->execute(['id_pau' => '10011']);

        $this->assertSame('', $r['id_asignatura_real']);
        $this->assertSame('Fil', $r['asignaturas_faltan'][1101]);
        $this->assertSame('Teo', $r['asignaturas_faltan'][1102]);
        $this->assertSame('---------', $r['asignaturas_faltan'][9997]);
        $this->assertArrayHasKey(9998, $r['asignaturas_faltan']);
        $this->assertArrayHasKey(9999, $r['asignaturas_faltan']);
        $this->assertSame([], $r['profesores']);
        $this->assertNotSame('', $r['mi_sigla'] . 'x');
    }

    public function test_el_alta_omite_las_asignaturas_con_nota_superada(): void
    {
        $this->catalogo = [$this->asig(1101, 1101, 'Fil'), $this->asig(1102, 1102, 'Teo')];
        $superada = $this->createMock(PersonaNota::class);
        $superada->method('getId_asignatura')->willReturn(1101);
        $superada->method('getIdNivelVo')->willReturn(NivelId::fromNullableInt(1101));
        $this->notasPersona = [$superada];
        $this->asignaturas->method('findById')->willReturn($this->asig(1101, 1101, 'Fil'));

        $faltan = $this->uc()->execute(['id_pau' => '10011'])['asignaturas_faltan'];

        $this->assertArrayNotHasKey(1101, $faltan);
        $this->assertArrayHasKey(1102, $faltan);
    }

    // ---- ayudas del formulario ----------------------------------------------------------------------

    public function test_el_pack_de_constantes_expone_las_situaciones_y_formatos(): void
    {
        $pack = NotaPersonaFormData::formNotasVoPack();

        $this->assertSame(NotaSituacion::SUPERADA, $pack['vo']['NotaSituacion']['SUPERADA']);
        $this->assertSame(TipoActa::FORMATO_CERTIFICADO, $pack['vo']['TipoActa']['FORMATO_CERTIFICADO']);
        $this->assertCount(14, $pack['vo']['NotaSituacion']);
        $this->assertStringStartsWith('"11"', $pack['lista_situacion_no_acta']);
        foreach (NotaSituacion::getArrayNoSuperadas() as $id) {
            $this->assertStringContainsString('"' . $id . '"', $pack['lista_situacion_no_acta']);
        }
        $this->assertSame(NotaSituacion::getArraySituacionTxt(), $pack['aOpcionesSituacion']);
    }

    public function test_las_opcionales_genericas_arman_la_condicion_js(): void
    {
        $this->catalogo = [$this->asig(1210, 1210, 'Op I'), $this->asig(1211, 1211, 'Op II')];

        $r = $this->uc()->opcionalesGenericasHelpers();

        $this->assertSame('id==1210 || id==1211', $r['condicion_js']);
        $this->assertJson($r['op_genericas_json']);
        $this->assertIsArray(json_decode($r['op_genericas_json'], true));
    }

    public function test_sin_genericas_ni_huecos_la_condicion_depende_solo_del_hueco_de_sobra(): void
    {
        $this->catalogo = [];

        $sinPersona = $this->uc()->opcionalesGenericasHelpers();
        $conPersona = $this->uc()->opcionalesGenericasHelpers(10011);

        $this->assertSame(['condicion_js' => '', 'op_genericas_json' => '[]'], $sinPersona);
        $this->assertSame(['condicion_js' => 'id==2435', 'op_genericas_json' => '["2435"]'], $conPersona);
    }
}
