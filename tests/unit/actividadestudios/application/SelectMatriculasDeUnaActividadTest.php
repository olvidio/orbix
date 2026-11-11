<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\Select_matriculas_de_una_actividad;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\unit\actividadestudios\application\support\SustituyeServiciosDelContenedor;
use Tests\unit\planning\support\DescribeCriteria;

/** Widget 3103: matrículas de una actividad agrupadas por asignatura, según la actividad sea o no de mi dl. */
final class SelectMatriculasDeUnaActividadTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ActividadAsignaturaDlRepositoryInterface&MockObject $asignaturasDl;
    private ActividadAsignaturaRepositoryInterface&MockObject $asignaturasTodas;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private MatriculaDlRepositoryInterface&MockObject $matriculasDl;
    private MatriculaRepositoryInterface&MockObject $matriculasTodas;
    /** @var array<int, string> */
    private array $personas = [];
    /** @var list<array{int, ?int}> [id_asignatura, id_profesor] */
    private array $asignaturasDelCa = [];
    /** @var array<int, list<int>> id_asignatura => ids matriculados */
    private array $matriculados = [];
    /** @var list<string> */
    private array $repos = [];
    /** @var array<string, list<string>> */
    private array $filtros = [];

    public function setUp(): void
    {
        parent::setUp();
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(function (int $id): ?PersonaDl {
            if (!isset($this->personas[$id])) {
                return null;
            }
            $p = $this->createMock(PersonaDl::class);
            $p->method('getPrefApellidosNombre')->willReturn($this->personas[$id]);
            $p->method('getCentro_o_dl')->willReturn('ctr' . $id);

            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $asignaturasDelCa = function (string $repo): \Closure {
            return function (Criteria $c) use ($repo): array {
                $this->repos[] = $repo;
                $this->filtros['asignaturas'] = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));
                $lista = [];
                foreach ($this->asignaturasDelCa as [$idAsignatura, $idProfesor]) {
                    $a = $this->createMock(ActividadAsignatura::class);
                    $a->method('getId_asignatura')->willReturn($idAsignatura);
                    $a->method('getId_profesor')->willReturn($idProfesor);
                    $lista[] = $a;
                }

                return $lista;
            };
        };
        $this->asignaturasDl = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $this->asignaturasDl->method('matching')->willReturnCallback($asignaturasDelCa('asignaturas dl'));
        $this->asignaturasTodas = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $this->asignaturasTodas->method('matching')->willReturnCallback($asignaturasDelCa('asignaturas todas'));
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            if ($id === 9999) {
                return null;
            }
            $a = $this->createMock(Asignatura::class);
            $a->method('getNombre_corto')->willReturn($id === 1109 ? null : 'Asig ' . $id);

            return $a;
        });
        $matriculas = function (string $repo): \Closure {
            return function (Criteria $c) use ($repo): array {
                $this->repos[] = $repo;
                $f = DescribeCriteria::filtros($c);
                $this->filtros['matriculas'] = $f;
                $lista = [];
                foreach ($this->matriculados[(int) substr($f[1], strlen('id_asignatura eq '))] ?? [] as $idNom) {
                    $m = $this->createMock(Matricula::class);
                    $m->method('getId_nom')->willReturn($idNom);
                    $lista[] = $m;
                }

                return $lista;
            };
        };
        $this->matriculasDl = $this->createMock(MatriculaDlRepositoryInterface::class);
        $this->matriculasDl->method('matching')->willReturnCallback($matriculas('matriculas dl'));
        $this->matriculasTodas = $this->createMock(MatriculaRepositoryInterface::class);
        $this->matriculasTodas->method('matching')->willReturnCallback($matriculas('matriculas todas'));
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        parent::tearDown();
    }

    private function widget(): Select_matriculas_de_una_actividad
    {
        $w = new Select_matriculas_de_una_actividad(
            $this->actividades,
            $this->asignaturasDl,
            $this->asignaturasTodas,
            $this->asignaturas,
            $this->matriculasDl,
            $this->matriculasTodas,
        );
        $w->setId_pau((string) self::ID_ACTIV);
        $w->setPau('a');
        $w->setObj_pau('Actividad');

        return $w;
    }

    private function actividad(bool $deMiDl = true): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('ca n verano');
        $a->method('getDl_org')->willReturn($deMiDl ? ConfigGlobal::mi_delef() : 'otra');
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($a);
    }

    public function test_actividad_no_encontrada_devuelve_el_mensaje_y_la_tabla_vacia(): void
    {
        $this->actividades->method('findById')->willReturn(null);

        $d = $this->widget()->getSegmentData();

        $this->assertSame('select_matriculas_de_una_actividad', $d['segment_tipo']);
        $this->assertNotSame('', $d['sin_asignaturas_mensaje']);
        $this->assertSame([], $d['tabla']['valores']);
        $this->assertSame([], $d['tabla']['grupos']);
        $this->assertSame('', $d['wrapper']['nom_activ']);
    }

    public function test_una_actividad_sin_asignaturas_lo_indica(): void
    {
        $this->actividad();

        $d = $this->widget()->getSegmentData();

        $this->assertNotSame('', $d['sin_asignaturas_mensaje']);
        $this->assertSame('ca n verano', $d['wrapper']['nom_activ']);
        $this->assertSame([], $d['tabla']['valores']);
    }

    public function test_en_una_actividad_de_mi_dl_se_ven_todas_las_matriculas_agrupadas_por_asignatura(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[1101, 20001], [1102, null], [1103, null]];
        $this->personas = [20001 => 'Vidal, Pere', 30001 => 'Zamora, Luis', 30002 => 'Bosch, Pau'];
        $this->matriculados = [1101 => [30001, 30002], 1102 => [30002]];

        $w = $this->widget();
        $d = $w->getSegmentData();

        $this->assertSame(['asignaturas dl', 'matriculas todas', 'matriculas todas', 'matriculas todas'], $this->repos);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'id_asignatura asc'], $this->filtros['asignaturas']);
        $this->assertSame(['id_activ eq ' . self::ID_ACTIV, 'id_asignatura eq 1103'], $this->filtros['matriculas']);
        $this->assertSame('', $d['sin_asignaturas_mensaje']);
        $this->assertSame('', $d['msg_err']);
        $this->assertSame([1101 => 'Asig 1101', 1102 => 'Asig 1102', 1103 => 'Asig 1103'], $d['tabla']['grupos']);
        $valores = $d['tabla']['valores'];
        $this->assertSame([1101, 1102], array_keys($valores));
        $fila = $valores[1101][0];
        $this->assertSame('30001#1101', $fila['sel']);
        $this->assertSame('Asig 1101', $fila[1]);
        $this->assertSame('Zamora, Luis (ctr30001)', $fila[2]);
        $this->assertSame(
            ['sel' => '30001#1101#' . self::ID_ACTIV, 'pau' => 'a'],
            HashB::open($fila['ctx_eliminar'], 'matricula_eliminar')
        );
        $this->assertSame('30002#1101', $valores[1101][1]['sel']);
        $this->assertSame('30002#1102', $valores[1102][0]['sel']);
        $this->assertSame(3, $w->getPermiso());
        $this->assertSame(3, $d['hash']['campos_hidden']['permiso']);
    }

    public function test_en_una_actividad_de_otra_dl_solo_se_ven_las_matriculas_de_mi_dl_y_el_permiso_es_de_lectura(): void
    {
        $this->actividad(false);
        $this->asignaturasDelCa = [[1101, null]];
        $this->personas = [30001 => 'Zamora, Luis'];
        $this->matriculados = [1101 => [30001]];

        $d = $this->widget()->getSegmentData();

        $this->assertSame(['asignaturas todas', 'matriculas dl'], $this->repos);
        $this->assertSame(1, $d['hash']['campos_hidden']['permiso']);
        $this->assertCount(1, $d['tabla']['valores'][1101]);
    }

    public function test_los_datos_del_segmento_llevan_los_campos_ocultos_y_las_rutas(): void
    {
        $this->actividad();
        $w = $this->widget();
        $w->setBloque('bloque_3103');
        $w->setQueSel('matriculas');

        $d = $w->getSegmentData();

        $this->assertSame([
            'pau' => 'a',
            'id_pau' => self::ID_ACTIV,
            'obj_pau' => 'Actividad',
            'queSel' => 'matriculas',
            'id_dossier' => 3103,
            'permiso' => 3,
            'bloque' => 'bloque_3103',
        ], $d['hash']['campos_hidden']);
        $this->assertSame('sel!mod!scroll_id!refresh!id_sel!ctx_eliminar', $d['hash']['campos_no']);
        $this->assertSame('bloque_3103', $d['wrapper']['bloque']);
        $this->assertSame(DossierTipoPublicUrls::relativeFormController(1303), $d['wrapper']['url_form_relative']);
        $this->assertSame('src/actividadestudios/matricula_eliminar', $d['wrapper']['url_matricula_eliminar_path']);
        $this->assertCount(2, $d['tabla']['cabeceras']);
        $this->assertSame('fnjs_borrar(this.form)', $d['tabla']['botones'][0]['click']);
    }

    public function test_la_seleccion_y_el_scroll_solo_se_anyaden_si_hay_filas(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[1101, null]];
        $this->personas = [30001 => 'Zamora, Luis'];
        $this->matriculados = [1101 => [30001]];
        $w = $this->widget();
        $w->setQid_sel('30001#1101');
        $w->setQscroll_id(4);

        $valores = $w->getSegmentData()['tabla']['valores'];

        $this->assertSame('30001#1101', $valores['select']);
        $this->assertSame(4, $valores['scroll_id']);

        $this->matriculados = [];
        $this->assertSame([], $w->getSegmentData()['tabla']['valores']);
    }

    public function test_un_alumno_o_un_profesor_que_no_se_encuentran_se_avisan_y_el_alumno_no_sale(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[1101, 20009]];
        $this->personas = [30001 => 'Zamora, Luis'];
        $this->matriculados = [1101 => [30009, 30001]];

        $d = $this->widget()->getSegmentData();

        $this->assertStringContainsString('id_nom: 20009 (profesor)', $d['msg_err']);
        $this->assertStringContainsString('id_nom: 30009', $d['msg_err']);
        $this->assertSame(['30001#1101'], array_column($d['tabla']['valores'][1101], 'sel'));
    }

    public function test_una_asignatura_sin_nombre_corto_da_un_grupo_sin_titulo(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[1109, null]];

        $this->assertSame([1109 => ''], $this->widget()->getSegmentData()['tabla']['grupos']);
    }

    /** Hallazgo: una asignatura del ca que ya no existe lanza excepción y tumba el dossier entero. */
    public function test_una_asignatura_que_ya_no_existe_lanza_excepcion(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[9999, null]];

        $this->expectException(\Exception::class);

        $this->widget()->getSegmentData();
    }

    public function test_get_valores_y_get_grupos_calculan_la_tabla_una_sola_vez(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[1101, null]];
        $this->personas = [30001 => 'Zamora, Luis'];
        $this->matriculados = [1101 => [30001]];
        $w = $this->widget();

        $valores = $w->getValores();
        $grupos = $w->getGrupos();

        $this->assertSame([1101 => 'Asig 1101'], $grupos);
        $this->assertSame('30001#1101', $valores[1101][0]['sel']);
        $this->assertSame(['asignaturas dl', 'matriculas todas'], $this->repos);
    }

    /**
     * Hallazgo: con asignaturas pero sin ninguna matrícula, `getValores()` queda vacío y cada llamada a
     * `getValores()` vuelve a hacer todas las consultas.
     */
    public function test_sin_matriculas_cada_llamada_a_get_valores_repite_las_consultas(): void
    {
        $this->actividad();
        $this->asignaturasDelCa = [[1101, null]];
        $w = $this->widget();

        $w->getValores();
        $w->getValores();

        $this->assertSame(['asignaturas dl', 'matriculas todas', 'asignaturas dl', 'matriculas todas'], $this->repos);
    }

    public function test_getters_y_setters_del_widget(): void
    {
        $w = $this->widget();
        $w->setId_dossier('3103');
        $w->setPermiso('2');
        $w->setPau(null);
        $w->setObj_pau(null);

        $this->assertSame([3103, 2, '', '', self::ID_ACTIV], [$w->getId_dossier(), $w->getPermiso(), $w->getPau(), $w->getObj_pau(), $w->getId_pau()]);
    }
}
