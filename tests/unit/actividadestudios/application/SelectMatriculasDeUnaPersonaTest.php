<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\application\Select_matriculas_de_una_persona;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\security\HashB;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Widget 1303: plan de estudios de una persona (sus ca propios del curso y las asignaturas matriculadas en cada uno). */
final class SelectMatriculasDeUnaPersonaTest extends myTest
{
    private const ID_NOM = 10011;
    private const ID_ACTIV = 3001;

    private AsistenteActividadService&MockObject $asistencias;
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private MatriculaRepositoryInterface&MockObject $matriculas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private PersonaFinderService&MockObject $finder;
    private PersonaDlRepositoryInterface&MockObject $personasDl;
    private ActividadAsignaturaRepositoryInterface&MockObject $ofertas;
    private DbSchemaRepositoryInterface&MockObject $esquemas;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var array<int, object> personas que encuentra el buscador global */
    private array $personasGlobal = [];
    /** @var list<Asistente> */
    private array $asistenciasDevueltas = [];
    /** @var array<string, mixed> */
    private array $consulta = [];
    /** @var array<int, int> id_activ => id_tipo_activ */
    private array $tiposActividad = [];
    /** @var list<Matricula> */
    private array $matriculasDevueltas = [];
    /** @var list<array{int, int}> ofertas [id_asignatura, id_schema] */
    private array $ofertasDevueltas = [];
    /** @var list<string> */
    private array $filtrosMatriculas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->anterior['oConfig'] = $_SESSION['oConfig'] ?? null;
        $curso = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);

        $this->asistencias = $this->createMock(AsistenteActividadService::class);
        $this->asistencias->method('getActividadesDeAsistente')->willReturnCallback(
            function (Criteria $asistente, ?Criteria $actividad, bool $reverse): array {
                $this->consulta = [
                    'asistente' => DescribeCriteria::filtros($asistente),
                    'actividad' => DescribeCriteria::filtros($actividad),
                    'reverse' => $reverse,
                ];

                return $this->asistenciasDevueltas;
            }
        );
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->actividades->method('findById')->willReturnCallback(function (int $id): ?ActividadAll {
            if ($id === 3999) {
                return null;
            }
            $a = $this->createMock(ActividadAll::class);
            $a->method('getNom_activ')->willReturn('ca ' . $id);
            $a->method('getDl_org')->willReturn(ConfigGlobal::mi_delef());
            $a->method('getId_tipo_activ')->willReturn($this->tiposActividad[$id] ?? 112200);

            return $a;
        });
        $this->matriculas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->matriculas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosMatriculas = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->matriculasDevueltas;
        });
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            if ($id === 9999) {
                return null;
            }
            $a = $this->createMock(Asignatura::class);
            $a->method('getNombre_corto')->willReturn('Asig ' . $id);

            return $a;
        });
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (int $id): ?object => $this->personasGlobal[$id] ?? null);
        $this->personasDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->ofertas = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $this->ofertas->method('matching')->willReturnCallback(function (): array {
            $lista = [];
            foreach ($this->ofertasDevueltas as [$idAsignatura, $idSchema]) {
                $o = $this->createMock(ActividadAsignatura::class);
                $o->method('getId_asignatura')->willReturn($idAsignatura);
                $o->method('getId_schema')->willReturn($idSchema);
                $lista[] = $o;
            }

            return $lista;
        });
        $this->esquemas = $this->createMock(DbSchemaRepositoryInterface::class);
        $this->esquemas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $id = (int) $c->filters()[0]->value;
            $s = $this->createMock(DbSchema::class);
            $s->method('getId')->willReturn($id);
            $s->method('getSchema')->willReturn($id === 2002 ? 'H-dlxxv' : 'H-dlbv');

            return [$s];
        });
    }

    public function tearDown(): void
    {
        if ($this->anterior['oConfig'] === null) {
            unset($_SESSION['oConfig']);
        } else {
            $_SESSION['oConfig'] = $this->anterior['oConfig'];
        }
        parent::tearDown();
    }

    private function widget(int $idPau = self::ID_NOM): Select_matriculas_de_una_persona
    {
        $w = new Select_matriculas_de_una_persona(
            $this->asistencias,
            $this->actividades,
            $this->matriculas,
            $this->asignaturas,
            $this->finder,
            $this->personasDl,
            $this->ofertas,
            $this->esquemas,
        );
        $w->setId_pau((string) $idPau);
        $w->setPau('p');
        $w->setObj_pau('PersonaDl');

        return $w;
    }

    private function persona(?int $nivel = NivelStgrId::C1, ?string $dl = null, string $nombre = 'Pla, Joan'): PersonaDl&MockObject
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNivel_stgr')->willReturn($nivel);
        $p->method('getDl')->willReturn($dl ?? ConfigGlobal::mi_delef());
        $p->method('getPrefApellidosNombre')->willReturn($nombre);

        return $p;
    }

    /** El alumno es de mi dl: está en el repositorio de la dl y en el buscador global. */
    private function alumnoDeMiDl(?int $nivel = NivelStgrId::C1): void
    {
        $p = $this->persona($nivel);
        $this->personasDl->method('findById')->with(self::ID_NOM)->willReturn($p);
        $this->personasGlobal[self::ID_NOM] = $p;
    }

    private function asistencia(int $idActiv = self::ID_ACTIV, bool $propio = true, bool $estOk = false, ?string $observ = null): void
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_activ')->willReturn($idActiv);
        $a->method('isPropio')->willReturn($propio);
        $a->method('isEst_ok')->willReturn($estOk);
        $a->method('getObserv_est')->willReturn($observ);
        $this->asistenciasDevueltas[] = $a;
    }

    private function matricula(int $idAsignatura, ?bool $preceptor = null, ?int $idPreceptor = null, int $idSchema = 0): void
    {
        $m = $this->createMock(Matricula::class);
        $m->method('getId_asignatura')->willReturn($idAsignatura);
        $m->method('isPreceptor')->willReturn($preceptor);
        $m->method('getId_preceptor')->willReturn($idPreceptor);
        $m->method('getId_schema')->willReturn($idSchema);
        $this->matriculasDevueltas[] = $m;
    }

    /** @return array{string, string} inicio y fin del curso de estudios actual */
    private function curso(): array
    {
        $any = (int) date('m') > 9 ? (int) date('Y') + 1 : (int) date('Y');

        return [
            FuncTablasSupport::cursoEst('inicio', $any)->format('Y-m-d'),
            FuncTablasSupport::cursoEst('fin', $any)->format('Y-m-d'),
        ];
    }

    public function test_se_buscan_los_ca_propios_del_alumno_en_el_curso_actual(): void
    {
        $this->alumnoDeMiDl();
        [$ini, $fin] = $this->curso();

        $d = $this->widget()->getSegmentData();

        $this->assertSame(['id_nom eq ' . self::ID_NOM, 'propio eq true'], $this->consulta['asistente']);
        $this->assertSame("f_ini between [$ini,$fin]", $this->consulta['actividad'][1]);
        $this->assertTrue($this->consulta['reverse']);
        $this->assertSame('select_matriculas_de_una_persona', $d['segment_tipo']);
        $this->assertSame([], $d['cas']);
        $this->assertNotSame('', $d['empty_cas_message']);
    }

    /**
     * Hallazgo: el patrón de tipos de actividad es `^1(122)|(222)|(332)|(123)`: el `^` y el dígito de sección
     * solo afectan a la primera alternativa; las otras tres casan en cualquier posición del id (y de cualquier
     * sección).
     */
    public function test_el_patron_de_tipos_de_actividad_solo_ancla_la_primera_alternativa(): void
    {
        $this->alumnoDeMiDl();

        $this->widget()->getSegmentData();

        $this->assertSame('id_tipo_activ matches ^1(122)|(222)|(332)|(123)', $this->consulta['actividad'][0]);
    }

    public function test_sin_ningun_ca_este_curso_se_ofrece_el_formulario_para_verlos_todos(): void
    {
        $this->alumnoDeMiDl();
        [$ini, $fin] = $this->curso();

        $d = $this->widget()->getSegmentData();

        $form = $d['aviso_todos_form'];
        $this->assertSame('frontend/dossiers/controller/dossiers_ver.php', $form['dossiers_form_action']);
        $this->assertSame([
            'pau' => 'p',
            'id_pau' => self::ID_NOM,
            'obj_pau' => 'PersonaDl',
            'id_dossier' => 1303,
            'permiso' => '3',
            'que' => 'matriculas',
            'todos' => 1,
            'mod' => 'xx',
        ], $form['hash']['campos_hidden']);
        $this->assertStringContainsString($ini, $form['mensaje']);
        $this->assertStringContainsString($fin, $form['mensaje']);
        $this->assertSame([], $d['aviso_lines']);
    }

    public function test_con_todos_no_se_filtra_por_curso_y_si_no_hay_ninguno_se_avisa(): void
    {
        $this->alumnoDeMiDl();
        $w = $this->widget();
        $w->setTodos(1);

        $d = $w->getSegmentData();

        $this->assertCount(1, $this->consulta['actividad']);
        $this->assertNull($d['aviso_todos_form']);
        $this->assertCount(1, $d['aviso_lines']);
    }

    public function test_con_una_actividad_concreta_se_busca_solo_esa_asistencia_sea_o_no_propia(): void
    {
        $this->alumnoDeMiDl();
        $w = $this->widget();
        $w->setQId_activ('3001');

        $w->getSegmentData();

        $this->assertSame(['id_nom eq ' . self::ID_NOM, 'id_activ eq 3001'], $this->consulta['asistente']);
        $this->assertSame(['id_activ eq 3001'], $this->consulta['actividad']);
    }

    public function test_un_alumno_de_repaso_lleva_el_aviso(): void
    {
        $this->alumnoDeMiDl(NivelStgrId::R);
        $this->asistencia();

        $d = $this->widget()->getSegmentData();

        $this->assertCount(1, $d['aviso_lines']);
        $this->assertStringEndsWith('<br>', $d['aviso_lines'][0]);
    }

    public function test_plan_de_un_ca_con_sus_matriculas_y_los_tokens_de_cada_accion(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia(self::ID_ACTIV, true, true, 'convalida latín');
        $this->matricula(1101);
        $this->matricula(1102, true, 20001);
        $this->matricula(1103, true);
        $this->personasGlobal[20001] = $this->persona(null, null, 'Vidal, Pere');
        $w = $this->widget();
        $w->setQueSel('matriculas');

        $d = $w->getSegmentData();

        $this->assertSame(['id_nom eq ' . self::ID_NOM, 'id_activ eq ' . self::ID_ACTIV, 'id_nivel asc'], $this->filtrosMatriculas);
        $this->assertSame('', $d['empty_cas_message']);
        $this->assertNull($d['aviso_todos_form']);
        $this->assertCount(1, $d['cas']);
        $ca = $d['cas'][0];
        $this->assertSame('', $ca['html_prefix']);
        $this->assertSame('ca ' . self::ID_ACTIV, $ca['nom_activ']);
        $this->assertSame(['seleccionados1', 1], [$ca['form'], $ca['ca_num']]);
        $this->assertSame(['checked', ''], [$ca['chk_1'], $ca['chk_2']]);
        $this->assertSame('convalida latín', $ca['observ_est']);
        $this->assertSame(3, $ca['permiso']);

        $valores = $ca['tabla']['valores'];
        $this->assertSame([1, 2, 3], array_keys($valores));
        $this->assertSame('3001#1101#10011', $valores[1]['sel']);
        $this->assertSame('', $valores[1][1]);
        $this->assertSame('Asig 1101', $valores[1][2]);
        $this->assertSame('Vidal, Pere', $valores[2][1]);
        $this->assertNotSame('', $valores[3][1]);
        $this->assertNotSame('x', $valores[3][1]);
        $this->assertSame(
            ['sel' => '3001#1101#10011', 'pau' => 'p'],
            HashB::open($valores[1]['ctx_eliminar'], 'matricula_eliminar')
        );
        $this->assertSame('sql_13031', $ca['tabla']['id_tabla']);
        $this->assertCount(2, $ca['tabla']['botones']);
        $this->assertSame('fnjs_modificar(this.form,1)', $ca['tabla']['botones'][0]['click']);

        $ocultos = $ca['hash']['campos_hidden'];
        $this->assertSame(
            ['p', self::ID_NOM, self::ID_ACTIV, 'PersonaDl', 'matriculas', 1303, 3],
            [$ocultos['pau'], $ocultos['id_pau'], $ocultos['id_activ'], $ocultos['obj_pau'], $ocultos['queSel'], $ocultos['id_dossier'], $ocultos['permiso']]
        );
        $ids = ['id_activ' => self::ID_ACTIV, 'id_pau' => self::ID_NOM];
        $this->assertSame($ids, HashB::open($ocultos['ctx_observ_est'], 'asistente_observ_est'));
        $this->assertSame($ids, HashB::open($ocultos['ctx_plan_est_ok'], 'asistente_plan_est_ok'));
        $this->assertSame($ids, HashB::open($ocultos['ctx_observ'], 'asistente_observ'));
        $this->assertSame(['id_pau' => self::ID_NOM, 'id_activ' => self::ID_ACTIV], HashB::open($ocultos['ctx_auto'], 'matricula_automatica'));

        $query = ['mod' => 'nuevo', 'pau' => 'p', 'id_pau' => self::ID_NOM, 'id_activ' => self::ID_ACTIV];
        $this->assertSame(DossierTipoPublicUrls::formControllerLinkSpec(1303, $query), $ca['link_add_spec']);
        $this->assertSame($query + ['modo' => 'matricular_ca'], $ca['link_matricular_spec']['query']);
        $this->assertSame(DossierTipoPublicUrls::relativeFormController(1303), $d['wrapper']['url_form_relative']);
    }

    public function test_el_plan_sin_confirmar_marca_la_otra_casilla_y_el_asistente_no_propio_lleva_aviso(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia(self::ID_ACTIV, false, false);

        $ca = $this->widget()->getSegmentData()['cas'][0];

        $this->assertSame(['', 'checked'], [$ca['chk_1'], $ca['chk_2']]);
        $this->assertNotSame('', $ca['html_prefix']);
        $this->assertSame([], $ca['tabla']['valores']);
    }

    public function test_un_preceptor_que_no_se_encuentra_se_marca_con_x_y_se_avisa_en_la_cabecera(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia();
        $this->matricula(1101, true, 20009);

        $ca = $this->widget()->getSegmentData()['cas'][0];

        $this->assertSame('x', $ca['tabla']['valores'][1][1]);
        $this->assertStringContainsString('id_nom: 20009', $ca['html_prefix']);
    }

    public function test_la_seleccion_y_el_scroll_solo_se_anyaden_si_hay_matriculas(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia();
        $this->matricula(1101);
        $w = $this->widget();
        $w->setQid_sel('3001#1101#10011');
        $w->setQscroll_id(0);

        $valores = $w->getSegmentData()['cas'][0]['tabla']['valores'];

        $this->assertSame('3001#1101#10011', $valores['select']);
        $this->assertSame(0, $valores['scroll_id']);
    }

    public function test_la_asignatura_ofrecida_por_otra_dl_lleva_el_prefijo_de_esa_dl(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia();
        $this->ofertasDevueltas = [[1101, 2002], [1102, 1001]];
        $this->matricula(1101, null, null, 2002);
        $this->matricula(1102, null, null, 1001);

        $valores = $this->widget()->getSegmentData()['cas'][0]['tabla']['valores'];

        $this->assertSame('(dlxx) Asig 1101', $valores[1][2]);
        $this->assertSame('Asig 1102', $valores[2][2]);
    }

    public function test_un_alumno_de_otra_dl_tiene_permiso_2_y_no_puede_modificar_ni_borrar(): void
    {
        $this->personasGlobal[self::ID_NOM] = $this->persona(NivelStgrId::C1, 'otra');
        $this->personasDl->method('findById')->willReturn($this->persona());
        $this->asistencia();
        $this->matricula(1101);

        $ca = $this->widget()->getSegmentData()['cas'][0];

        $this->assertSame(2, $ca['permiso']);
        $this->assertSame([], $ca['tabla']['botones']);
    }

    public function test_un_alumno_que_no_es_de_mi_dl_ni_esta_en_ella_bloquea_el_plan(): void
    {
        $this->personasDl->method('findById')->willReturn(null);
        $this->personasGlobal[self::ID_NOM] = $this->persona(NivelStgrId::C1, 'otra');
        $this->asistencias->expects($this->never())->method('getActividadesDeAsistente');

        $d = $this->widget()->getSegmentData();

        $this->assertSame([], $d['cas']);
        $this->assertCount(1, $d['aviso_lines']);
        $this->assertSame('', $d['empty_cas_message']);
        $this->assertNull($d['aviso_todos_form']);
    }

    public function test_un_alumno_que_solo_esta_en_el_buscador_global_y_es_de_mi_dl_se_gestiona(): void
    {
        $this->personasDl->method('findById')->willReturn(null);
        $this->personasGlobal[self::ID_NOM] = $this->persona();
        $this->asistencia();

        $this->assertCount(1, $this->widget()->getSegmentData()['cas']);
    }

    public function test_una_persona_de_paso_se_busca_directamente_en_el_buscador_global(): void
    {
        $ex = new PersonaEx();
        $this->personasGlobal[-1005] = $ex;
        $this->personasDl->expects($this->never())->method('findById');
        $this->asistencia();

        $ca = $this->widget(-1005)->getSegmentData()['cas'][0];

        $this->assertSame(3, $ca['permiso']);
        $this->assertSame(-1005, $ca['hash']['campos_hidden']['id_pau']);
    }

    public function test_un_alumno_que_no_existe_lanza_excepcion(): void
    {
        $this->personasDl->method('findById')->willReturn(null);

        $this->expectException(\Exception::class);

        $this->widget()->getSegmentData();
    }

    public function test_un_ca_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia(3999);

        $this->expectException(\Exception::class);

        $this->widget()->getSegmentData();
    }

    public function test_una_matricula_de_una_asignatura_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia();
        $this->matricula(9999);

        $this->expectException(\Exception::class);

        $this->widget()->getSegmentData();
    }

    public function test_con_dos_ca_propios_se_avisa_salvo_que_el_segundo_sea_el_semestre_de_invierno(): void
    {
        $this->alumnoDeMiDl();
        $this->asistencia(3001);
        $this->asistencia(3002);

        $d = $this->widget()->getSegmentData();

        $this->assertCount(2, $d['cas']);
        $this->assertCount(1, $d['aviso_lines']);
        $this->assertStringContainsString('2', $d['aviso_lines'][0]);
        $this->assertSame(['seleccionados1', 'seleccionados2'], array_column($d['cas'], 'form'));

        $this->tiposActividad[3002] = 132500;
        $this->assertSame([], $this->widget()->getSegmentData()['aviso_lines']);
    }

    public function test_getters_y_setters_del_widget(): void
    {
        $w = $this->widget();
        $w->setId_dossier('1303');
        $w->setPermiso('2');
        $w->setStatus('x');
        $w->setBloque('b1303');

        $this->assertSame([1303, 2, 'x', 'p', 'PersonaDl', self::ID_NOM], [$w->getId_dossier(), $w->getPermiso(), $w->getStatus(), $w->getPau(), $w->getObj_pau(), $w->getId_pau()]);
        $this->assertSame([], $w->getBotones());
        $this->assertCount(2, $w->getCabeceras());
    }
}
