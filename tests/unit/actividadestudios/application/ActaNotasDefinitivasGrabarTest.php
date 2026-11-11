<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\ActaNotasDefinitivasGrabar;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\application\support\HuecoOpcionalDePlan;
use src\notas\application\support\LiberarHuecoNivelNota;
use src\notas\application\support\NivelCatalogoAsignaturaEnPlan;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;
use Tests\unit\actividadestudios\application\support\SustituyeServiciosDelContenedor;
use Tests\unit\planning\support\DescribeCriteria;

/**
 * Paso de las notas de un acta de ca al expediente (`PersonaNota`): qué matrículas se graban, con qué situación,
 * época y nivel, cuáles se saltan y cuándo se corta el proceso.
 *
 * `EditarPersonaNota` y las clases de soporte de notas (finales) son las reales, sobre repositorios simulados.
 */
final class ActaNotasDefinitivasGrabarTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;
    private const ID_ASIG = 1101;
    private const ACTA = 'dlb 12/26';
    private const TIPO_CA_N = 112025;
    private const TIPO_SEM_INV_AGD = 132500;

    private MatriculaRepositoryInterface&MockObject $matriculas;
    private ActaRepositoryInterface&MockObject $actas;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private ActividadAsignaturaDlRepositoryInterface&MockObject $actividadAsignaturas;
    private PersonaNotaDlRepositoryInterface&MockObject $notasDl;
    private DossierRepositoryInterface&MockObject $dossiers;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<Matricula> */
    private array $matriculasDevueltas = [];
    /** @var list<string> */
    private array $filtrosMatriculas = [];
    /** @var list<string> */
    private array $filtrosActas = [];
    /** @var list<Acta> actas de la asignatura en la actividad */
    private array $actasDelCa = [];
    /** @var array<string, Acta> */
    private array $actasPorNumero = [];
    /** @var array<int, list<PersonaNota>> notas previas de la asignatura, por alumno */
    private array $notasPrevias = [];
    /** @var list<PersonaNota> */
    private array $guardadas = [];
    /** @var list<PersonaNota> */
    private array $eliminadas = [];
    /** @var array<int, string> alumnos que encuentra el buscador */
    private array $personas = [10011 => 'Joan Pla', 10012 => 'Pau Bosch'];

    public function setUp(): void
    {
        parent::setUp();
        $this->anterior['oConfig'] = $_SESSION['oConfig'] ?? null;
        $curso = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', '0.6', '10', null, 'es_ES.UTF-8', null, null, $curso, $curso);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            if (!isset($this->personas[$id])) {
                return null;
            }
            $p = $this->createMock(PersonaDl::class);
            $p->method('getNombreApellidos')->willReturn($this->personas[$id]);
            $p->method('getApellidosNombre')->willReturn($this->personas[$id]);
            $p->method('getDl')->willReturn('dlb');
            $p->method('getId_schema')->willReturn(ConfigGlobal::mi_id_schema());

            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->matriculas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosMatriculas = DescribeCriteria::filtros($c);

            return $this->matriculasDevueltas;
        });
        $this->actas = $this->createMock(ActaRepositoryInterface::class);
        $this->actas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosActas = DescribeCriteria::filtros($c);

            return $this->actasDelCa;
        });
        $this->actas->method('findById')->willReturnCallback(fn (string $acta): ?Acta => $this->actasPorNumero[$acta] ?? null);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            $a = $this->createMock(Asignatura::class);
            $a->method('isActive')->willReturn(true);
            $a->method('getId_nivel')->willReturn($id);

            return $a;
        });
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);

            return $this->notasPrevias[(int) substr($f[0], strlen('id_nom eq '))] ?? [];
        });
        $this->notas->method('findById')->willReturnCallback(
            fn (int $idNom): ?PersonaNota => $this->notasPrevias[$idNom][0] ?? null
        );
        $this->notas->method('Eliminar')->willReturnCallback(function (PersonaNota $n): bool {
            $this->eliminadas[] = $n;

            return true;
        });
        $this->actividadAsignaturas = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $this->notasDl = $this->createMock(PersonaNotaDlRepositoryInterface::class);
        $this->notasDl->method('Guardar')->willReturnCallback(function (PersonaNota $n): bool {
            $this->guardadas[] = $n;

            return true;
        });
        $this->dossiers = $this->createMock(DossierRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        if ($this->anterior['oConfig'] === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->anterior['oConfig'];
        }
        parent::tearDown();
    }

    private function uc(): ActaNotasDefinitivasGrabar
    {
        $plan = new PlanEstudiosDePersona($this->createMock(PersonaNotaRepositoryInterface::class));

        return new ActaNotasDefinitivasGrabar(
            $this->matriculas,
            $this->actas,
            $this->actividades,
            $this->asignaturas,
            $this->notas,
            $this->actividadAsignaturas,
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(DbSchemaRepositoryInterface::class),
            $this->dossiers,
            $this->notasDl,
            new LiberarHuecoNivelNota($this->asignaturas, $plan),
            new NivelCatalogoAsignaturaEnPlan($this->asignaturas, $plan),
            new HuecoOpcionalDePlan($this->asignaturas, $plan),
            new ActaFirmadaPolicy($this->actas),
        );
    }

    /** @return array{success: bool, mensaje: string} */
    private function grabar(int $idAsignatura = self::ID_ASIG): array
    {
        return $this->uc()->execute(['id_activ' => (string) self::ID_ACTIV, 'id_asignatura' => (string) $idAsignatura]);
    }

    private function actividad(int $tipo = self::TIPO_CA_N, ?string $dlOrg = null): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_tipo_activ')->willReturn($tipo);
        $a->method('getDl_org')->willReturn($dlOrg ?? ConfigGlobal::mi_delef());
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    /** El acta de la asignatura en el ca, con fecha (o sin ella) y firmada o no. */
    private function acta(string $numero = self::ACTA, ?string $fecha = '2026-07-24', bool $firmada = false): void
    {
        $a = new Acta();
        $a->setActa($numero);
        $a->setF_acta($fecha === null ? null : new DateTimeLocal($fecha));
        if ($firmada) {
            $a->setPdf('%PDF-firmado');
        }
        $this->actasDelCa[] = $a;
        $this->actasPorNumero[$numero] = $a;
    }

    private function matricula(
        int $idNom = 10011,
        ?float $nota = 8.0,
        ?int $situacion = NotaSituacion::NUMERICA,
        ?string $acta = self::ACTA,
        ?bool $preceptor = null,
        ?int $max = 10,
        int $idAsignatura = self::ID_ASIG,
    ): void {
        $m = new Matricula();
        $m->setId_activ(self::ID_ACTIV);
        $m->setId_asignatura($idAsignatura);
        $m->setId_nom($idNom);
        $m->setId_situacion($situacion);
        $m->setPreceptor($preceptor);
        $m->setNotaNumVo($nota);
        $m->setNotaMaxVo($max);
        $m->setActa($acta);
        $this->matriculasDevueltas[] = $m;
    }

    private function notaPrevia(int $idNom, int $idActiv, int $idAsignatura = self::ID_ASIG): PersonaNota
    {
        $n = new PersonaNota();
        $n->setId_nom($idNom);
        $n->setIdNivelVo($idAsignatura);
        $n->setIdAsignaturaVo($idAsignatura);
        $n->setTipoActaVo(TipoActa::FORMATO_ACTA);
        $n->setId_activ($idActiv);
        $this->notasPrevias[$idNom][] = $n;

        return $n;
    }

    public function test_un_acta_de_otra_dl_no_se_puede_modificar(): void
    {
        $this->actas->expects($this->never())->method('matching');

        $r = $this->uc()->execute(['id_activ' => self::ID_ACTIV, 'id_asignatura' => self::ID_ASIG, 'id_schema' => ConfigGlobal::mi_id_schema() + 1]);

        $this->assertFalse($r['success']);
        $this->assertNotSame('', $r['mensaje']);
    }

    public function test_actividad_no_encontrada(): void
    {
        $this->actividades->method('findById')->willReturn(null);
        $this->matriculas->expects($this->never())->method('matching');

        $this->assertFalse($this->grabar()['success']);
    }

    public function test_sin_matriculas_no_hay_nada_que_grabar_y_termina_bien(): void
    {
        $this->actividad();

        $r = $this->grabar();

        $this->assertSame(['success' => true, 'mensaje' => ''], $r);
        $this->assertSame([], $this->guardadas);
        $schema = ConfigGlobal::mi_id_schema();
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'id_asignatura eq ' . self::ID_ASIG, 'id_schema eq ' . $schema], $this->filtrosActas);
    }

    public function test_en_una_actividad_de_mi_dl_se_graban_las_matriculas_de_todas_las_dl(): void
    {
        $this->actividad();

        $this->grabar();

        $this->assertSame(['id_asignatura eq ' . self::ID_ASIG, 'id_activ eq ' . self::ID_ACTIV], $this->filtrosMatriculas);
    }

    public function test_en_una_actividad_de_otra_dl_solo_se_graban_las_matriculas_de_mi_dl(): void
    {
        $this->actividad(self::TIPO_CA_N, 'otra');

        $this->grabar();

        $this->assertSame(
            ['id_asignatura eq ' . self::ID_ASIG, 'id_activ eq ' . self::ID_ACTIV, 'id_schema eq ' . ConfigGlobal::mi_id_schema()],
            $this->filtrosMatriculas
        );
    }

    public function test_una_nota_numerica_con_acta_se_graba_en_el_expediente_con_todos_sus_datos(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula();
        $this->dossiers->expects($this->once())->method('Guardar');

        $r = $this->grabar();

        $this->assertSame(['success' => true, 'mensaje' => ''], $r);
        $this->assertCount(1, $this->guardadas);
        $n = $this->guardadas[0];
        $this->assertSame(10011, $n->getId_nom());
        $this->assertSame(self::ID_ASIG, $n->getId_asignatura());
        $this->assertSame(self::ID_ASIG, $n->getIdNivelVo()->value());
        $this->assertSame(NotaSituacion::NUMERICA, $n->getId_situacion());
        $this->assertSame(self::ACTA, $n->getActa());
        $this->assertSame('2026-07-24', $n->getF_acta()?->getIso());
        $this->assertSame(TipoActa::FORMATO_ACTA, $n->getTipo_acta());
        $this->assertFalse($n->isPreceptor());
        $this->assertNull($n->getId_preceptor());
        $this->assertSame(NotaEpoca::EPOCA_CA, $n->getEpocaVo()?->value());
        $this->assertSame(self::ID_ACTIV, $n->getId_activ());
        $this->assertSame(8.0, $n->getNotaNumVo()?->value());
        $this->assertSame(10, $n->getNotaMaxVo()?->value());
    }

    public function test_en_el_semestre_de_invierno_de_agd_la_epoca_es_invierno(): void
    {
        $this->actividad(self::TIPO_SEM_INV_AGD);
        $this->acta();
        $this->matricula();

        $this->grabar();

        $this->assertSame(NotaEpoca::EPOCA_INVIERNO, $this->guardadas[0]->getEpocaVo()?->value());
    }

    public function test_una_nota_por_debajo_del_corte_se_graba_como_examinado(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 5.0);

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertSame(NotaSituacion::EXAMINADO, $this->guardadas[0]->getId_situacion());
        $this->assertSame(5.0, $this->guardadas[0]->getNotaNumVo()?->value());
    }

    public function test_sin_nota_maxima_se_usa_la_de_la_configuracion(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, self::ACTA, null, null);

        $this->grabar();

        $this->assertSame(10, $this->guardadas[0]->getNotaMaxVo()?->value());
    }

    public function test_sin_situacion_pero_con_nota_y_acta_la_situacion_se_deduce_de_la_nota(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, null);

        $this->grabar();

        $this->assertSame(NotaSituacion::NUMERICA, $this->guardadas[0]->getId_situacion());
    }

    public function test_el_codigo_de_cursada_en_el_campo_acta_graba_la_asignatura_como_cursada(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, null, null, (string) NotaSituacion::CURSADA);

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertSame(NotaSituacion::CURSADA, $this->guardadas[0]->getId_situacion());
        $this->assertNull($this->guardadas[0]->getNotaNumVo());
    }

    public function test_una_matricula_sin_acta_ni_nota_no_graba_nada_y_borra_la_nota_que_hubiera_de_este_ca(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, null, null, null);
        $previa = $this->notaPrevia(10011, self::ID_ACTIV);

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertSame([], $this->guardadas);
        $this->assertSame([$previa], $this->eliminadas);
    }

    public function test_una_matricula_con_acta_pero_sin_nota_ni_situacion_tampoco_se_graba(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, null, null);

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertSame([], $this->guardadas);
        $this->assertSame([], $this->eliminadas);
    }

    public function test_si_ya_hay_nota_de_este_mismo_ca_se_edita_en_lugar_de_crearla(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 9.0);
        $this->notaPrevia(10011, self::ID_ACTIV);
        // En el alta se comprueba que no exista ya la nota; en la edición no se consulta.
        $this->notasDl->expects($this->never())->method('matching');

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertCount(1, $this->guardadas);
        $this->assertSame(9.0, $this->guardadas[0]->getNotaNumVo()?->value());
    }

    public function test_si_la_nota_ya_existe_de_otra_actividad_no_se_pisa_y_se_avisa_con_el_nombre(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula();
        $this->notaPrevia(10011, 2999);

        $r = $this->grabar();

        $this->assertFalse($r['success']);
        $this->assertStringContainsString('Joan Pla (dlb)', $r['mensaje']);
        $this->assertSame([], $this->guardadas);
    }

    public function test_si_el_alta_falla_porque_la_nota_ya_esta_en_la_dl_se_devuelve_el_error(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula();
        $existente = new PersonaNota();
        $existente->setId_nom(10011);
        $existente->setIdAsignaturaVo(self::ID_ASIG);
        $existente->setTipoActaVo(TipoActa::FORMATO_ACTA);
        $this->notasDl->method('matching')->willReturn([$existente]);

        $r = $this->grabar();

        $this->assertFalse($r['success']);
        $this->assertStringContainsString('10011', $r['mensaje']);
        $this->assertSame([], $this->guardadas);
    }

    public function test_una_matricula_con_nota_en_un_acta_firmada_no_se_toca(): void
    {
        $this->actividad();
        $this->acta(self::ACTA, '2026-07-24', true);
        $this->matricula();
        $this->matricula(10012, null, null);

        $r = $this->grabar();

        // La que tiene nota se salta; la que no la tiene sigue el proceso (y no graba nada: sin nota ni situación).
        $this->assertTrue($r['success']);
        $this->assertSame([], $this->guardadas);
    }

    /**
     * Hallazgo: una matrícula con situación «numérica» y acta pero sin nota se graba tal cual en el expediente
     * (nota numérica sin número), también en un acta ya firmada, porque el bloqueo solo mira si hay nota.
     */
    public function test_una_matricula_con_situacion_numerica_pero_sin_nota_se_graba_sin_numero_aunque_el_acta_este_firmada(): void
    {
        $this->actividad();
        $this->acta(self::ACTA, '2026-07-24', true);
        $this->matricula(10012, null);

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertSame(NotaSituacion::NUMERICA, $this->guardadas[0]->getId_situacion());
        $this->assertNull($this->guardadas[0]->getNotaNumVo());
    }

    public function test_una_situacion_que_exige_acta_sin_acta_corta_el_proceso(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, null);

        $r = $this->grabar();

        $this->assertFalse($r['success']);
        $this->assertNotSame('', $r['mensaje']);
        $this->assertSame([], $this->guardadas);
    }

    public function test_un_acta_que_no_existe_o_sin_fecha_corta_el_proceso(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, 'dlb 99/26');

        $this->assertFalse($this->grabar()['success']);

        $this->acta('dlb 98/26', null);
        $this->matriculasDevueltas = [];
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, 'dlb 98/26');

        $this->assertFalse($this->grabar()['success']);
        $this->assertSame([], $this->guardadas);
    }

    /**
     * Hallazgo: los errores de acta cortan con `return` en mitad del bucle y sin transacción: las notas de los
     * alumnos anteriores ya están grabadas aunque la respuesta sea de error (uno de los mensajes dice incluso
     * «No se ha guardado nada»).
     */
    public function test_un_error_de_acta_a_mitad_deja_grabadas_las_notas_anteriores(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011);
        $this->matricula(10012, 8.0, NotaSituacion::NUMERICA, null);

        $r = $this->grabar();

        $this->assertFalse($r['success']);
        $this->assertCount(1, $this->guardadas);
        $this->assertSame(10011, $this->guardadas[0]->getId_nom());
    }

    /**
     * Hallazgo: una matrícula sin situación (o cursada/examinada) toma la fecha de `$cActas[0]` sin comprobar que
     * exista: si la asignatura aún no tiene acta en el ca, el guardado acaba en un error fatal.
     */
    public function test_sin_acta_de_la_asignatura_en_el_ca_una_matricula_sin_situacion_revienta(): void
    {
        $this->actividad();
        $this->matricula(10011, null, null, null);

        $this->expectException(\Error::class);

        @$this->grabar();
    }

    public function test_con_preceptor_la_nota_lleva_el_profesor_de_la_asignatura_como_preceptor(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, self::ACTA, true);
        $aa = new ActividadAsignatura();
        $aa->setId_profesor(20001);
        $this->actividadAsignaturas->expects($this->once())->method('findById')->with(self::ID_ACTIV, self::ID_ASIG)->willReturn($aa);

        $r = $this->grabar();

        $this->assertTrue($r['success']);
        $this->assertTrue($this->guardadas[0]->isPreceptor());
        $this->assertSame(20001, $this->guardadas[0]->getId_preceptor());
    }

    public function test_con_preceptor_una_nota_por_debajo_del_corte_no_se_graba_y_se_avisa_con_el_nombre(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 5.0, NotaSituacion::NUMERICA, self::ACTA, true);
        $this->matricula(10019, 5.0, NotaSituacion::NUMERICA, self::ACTA, true);

        $r = $this->grabar();

        $this->assertFalse($r['success']);
        $this->assertStringContainsString('Joan Pla', $r['mensaje']);
        $this->assertStringContainsString('id_nom: 10019', $r['mensaje']);
        $this->assertSame([], $this->guardadas);
    }

    public function test_con_preceptor_sin_acta_o_sin_la_asignatura_en_el_ca_se_corta_el_proceso(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, null, true);

        $this->assertFalse($this->grabar()['success']);

        $this->matriculasDevueltas = [];
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, self::ACTA, true);
        $this->actividadAsignaturas->method('findById')->willReturn(null);

        $this->assertFalse($this->grabar()['success']);
        $this->assertSame([], $this->guardadas);
    }

    public function test_el_marcador_de_fin_de_ciclo_usa_su_propio_id_como_nivel(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, self::ACTA, null, 10, HuecoOpcionalDePlan::ID_FIN_BIENIO);
        $this->asignaturas->expects($this->never())->method('findById');

        $r = $this->grabar(HuecoOpcionalDePlan::ID_FIN_BIENIO);

        $this->assertTrue($r['success']);
        $this->assertSame(HuecoOpcionalDePlan::ID_FIN_BIENIO, $this->guardadas[0]->getIdNivelVo()->value());
    }

    public function test_una_opcional_ocupa_el_primer_hueco_libre_de_opcionales_del_plan(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, self::ACTA, null, 10, 3105);
        $huecos = [];
        foreach ([2430, 2431] as $nivel) {
            $h = $this->createMock(Asignatura::class);
            $h->method('isActive')->willReturn(true);
            $h->method('getId_tipo')->willReturn(HuecoOpcionalDePlan::ID_TIPO_OPCIONAL);
            $h->method('getId_nivel')->willReturn($nivel);
            $h->method('getId_asignatura')->willReturn($nivel);
            $huecos[] = $h;
        }
        $this->asignaturas->method('matching')->willReturn($huecos);
        // El alumno ya tiene otra opcional en el primer hueco.
        $this->notaPrevia(10011, 2999, 3101)->setIdNivelVo(2430);
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notas->method('matching')->willReturnCallback(function (Criteria $c): array {
            return count(DescribeCriteria::filtros($c)) === 1 ? $this->notasPrevias[10011] : [];
        });

        $r = $this->grabar(3105);

        $this->assertTrue($r['success']);
        $this->assertSame(3105, $this->guardadas[0]->getId_asignatura());
        $this->assertSame(2431, $this->guardadas[0]->getIdNivelVo()->value());
    }

    /**
     * Sin huecos de opcionales en el catálogo del plan, la opcional va a los niveles «de sobra» (2435…2499). El
     * aviso «ha cursado una opcional que no tocaba» solo saldría con esos 65 niveles ocupados.
     */
    public function test_una_opcional_sin_huecos_en_el_plan_va_al_primer_nivel_de_sobra(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011, 8.0, NotaSituacion::NUMERICA, self::ACTA, null, 10, 3105);
        $this->asignaturas->method('matching')->willReturn([]);

        $r = $this->grabar(3105);

        $this->assertTrue($r['success']);
        $this->assertSame(HuecoOpcionalDePlan::SOBRA_DESDE, $this->guardadas[0]->getIdNivelVo()->value());
    }

    public function test_los_errores_de_varios_alumnos_se_acumulan_y_los_demas_se_graban(): void
    {
        $this->actividad();
        $this->acta();
        $this->matricula(10011);
        $this->matricula(10012);
        $this->notaPrevia(10011, 2999);

        $r = $this->grabar();

        $this->assertFalse($r['success']);
        $this->assertStringContainsString('Joan Pla', $r['mensaje']);
        $this->assertCount(1, $this->guardadas);
        $this->assertSame(10012, $this->guardadas[0]->getId_nom());
    }
}
