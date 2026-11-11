<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\ActividadAsignaturaEditar;
use src\actividadestudios\application\AsistentePlanEstOk;
use src\actividadestudios\application\DocenciaActualizar;
use src\actividadestudios\application\MatriculaEditar;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Ediciones de estudios en actividades: plan de estudios del asistente, asignatura, matrícula y docencia. */
final class EdicionesActividadEstudiosTest extends myTest
{
    private mixed $configAnterior;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $_SESSION['session_auth'] = ['sfsv' => 1];
    }

    public function tearDown(): void
    {
        foreach (['oConfig' => $this->configAnterior, 'session_auth' => $this->sesionAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    // ---- AsistentePlanEstOk ----------------------------------------------------------------------

    private function planEstOk(?Asistente $asistente, bool $guarda = true): AsistentePlanEstOk
    {
        $servicio = $this->createMock(AsistenteActividadService::class);
        $servicio->method('getRepoAsistente')->willReturn(AsistenteRepositoryInterface::class);
        $repo = $this->createMock(AsistenteRepositoryInterface::class);
        $repo->method('findById')->willReturn($asistente);
        $repo->method('Guardar')->willReturn($guarda);
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturn($repo);

        return new AsistentePlanEstOk($container, $servicio);
    }

    /**
     * @dataProvider planInvalidoProvider
     * @param array<string, mixed> $input
     */
    public function test_plan_est_ok_exige_actividad_y_persona(array $input): void
    {
        $this->assertNotSame('', $this->planEstOk(null)->execute($input));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function planInvalidoProvider(): iterable
    {
        yield 'vacío' => [[]];
        yield 'sin persona' => [['id_activ' => '3001']];
        yield 'sin actividad' => [['id_pau' => '10011']];
    }

    public function test_plan_est_ok_con_asistente_inexistente_avisa(): void
    {
        $this->assertNotSame('', $this->planEstOk(null)->execute(['id_activ' => '3001', 'id_pau' => '10011']));
    }

    /**
     * @dataProvider valoresEstOkProvider
     */
    public function test_plan_est_ok_marca_o_desmarca_y_acepta_id_nom_como_alternativa(string $valor, bool $esperado, string $claveId): void
    {
        $a = $this->createMock(Asistente::class);
        $a->expects($this->once())->method('setEst_ok')->with($esperado);

        $this->assertSame('', $this->planEstOk($a)->execute(['id_activ' => '3001', $claveId => '10011', 'est_ok' => $valor]));
    }

    /**
     * @return iterable<string, array{string, bool, string}>
     */
    public static function valoresEstOkProvider(): iterable
    {
        yield 'on con id_pau' => ['on', true, 'id_pau'];
        yield 'true con id_nom' => ['true', true, 'id_nom'];
        yield 'vacío desmarca' => ['', false, 'id_pau'];
        yield 'f desmarca' => ['f', false, 'id_nom'];
    }

    public function test_plan_est_ok_avisa_si_no_se_guarda(): void
    {
        $this->assertNotSame('', $this->planEstOk($this->createMock(Asistente::class), false)->execute(['id_activ' => '3001', 'id_pau' => '10011']));
    }

    // ---- ActividadAsignaturaEditar ----------------------------------------------------------------------

    public function test_editar_asignatura_valida_claves_y_existencia(): void
    {
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');
        $uc = new ActividadAsignaturaEditar($repo);

        $this->assertNotSame('', $uc->execute(['id_activ' => '3001']));
        $this->assertNotSame('', $uc->execute(['id_asignatura' => '1101']));
        $this->assertNotSame('', $uc->execute(['id_activ' => '3001', 'id_asignatura' => '1101']));
    }

    public function test_editar_asignatura_aplica_profesor_aviso_tipo_y_fechas(): void
    {
        $a = new ActividadAsignatura();
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->method('findById')->with(3001, 1101)->willReturn($a);
        $repo->expects($this->once())->method('Guardar')->willReturn(true);

        $r = (new ActividadAsignaturaEditar($repo))->execute(['id_activ' => '3001', 'id_asignatura' => '1101', 'id_profesor' => '10050', 'avis_profesor' => 'x', 'tipo' => 'v', 'f_ini' => '1/3/2026', 'f_fin' => 'basura']);

        $this->assertSame('', $r);
        $this->assertSame([10050, 'v'], [$a->getId_profesor(), $a->getTipo()]);
        $this->assertSame('1/3/2026', $a->getF_ini()?->getFromLocal());
        $this->assertNull($a->getF_fin(), 'una fecha inválida se guarda como vacía');
    }

    public function test_editar_asignatura_avisa_si_no_se_guarda(): void
    {
        $repo = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $repo->method('findById')->willReturn(new ActividadAsignatura());
        $repo->method('Guardar')->willReturn(false);

        $this->assertNotSame('', (new ActividadAsignaturaEditar($repo))->execute(['id_activ' => '3001', 'id_asignatura' => '1101']));
    }

    // ---- MatriculaEditar ----------------------------------------------------------------------

    public function test_editar_matricula_valida_claves_y_existencia(): void
    {
        $repo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');
        $uc = new MatriculaEditar($repo);

        $this->assertNotSame('', $uc->execute(['id_activ' => '3001', 'id_asignatura' => '1101']));
        $this->assertNotSame('', $uc->execute(['id_activ' => '3001', 'id_pau' => '10011']));
        $this->assertNotSame('', $uc->execute(['id_activ' => '3001', 'id_pau' => '10011', 'id_asignatura' => '1101']));
    }

    public function test_editar_matricula_aplica_nivel_situacion_y_preceptor_y_busca_por_actividad_asignatura_persona(): void
    {
        $m = $this->createMock(Matricula::class);
        $m->expects($this->once())->method('setId_nivel')->with(1102);
        $m->expects($this->once())->method('setId_situacion')->with(2);
        $m->expects($this->once())->method('setPreceptor')->with(true);
        $m->expects($this->once())->method('setId_preceptor')->with(10050);
        $repo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $repo->expects($this->once())->method('findById')->with(3001, 1101, 10011)->willReturn($m);
        $repo->method('Guardar')->willReturn(true);

        $r = (new MatriculaEditar($repo))->execute(['id_activ' => '3001', 'id_nom' => '10011', 'id_asignatura' => '1101', 'id_nivel' => '1102', 'id_situacion' => '2', 'preceptor' => 'on', 'id_preceptor' => '10050']);

        $this->assertSame('', $r);
    }

    public function test_editar_matricula_avisa_si_no_se_guarda(): void
    {
        $repo = $this->createMock(MatriculaDlRepositoryInterface::class);
        $repo->method('findById')->willReturn($this->createMock(Matricula::class));
        $repo->method('Guardar')->willReturn(false);

        $this->assertNotSame('', (new MatriculaEditar($repo))->execute(['id_activ' => '3001', 'id_pau' => '10011', 'id_asignatura' => '1101']));
    }

    // ---- DocenciaActualizar ----------------------------------------------------------------------

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ProfesorDocenciaStgrRepositoryInterface&MockObject $docencias;
    private ActaRepositoryInterface&MockObject $actas;
    private ActividadAsignaturaRepositoryInterface&MockObject $asignaturas;

    private function docencia(): DocenciaActualizar
    {
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->docencias = $this->createMock(ProfesorDocenciaStgrRepositoryInterface::class);
        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->asignaturas = $this->createMock(ActividadAsignaturaRepositoryInterface::class);

        return new DocenciaActualizar($this->actividades, $this->docencias, $this->actas, $this->asignaturas);
    }

    private function actividadTerminada(string $ini = '2026-02-10', int $tipo = 112001): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn(3001);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getF_ini')->willReturn(new DateTimeLocal($ini));

        return $a;
    }

    private function asignaturaConProfesor(?int $profesor = 10050, ?string $tipo = null): ActividadAsignatura
    {
        $a = new ActividadAsignatura();
        $a->setId_activ(3001);
        $a->setId_asignatura(1101);
        $a->setId_profesor($profesor);
        if ($tipo !== null) {
            $a->setTipo($tipo);
        }

        return $a;
    }

    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '01/09/2025', 'empiezamax' => '31/08/2026'];

    public function test_la_docencia_se_busca_en_actividades_terminadas_del_periodo_y_el_mensaje_nombra_el_curso(): void
    {
        $uc = $this->docencia();
        $filtros = [];
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros = DescribeCriteria::filtros($c);

            return [];
        });

        $r = $uc->execute(self::PERIODO);

        $this->assertStringContainsString('f_ini between', $filtros[0]);
        $this->assertSame('status eq ' . \src\actividades\domain\value_objects\StatusId::TERMINADA, $filtros[1]);
        $this->assertSame('id_tipo_activ matches ^1[123][23]', $filtros[2]);
        $this->assertNotSame('', $r);
    }

    public function test_una_docencia_nueva_se_crea_con_curso_de_inicio_tipo_ca_y_actas_unidas(): void
    {
        $uc = $this->docencia();
        $this->actividades->method('matching')->willReturn([$this->actividadTerminada('2026-02-10')]);
        $this->asignaturas->method('matching')->willReturn([$this->asignaturaConProfesor()]);
        $a1 = $this->createMock(Acta::class);
        $a1->method('getActa')->willReturn('dlb 1/26');
        $a2 = $this->createMock(Acta::class);
        $a2->method('getActa')->willReturn('dlb 2/26');
        $this->actas->method('matching')->willReturn([$a1, $a2]);
        $this->docencias->method('matching')->willReturn([]);
        $this->docencias->method('getNewId')->willReturn(900);
        $guardada = null;
        $this->docencias->expects($this->once())->method('Guardar')->willReturnCallback(function (ProfesorDocenciaStgr $d) use (&$guardada): bool {
            $guardada = $d;

            return true;
        });

        $uc->execute(self::PERIODO);

        $this->assertSame([900, 10050, 3001, 1101, 2025, 'v', 'dlb 1/26, dlb 2/26'], [
            $guardada->getId_item(), $guardada->getId_nom(), $guardada->getId_activ(), $guardada->getId_asignatura(),
            $guardada->getCurso_inicio(), $guardada->getTipo(), $guardada->getActa(),
        ]);
    }

    public function test_el_curso_de_inicio_es_el_anterior_si_la_actividad_empieza_antes_del_mes_de_inicio(): void
    {
        $uc = $this->docencia();
        $this->actividades->method('matching')->willReturn([$this->actividadTerminada('2026-10-10')]);
        $this->asignaturas->method('matching')->willReturn([$this->asignaturaConProfesor()]);
        $this->actas->method('matching')->willReturn([]);
        $this->docencias->method('matching')->willReturn([]);
        $this->docencias->method('getNewId')->willReturn(1);
        $curso = null;
        $this->docencias->method('Guardar')->willReturnCallback(function (ProfesorDocenciaStgr $d) use (&$curso): bool {
            $curso = $d->getCurso_inicio();

            return true;
        });

        $uc->execute(self::PERIODO);

        $this->assertSame(2026, $curso, 'octubre de 2026 ya es del curso que empieza en septiembre de 2026');
    }

    public function test_una_docencia_existente_se_actualiza_y_no_se_crea_otra(): void
    {
        $uc = $this->docencia();
        $this->actividades->method('matching')->willReturn([$this->actividadTerminada()]);
        $this->asignaturas->method('matching')->willReturn([$this->asignaturaConProfesor(10050, 'i')]);
        $this->actas->method('matching')->willReturn([]);
        $existente = $this->createMock(ProfesorDocenciaStgr::class);
        $existente->expects($this->once())->method('setTipo')->with('i');
        $existente->expects($this->once())->method('setActa')->with('');
        $this->docencias->method('matching')->willReturn([$existente]);
        $this->docencias->expects($this->never())->method('getNewId');
        $this->docencias->expects($this->once())->method('Guardar')->with($existente)->willReturn(true);

        $uc->execute(self::PERIODO);
    }

    public function test_las_asignaturas_sin_profesor_no_generan_docencia_ni_las_actividades_sin_fecha(): void
    {
        $uc = $this->docencia();
        $sinFecha = $this->createMock(ActividadAll::class);
        $sinFecha->method('getF_ini')->willReturn(null);
        $this->actividades->method('matching')->willReturn([$sinFecha, $this->actividadTerminada()]);
        $this->asignaturas->method('matching')->willReturn([$this->asignaturaConProfesor(null), $this->asignaturaConProfesor(0)]);
        $this->docencias->expects($this->never())->method('Guardar');

        $uc->execute(self::PERIODO);
    }

    public function test_una_asignatura_sin_tipo_en_una_actividad_de_invierno_se_graba_como_invierno(): void
    {
        $uc = $this->docencia();
        $this->actividades->method('matching')->willReturn([$this->actividadTerminada('2026-02-10', 132501)]);
        $this->asignaturas->method('matching')->willReturn([$this->asignaturaConProfesor()]);
        $this->actas->method('matching')->willReturn([]);
        $this->docencias->method('matching')->willReturn([]);
        $this->docencias->method('getNewId')->willReturn(1);
        $tipo = null;
        $this->docencias->method('Guardar')->willReturnCallback(function (ProfesorDocenciaStgr $d) use (&$tipo): bool {
            $tipo = $d->getTipo();

            return true;
        });

        $uc->execute(self::PERIODO);

        $this->assertSame('i', $tipo);
    }

    /**
     * Hallazgo: el resultado de `Guardar` no se comprueba; un fallo al grabar la docencia se pierde en silencio
     * y el mensaje final siempre dice que se ha actualizado.
     */
    public function test_un_fallo_de_guardado_no_se_nota_en_el_mensaje_final(): void
    {
        $uc = $this->docencia();
        $this->actividades->method('matching')->willReturn([$this->actividadTerminada()]);
        $this->asignaturas->method('matching')->willReturn([$this->asignaturaConProfesor()]);
        $this->actas->method('matching')->willReturn([]);
        $this->docencias->method('matching')->willReturn([]);
        $this->docencias->method('getNewId')->willReturn(1);
        $this->docencias->method('Guardar')->willReturn(false);

        $this->assertStringContainsString('2025', $uc->execute(self::PERIODO));
    }
}
