<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\NivelId;
use src\notas\application\AsignaturasPendientesResumenData;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\NotasDeUnaPersonaData;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Tesera;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Notas de una persona (dossier 1011) y resumen de asignaturas pendientes por tramo. */
final class NotasDeUnaPersonaDataTest extends myTest
{
    private MatriculaRepositoryInterface&MockObject $matriculas;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaNotaRepositoryInterface&MockObject $notas;

    /** @var list<PersonaNota> */
    private array $notasPersona = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c): array {
            foreach (DescribeCriteria::filtros($c) as $f) {
                if (str_starts_with($f, 'id_asignatura eq')) {
                    return [];
                }
            }

            return $this->notasPersona;
        });
    }

    private function asig(int $idNivel, int $idAsignatura, string $corto, bool $activa = true): Asignatura&MockObject
    {
        $a = $this->createMock(Asignatura::class);
        $a->method('getId_nivel')->willReturn($idNivel);
        $a->method('getId_asignatura')->willReturn($idAsignatura);
        $a->method('getNombre_corto')->willReturn($corto);
        $a->method('getCreditos')->willReturn(4.0);
        $a->method('getYear')->willReturn('1');
        $a->method('isActive')->willReturn($activa);

        return $a;
    }

    private function nota(int $idAsignatura, int $idNivel, int $idSituacion = NotaSituacion::SUPERADA, array $extra = []): PersonaNota&MockObject
    {
        $n = $this->createMock(PersonaNota::class);
        $n->method('getId_nom')->willReturn($extra['id_nom'] ?? 10011);
        $n->method('getId_asignatura')->willReturn($idAsignatura);
        $n->method('getIdNivelVo')->willReturn(NivelId::fromNullableInt($idNivel));
        $n->method('getId_situacion')->willReturn($idSituacion);
        $n->method('getActa')->willReturn($extra['acta'] ?? 'dlb 12/24');
        $n->method('getTipo_acta')->willReturn(1);
        $n->method('getNota_txt')->willReturn($extra['nota'] ?? '8');
        $n->method('getF_acta')->willReturn($extra['f_acta'] ?? null);
        $n->method('isPreceptor')->willReturn(false);
        $n->method('getId_preceptor')->willReturn(null);
        $n->method('getEpocaVo')->willReturn($extra['epoca'] ?? null);
        $n->method('getDetalle')->willReturn('det');
        $n->method('getId_activ')->willReturn($extra['id_activ'] ?? null);

        return $n;
    }

    private function datos(): NotasDeUnaPersonaData
    {
        return new NotasDeUnaPersonaData(
            $this->matriculas, $this->actividades, $this->asignaturas,
            new ExpedienteNotasPersona($this->notas), new PlanEstudiosDePersona($this->notas)
        );
    }

    // ---- NotasDeUnaPersonaData ----------------------------------------------------------------------

    public function test_sin_notas_ni_matriculas_la_tabla_esta_vacia_y_sin_aviso(): void
    {
        $this->matriculas->method('getMatriculasPendientes')->with(10011)->willReturn([]);

        $this->assertSame(['aValores' => [], 'aviso' => ''], $this->datos()->getTabla(10011, 3));
    }

    public function test_el_aviso_lista_las_matriculas_pendientes_y_salta_asignaturas_inexistentes(): void
    {
        $m1 = $this->createMock(Matricula::class);
        $m1->method('getId_activ')->willReturn(3001);
        $m1->method('getId_asignatura')->willReturn(1101);
        $m2 = $this->createMock(Matricula::class);
        $m2->method('getId_activ')->willReturn(3002);
        $m2->method('getId_asignatura')->willReturn(1102);
        $this->matriculas->method('getMatriculasPendientes')->willReturn([$m1, $m2]);
        $act = $this->createMock(ActividadAll::class);
        $act->method('getNom_activ')->willReturn('ca invierno');
        $this->actividades->method('findById')->willReturn($act);
        $this->asignaturas->method('findById')->willReturnMap([[1101, null, $this->asig(1101, 1101, 'Fil')], [1102, null, null]]);

        $aviso = $this->datos()->getTabla(10011, 3)['aviso'];

        $this->assertStringContainsString('<br>', $aviso);
        $this->assertStringContainsString('ca invierno', $aviso);
        $this->assertStringContainsString('Fil', $aviso);
        $this->assertSame(1, substr_count($aviso, 'ca invierno'));
    }

    public function test_cada_nota_es_una_fila_con_columnas_y_selector_solo_con_permiso_3(): void
    {
        $this->matriculas->method('getMatriculasPendientes')->willReturn([]);
        $this->notasPersona = [$this->nota(1101, 1101, NotaSituacion::SUPERADA, ['f_acta' => new DateTimeLocal('2024-06-30'), 'epoca' => new NotaEpoca(NotaEpoca::EPOCA_CA)])];
        $this->asignaturas->method('findById')->willReturn($this->asig(1101, 1101, 'Fil'));

        $con = $this->datos()->getTabla(10011, 3)['aValores'][1];
        $sin = $this->datos()->getTabla(10011, 1)['aValores'][1];

        $this->assertSame('1101#1101#1', $con['sel']);
        $this->assertArrayHasKey('ctx_eliminar', $con);
        $this->assertSame('', $sin['sel']);
        $this->assertArrayNotHasKey('ctx_eliminar', $sin);
        $this->assertSame(['Fil', '8', 'dlb 12/24', '30/6/2024', 'no'], [$con[1], $con[2], $con[3], $con[4], $con[5]]);
        $this->assertSame('det', $con[7]);
        $this->assertNotSame('', $con[6]);
    }

    public function test_el_nombre_de_actividad_sale_solo_si_la_nota_la_tiene(): void
    {
        $this->matriculas->method('getMatriculasPendientes')->willReturn([]);
        $this->notasPersona = [$this->nota(1101, 1101, NotaSituacion::SUPERADA, ['id_activ' => 3001]), $this->nota(1102, 1102)];
        $this->asignaturas->method('findById')->willReturn($this->asig(1101, 1101, 'Fil'));
        $act = $this->createMock(ActividadAll::class);
        $act->method('getNom_activ')->willReturn('ca invierno');
        $this->actividades->method('findById')->with(3001)->willReturn($act);

        $filas = $this->datos()->getTabla(10011, 1)['aValores'];

        $this->assertSame('ca invierno', $filas[1][8]);
        $this->assertSame('', $filas[2][8]);
    }

    public function test_una_nota_de_asignatura_inexistente_lanza_excepcion(): void
    {
        $this->matriculas->method('getMatriculasPendientes')->willReturn([]);
        $this->notasPersona = [$this->nota(1101, 1101)];
        $this->asignaturas->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->datos()->getTabla(10011, 1);
    }

    public function test_una_opcional_concreta_muestra_el_nombre_del_hueco_y_el_de_la_asignatura(): void
    {
        $this->matriculas->method('getMatriculasPendientes')->willReturn([]);
        $this->notasPersona = [$this->nota(3050, 2431)];
        $this->asignaturas->method('findById')->willReturn($this->asig(2431, 3050, 'Hist'));
        $this->asignaturas->method('matching')->willReturn([$this->asig(2431, 2431, 'Opt')]);

        $this->assertSame('Opt (Hist)', $this->datos()->getTabla(10011, 1)['aValores'][1][1]);
    }

    public function test_una_opcional_sin_hueco_en_el_plan_se_marca_de_sobra(): void
    {
        $this->matriculas->method('getMatriculasPendientes')->willReturn([]);
        $this->notasPersona = [$this->nota(3050, 2431)];
        $this->asignaturas->method('findById')->willReturn($this->asig(2431, 3050, 'Hist'));
        $this->asignaturas->method('matching')->willReturn([]);

        $this->assertSame('opcional de sobra', $this->datos()->getTabla(10011, 1)['aValores'][1][1]);
    }

    // ---- AsignaturasPendientesResumenData ----------------------------------------------------------------------

    private function persona(int $idNom, string $tabla, ?int $nivelStgr): PersonaDl&MockObject
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_nom')->willReturn($idNom);
        $p->method('getId_tabla')->willReturn($tabla);
        $p->method('getNivel_stgr')->willReturn($nivelStgr);

        return $p;
    }

    /**
     * @param list<PersonaDl> $personas
     */
    private function resumen(array $personas): AsignaturasPendientesResumenData
    {
        $personasRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $personasRepo->method('matching')->willReturn($personas);
        $tesera = new Tesera(new ExpedienteNotasPersona($this->notas), $this->asignaturas, new PlanEstudiosDePersona($this->notas));

        return new AsignaturasPendientesResumenData($this->asignaturas, $personasRepo, $this->notas, $tesera, new PlanEstudiosDePersona($this->notas));
    }

    public function test_el_resumen_cuenta_pendientes_por_tipo_y_nivel_stgr(): void
    {
        $catalogo = [$this->asig(1101, 11010, 'Fil'), $this->asig(1102, 11020, 'Teo')];
        $this->asignaturas->method('matching')->willReturn($catalogo);
        // Todas las personas comparten las notas del mock: la persona 10011 tiene aprobada Fil.
        $this->notasPersona = [$this->nota(11010, 1101)];
        $stgrB = \src\actividades\domain\value_objects\NivelStgrId::B;
        $stgrC1 = \src\actividades\domain\value_objects\NivelStgrId::C1;

        $r = $this->resumen([$this->persona(10011, 'n', $stgrB), $this->persona(10012, 'a', $stgrC1)])->execute();

        $this->assertSame([1101, 1102], array_keys($r['pendientes']));
        $this->assertSame(['nombre' => 'Fil', 'creditos' => 4.0, 'year' => '1'], $r['pendientes'][1101]['def']);
        // Fil está aprobada para ambas (las notas del mock son comunes): ninguna pendiente.
        $this->assertSame(0, $r['pendientes'][1101]['nb'] + $r['pendientes'][1101]['ac1']);
        // Teo pendiente: una numeraria de nivel b y una agregada de nivel c1.
        $this->assertSame(1, $r['pendientes'][1102]['nb']);
        $this->assertSame(1, $r['pendientes'][1102]['ac1']);
        $this->assertSame(0, $r['pendientes'][1102]['nc1']);
    }

    public function test_personas_sin_nivel_stgr_reconocido_se_ignoran(): void
    {
        $this->asignaturas->method('matching')->willReturn([$this->asig(1101, 11010, 'Fil')]);

        $r = $this->resumen([$this->persona(10011, 'n', null), $this->persona(10012, 'n', 99)])->execute();

        $this->assertSame(0, $r['pendientes'][1101]['nb'] + $r['pendientes'][1101]['nc1'] + $r['pendientes'][1101]['nc2']);
    }

    public function test_una_nota_de_asignatura_inactiva_no_aprueba_el_nivel(): void
    {
        $this->asignaturas->method('matching')->willReturn([$this->asig(1101, 11010, 'Fil', false)]);
        $this->notasPersona = [$this->nota(11010, 1101)];
        $stgrB = \src\actividades\domain\value_objects\NivelStgrId::B;

        $r = $this->resumen([$this->persona(10011, 'n', $stgrB)])->execute();

        $this->assertSame(1, $r['pendientes'][1101]['nb']);
    }
}
