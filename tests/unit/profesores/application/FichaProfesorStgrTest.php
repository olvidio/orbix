<?php

declare(strict_types=1);

namespace Tests\unit\profesores\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\entity\Departamento;
use src\asignaturas\domain\value_objects\DepartamentoName;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\profesores\application\FichaProfesorStgr;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\domain\entity\ProfesorAmpliacion;
use src\profesores\domain\entity\ProfesorCongreso;
use src\profesores\domain\entity\ProfesorDirector;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\profesores\domain\entity\ProfesorJuramento;
use src\profesores\domain\entity\ProfesorLatin;
use src\profesores\domain\entity\ProfesorPublicacion;
use src\profesores\domain\entity\ProfesorStgr;
use src\profesores\domain\entity\ProfesorTipo;
use src\profesores\domain\entity\ProfesorTituloEst;
use src\profesores\domain\InfoProfesorLatin;
use src\profesores\domain\InfoProfesorPublicacion;
use src\profesores\domain\value_objects\CongresoTipo;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/**
 * Ficha de un profesor del stgr: cabecera, bloques de nombramientos, currículum,
 * ampliación, congresos, docencia, dirección, juramento y publicaciones, permisos
 * por dossier y enlaces.
 *
 * La persona se busca con `Persona::findPersonaEnGlobal()` (localizador estático):
 * el test sustituye el `PersonaFinderService` del contenedor y lo repone al acabar.
 */
final class FichaProfesorStgrTest extends myTest
{
    private const ID_NOM = 10011;

    private TipoDossierRepositoryInterface&MockObject $tiposDossier;
    private ProfesorLatinRepositoryInterface&MockObject $latin;
    private DepartamentoRepositoryInterface&MockObject $departamentos;
    private ProfesorTipoRepositoryInterface&MockObject $tiposProfesor;
    private ProfesorStgrRepositoryInterface&MockObject $nombramientos;
    private ProfesorTituloEstRepositoryInterface&MockObject $titulos;
    private ProfesorAmpliacionRepositoryInterface&MockObject $ampliaciones;
    private AsignaturaRepositoryInterface&MockObject $asignaturas;
    private ProfesorCongresoRepositoryInterface&MockObject $congresos;
    private ProfesorDocenciaStgrRepositoryInterface&MockObject $docencias;
    private ProfesorDirectorRepositoryInterface&MockObject $directores;
    private ProfesorJuramentoRepositoryInterface&MockObject $juramentos;
    private ProfesorPublicacionRepositoryInterface&MockObject $publicaciones;
    private CentroRepositoryInterface&MockObject $centros;
    private CentroDlRepositoryInterface&MockObject $centrosDl;
    private PersonaFinderService&MockObject $finder;
    private object $finderOriginal;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var array<string, array{list<string>, list<string>}> filtros y orden de cada consulta */
    private array $consultas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'iPermMenus'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $this->ambito('dl');
        $_SESSION['iPermMenus'] = 0;
        $this->consultas = [];

        $this->finderOriginal = $GLOBALS['container']->get(PersonaFinderService::class);
        $this->finder = $this->createMock(PersonaFinderService::class);
        $GLOBALS['container']->set(PersonaFinderService::class, $this->finder);

        $this->tiposDossier = $this->createMock(TipoDossierRepositoryInterface::class);
        $this->latin = $this->createMock(ProfesorLatinRepositoryInterface::class);
        $this->departamentos = $this->createMock(DepartamentoRepositoryInterface::class);
        $this->tiposProfesor = $this->createMock(ProfesorTipoRepositoryInterface::class);
        $this->nombramientos = $this->createMock(ProfesorStgrRepositoryInterface::class);
        $this->titulos = $this->createMock(ProfesorTituloEstRepositoryInterface::class);
        $this->ampliaciones = $this->createMock(ProfesorAmpliacionRepositoryInterface::class);
        $this->asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $this->congresos = $this->createMock(ProfesorCongresoRepositoryInterface::class);
        $this->docencias = $this->createMock(ProfesorDocenciaStgrRepositoryInterface::class);
        $this->directores = $this->createMock(ProfesorDirectorRepositoryInterface::class);
        $this->juramentos = $this->createMock(ProfesorJuramentoRepositoryInterface::class);
        $this->publicaciones = $this->createMock(ProfesorPublicacionRepositoryInterface::class);
        $this->centros = $this->createMock(CentroRepositoryInterface::class);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);

        $this->departamentos->method('findById')->willReturnCallback(function (int $id): ?Departamento {
            if ($id === 99) {
                return null;
            }
            $d = $this->createMock(Departamento::class);
            $d->method('getNombreDepartamentoVo')->willReturn(new DepartamentoName('Departamento ' . $id));

            return $d;
        });
        $this->asignaturas->method('findById')->willReturnCallback(function (int $id): ?Asignatura {
            if ($id === 9999) {
                return null;
            }
            $a = $this->createMock(Asignatura::class);
            $a->method('getNombre_corto')->willReturn('Asig ' . $id);

            return $a;
        });
    }

    public function tearDown(): void
    {
        $GLOBALS['container']->set(PersonaFinderService::class, $this->finderOriginal);
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function ambito(string $ambito): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, $ambito, null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
    }

    private function sut(): FichaProfesorStgr
    {
        // Cada `matching` deja anotado su criterio; si el test no fijó filas, devuelve lista vacía.
        foreach ([
            'nombramientos' => $this->nombramientos,
            'titulos' => $this->titulos,
            'ampliaciones' => $this->ampliaciones,
            'congresos' => $this->congresos,
            'docencias' => $this->docencias,
            'directores' => $this->directores,
            'juramentos' => $this->juramentos,
            'publicaciones' => $this->publicaciones,
        ] as $nombre => $repo) {
            $repo->method('matching')->willReturnCallback(function (Criteria $c) use ($nombre): array {
                $this->consultas[$nombre] = [DescribeCriteria::filtros($c), DescribeCriteria::orden($c)];

                return $this->filas[$nombre] ?? [];
            });
        }

        return new FichaProfesorStgr(
            $this->tiposDossier,
            $this->latin,
            $this->departamentos,
            $this->tiposProfesor,
            $this->nombramientos,
            $this->titulos,
            $this->ampliaciones,
            $this->asignaturas,
            $this->congresos,
            $this->docencias,
            $this->directores,
            $this->juramentos,
            $this->publicaciones,
            $this->centros,
            $this->centrosDl,
        );
    }

    /** @var array<string, list<object>> filas que devuelve cada repositorio */
    private array $filas = [];

    private function hayPersona(?int $idCtr = 1003, bool $sacd = false): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNombreApellidosCrSin')->willReturn('Juan Pérez');
        $p->method('isSacd')->willReturn($sacd);
        $p->method('getId_ctr')->willReturn($idCtr);
        $p->method('getId_tabla')->willReturn('n');
        $this->finder->method('findPersonaEnGlobalODePaso')->with(self::ID_NOM)->willReturn($p);
    }

    private static function fecha(string $iso): DateTimeLocal
    {
        return new DateTimeLocal($iso);
    }

    private function nombramiento(int $idDep, ?string $cese, ?int $idTipo = 1): ProfesorStgr
    {
        $n = $this->createMock(ProfesorStgr::class);
        $n->method('getId_departamento')->willReturn($idDep);
        $n->method('getId_tipo_profesor')->willReturn($idTipo);
        $n->method('getF_nombramiento')->willReturn(self::fecha('2020-09-01'));
        $n->method('getEscrito_nombramiento')->willReturn('esc-n');
        $n->method('getF_cese')->willReturn($cese === null ? null : self::fecha($cese));
        $n->method('getEscrito_cese')->willReturn($cese === null ? null : 'esc-c');

        return $n;
    }

    // ---- persona -----------------------------------------------------------------------------------------

    public function test_si_no_se_encuentra_a_la_persona_devuelve_solo_el_error(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);
        $this->nombramientos->expects($this->never())->method('matching');

        $data = $this->sut()->getFichaData(self::ID_NOM, 'n');

        $this->assertSame(['error'], array_keys($data));
        $this->assertStringContainsString((string) self::ID_NOM, $data['error']);
    }

    public function test_la_cabecera_lleva_nombre_centro_y_las_marcas_de_la_persona(): void
    {
        $this->hayPersona(1003, sacd: true);
        $centro = $this->createMock(CentroDl::class);
        $centro->method('getNombre_ubi')->willReturn('Centro A');
        $this->centrosDl->expects($this->once())->method('findById')->with(1003)->willReturn($centro);
        $this->centros->expects($this->never())->method('findById');
        $l = $this->createMock(ProfesorLatin::class);
        $l->method('isLatin')->willReturn(true);
        $this->latin->method('findById')->with(self::ID_NOM)->willReturn($l);

        $data = $this->sut()->getFichaData(self::ID_NOM, 'n');

        $this->assertSame('', $data['error']);
        $this->assertSame('Juan Pérez', $data['nom_ap']);
        $this->assertSame('Centro A', $data['nombre_ubi']);
        $this->assertSame(['si', '', 'si', 'si'], [$data['num_txt'], $data['agd_txt'], $data['sacd_txt'], $data['latin_txt']]);
    }

    /**
     * @dataProvider tablasProvider
     */
    public function test_num_y_agd_dependen_de_la_tabla_de_la_persona(string $idTabla, string $num, string $agd): void
    {
        $this->hayPersona();

        $data = $this->sut()->getFichaData(self::ID_NOM, $idTabla);

        $this->assertSame([$num, $agd, '', ''], [$data['num_txt'], $data['agd_txt'], $data['sacd_txt'], $data['latin_txt']]);
    }

    /** @return array<string, array{string, string, string}> */
    public static function tablasProvider(): array
    {
        return ['n' => ['n', 'si', ''], 'agd' => ['a', '', 'si'], 's' => ['s', '', ''], 'vacío' => ['', '', '']];
    }

    public function test_en_ambito_rstgr_el_centro_se_busca_en_la_tabla_general(): void
    {
        $this->ambito('rstgr');
        $this->hayPersona(1003);
        $centro = $this->createMock(Centro::class);
        $centro->method('getNombre_ubi')->willReturn('Centro de otra dl');
        $this->centros->expects($this->once())->method('findById')->with(1003)->willReturn($centro);
        $this->centrosDl->expects($this->never())->method('findById');

        $this->assertSame('Centro de otra dl', $this->sut()->getFichaData(self::ID_NOM, 'n')['nombre_ubi']);
    }

    public function test_sin_centro_o_con_un_centro_que_no_existe_el_nombre_queda_vacio(): void
    {
        $this->hayPersona(null);
        $this->centrosDl->expects($this->never())->method('findById');

        $this->assertSame('', $this->sut()->getFichaData(self::ID_NOM, 'n')['nombre_ubi']);
    }

    // ---- consultas ---------------------------------------------------------------------------------------

    public function test_cada_bloque_se_pide_por_la_persona_y_con_su_orden(): void
    {
        $this->hayPersona();

        $this->sut()->getFichaData(self::ID_NOM, 'n');

        $porPersona = ['id_nom eq ' . self::ID_NOM];
        foreach (['nombramientos', 'titulos', 'ampliaciones', 'congresos', 'docencias', 'directores', 'juramentos', 'publicaciones'] as $bloque) {
            $this->assertSame($porPersona, $this->consultas[$bloque][0], $bloque);
        }
        $this->assertStringStartsWith('f_nombramiento ', $this->consultas['nombramientos'][1][0]);
        $this->assertStringStartsWith('year ', $this->consultas['titulos'][1][0]);
        $this->assertStringStartsWith('f_nombramiento ', $this->consultas['ampliaciones'][1][0]);
        $this->assertStringStartsWith('f_ini ', $this->consultas['congresos'][1][0]);
        $this->assertCount(2, $this->consultas['docencias'][1]);
        $this->assertStringStartsWith('curso_inicio ', $this->consultas['docencias'][1][0]);
        $this->assertStringStartsWith('id_asignatura ', $this->consultas['docencias'][1][1]);
        $this->assertStringStartsWith('f_nombramiento ', $this->consultas['directores'][1][0]);
        $this->assertSame([], $this->consultas['juramentos'][1]);
        $this->assertStringStartsWith('f_publicacion ', $this->consultas['publicaciones'][1][0]);
    }

    public function test_al_imprimir_solo_se_piden_nombramientos_y_ampliaciones_vigentes_y_se_omiten_tres_bloques(): void
    {
        $this->hayPersona();
        $this->filas['directores'] = [$this->createMock(ProfesorDirector::class)];

        $data = $this->sut()->getFichaData(self::ID_NOM, 'n', true);

        $this->assertCount(2, $this->consultas['nombramientos'][0]);
        $this->assertStringStartsWith('f_cese ', $this->consultas['nombramientos'][0][1]);
        $this->assertCount(2, $this->consultas['ampliaciones'][0]);
        $this->assertStringStartsWith('f_cese ', $this->consultas['ampliaciones'][0][1]);
        foreach (['directores', 'juramentos', 'publicaciones'] as $bloque) {
            $this->assertArrayNotHasKey($bloque, $this->consultas, $bloque . ' no se consulta al imprimir');
        }
        $this->assertSame([[], '', []], [$data['a_director'], $data['f_juramento'], $data['a_publicaciones']]);
        $this->assertTrue($data['use_print_phtml']);
    }

    // ---- bloques -----------------------------------------------------------------------------------------

    public function test_los_nombramientos_llevan_departamento_y_tipo_y_los_vigentes_forman_la_linea_de_departamentos(): void
    {
        $this->hayPersona();
        $tipo = $this->createMock(ProfesorTipo::class);
        $tipo->method('getTipo_profesor')->willReturn('ordinario');
        $this->tiposProfesor->method('findById')->with(1)->willReturn($tipo);
        $this->filas['nombramientos'] = [
            $this->nombramiento(3, null),
            $this->nombramiento(4, '2022-06-30'),
            $this->nombramiento(5, null, null),
            $this->nombramiento(99, null),
        ];

        $data = $this->sut()->getFichaData(self::ID_NOM, 'n');

        $this->assertSame([
            'departamento' => 'Departamento 3',
            'tipo_profesor' => 'ordinario',
            'f_nombramiento' => self::fecha('2020-09-01')->getFromLocal(),
            'escrito_nombramiento' => 'esc-n',
            'f_cese' => null,
            'escrito_cese' => null,
        ], $data['a_nombramientos'][0]);
        $this->assertSame(self::fecha('2022-06-30')->getFromLocal(), $data['a_nombramientos'][1]['f_cese']);
        $this->assertSame('', $data['a_nombramientos'][2]['tipo_profesor'], 'sin tipo de profesor');
        $this->assertSame('', $data['a_nombramientos'][3]['departamento'], 'departamento que ya no existe');
        // Hallazgo: el departamento inexistente deja un separador colgando al final.
        $this->assertSame('Departamento 3; Departamento 5; ', $data['dep'], 'solo los que no han cesado');
    }

    public function test_el_curriculum_lista_los_titulos(): void
    {
        $this->hayPersona();
        $t = $this->createMock(ProfesorTituloEst::class);
        $t->method('isEclesiastico')->willReturn(true);
        $t->method('getTitulo')->willReturn('Doctor en Teología');
        $t->method('getCentro_dnt')->willReturn('PUSC');
        $t->method('getYear')->willReturn(1999);
        $this->filas['titulos'] = [$t];

        $this->assertSame(
            [['eclesiastico' => true, 'titulo' => 'Doctor en Teología', 'centro_dnt' => 'PUSC', 'year' => 1999]],
            $this->sut()->getFichaData(self::ID_NOM, 'n')['a_curriculum']
        );
    }

    public function test_la_ampliacion_lleva_el_nombre_corto_de_la_asignatura(): void
    {
        $this->hayPersona();
        $a = $this->createMock(ProfesorAmpliacion::class);
        $a->method('getId_asignatura')->willReturn(1101);
        $a->method('getF_nombramiento')->willReturn(self::fecha('2021-01-15'));
        $a->method('getEscrito_nombramiento')->willReturn('esc');
        $this->filas['ampliaciones'] = [$a];

        $this->assertSame([[
            'nombre_corto' => 'Asig 1101',
            'f_nombramiento' => self::fecha('2021-01-15')->getFromLocal(),
            'escrito_nombramiento' => 'esc',
            'f_cese' => null,
            'escrito_cese' => null,
        ]], $this->sut()->getFichaData(self::ID_NOM, 'n')['a_ampliacion']);
    }

    public function test_una_ampliacion_de_una_asignatura_que_no_existe_lanza_excepcion(): void
    {
        $this->hayPersona();
        $a = $this->createMock(ProfesorAmpliacion::class);
        $a->method('getId_asignatura')->willReturn(9999);
        $this->filas['ampliaciones'] = [$a];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('9999');
        $this->sut()->getFichaData(self::ID_NOM, 'n');
    }

    public function test_los_congresos_traducen_el_tipo_y_dejan_vacio_el_desconocido(): void
    {
        $this->hayPersona();
        $tipos = CongresoTipo::getArrayTiposCongreso();
        $idTipo = array_key_first($tipos);
        $conocido = $this->createMock(ProfesorCongreso::class);
        $conocido->method('getTipo')->willReturn($idTipo);
        $conocido->method('getCongreso')->willReturn('Simposio');
        $conocido->method('getLugar')->willReturn('Roma');
        $conocido->method('getF_ini')->willReturn(self::fecha('2023-04-01'));
        $conocido->method('getF_fin')->willReturn(self::fecha('2023-04-03'));
        $conocido->method('getOrganiza')->willReturn('PUSC');
        $raro = $this->createMock(ProfesorCongreso::class);
        $raro->method('getTipo')->willReturn(987);
        $sinTipo = $this->createMock(ProfesorCongreso::class);
        $sinTipo->method('getTipo')->willReturn(null);
        $this->filas['congresos'] = [$conocido, $raro, $sinTipo];

        $congresos = $this->sut()->getFichaData(self::ID_NOM, 'n')['a_congresos'];

        $this->assertSame([
            'tipo' => $tipos[$idTipo],
            'congreso' => 'Simposio',
            'lugar' => 'Roma',
            'f_ini' => self::fecha('2023-04-01')->getFromLocal(),
            'f_fin' => self::fecha('2023-04-03')->getFromLocal(),
            'organiza' => 'PUSC',
        ], $congresos[0]);
        $this->assertSame(['', ''], [$congresos[1]['tipo'], $congresos[2]['tipo']]);
        $this->assertNull($congresos[1]['f_ini']);
    }

    public function test_la_docencia_lleva_el_curso_la_asignatura_y_el_modo(): void
    {
        $this->hayPersona();
        $tipos = TipoActividadAsignatura::getTiposActividad();
        $idTipo = (string) array_key_first($tipos);
        $d = $this->createMock(ProfesorDocenciaStgr::class);
        $d->method('getId_asignatura')->willReturn(1101);
        $d->method('getTipo')->willReturn($idTipo);
        $d->method('getCurso_inicio')->willReturn(2023);
        $d->method('getActa')->willReturn('dlb 12/24');
        $sinTipo = $this->createMock(ProfesorDocenciaStgr::class);
        $sinTipo->method('getId_asignatura')->willReturn(1102);
        $sinTipo->method('getTipo')->willReturn(null);
        $sinTipo->method('getCurso_inicio')->willReturn(2024);
        $this->filas['docencias'] = [$d, $sinTipo];

        $docencias = $this->sut()->getFichaData(self::ID_NOM, 'n')['a_docencias'];

        $this->assertSame(
            ['curso_inicio' => 2023, 'curso_fin' => 2024, 'nombre_corto' => 'Asig 1101', 'modo' => $tipos[$idTipo], 'acta' => 'dlb 12/24'],
            $docencias[0]
        );
        $this->assertSame(['', 2025], [$docencias[1]['modo'], $docencias[1]['curso_fin']]);
    }

    public function test_una_docencia_de_una_asignatura_que_no_existe_lanza_excepcion(): void
    {
        $this->hayPersona();
        $d = $this->createMock(ProfesorDocenciaStgr::class);
        $d->method('getId_asignatura')->willReturn(9999);
        $this->filas['docencias'] = [$d];

        $this->expectException(\RuntimeException::class);
        $this->sut()->getFichaData(self::ID_NOM, 'n');
    }

    public function test_direccion_juramento_y_publicaciones(): void
    {
        $this->hayPersona();
        $dir = $this->createMock(ProfesorDirector::class);
        $dir->method('getId_departamento')->willReturn(3);
        $dir->method('getF_nombramiento')->willReturn(self::fecha('2019-09-01'));
        $dir->method('getEscrito_nombramiento')->willReturn('esc-d');
        $this->filas['directores'] = [$dir];
        $jur = $this->createMock(ProfesorJuramento::class);
        $jur->method('getF_juramento')->willReturn(self::fecha('2018-10-02'));
        $otro = $this->createMock(ProfesorJuramento::class);
        $otro->method('getF_juramento')->willReturn(self::fecha('2001-01-01'));
        $this->filas['juramentos'] = [$jur, $otro];
        $pub = $this->createMock(ProfesorPublicacion::class);
        $pub->method('isPendiente')->willReturn(false);
        $pub->method('getTipo_publicacion')->willReturn('libro');
        $pub->method('getTitulo')->willReturn('Un título');
        $pub->method('getEditorial')->willReturn('Ed');
        $pub->method('getColeccion')->willReturn('Col');
        $pub->method('getF_publicacion')->willReturn(self::fecha('2017-05-05'));
        $pub->method('getReferencia')->willReturn('ref');
        $pub->method('getLugar')->willReturn('Madrid');
        $pub->method('getObserv')->willReturn('obs');
        $this->filas['publicaciones'] = [$pub];

        $data = $this->sut()->getFichaData(self::ID_NOM, 'n');

        $this->assertSame([[
            'departamento' => 'Departamento 3',
            'f_nombramiento' => self::fecha('2019-09-01')->getFromLocal(),
            'escrito_nombramiento' => 'esc-d',
            'f_cese' => null,
            'escrito_cese' => null,
        ]], $data['a_director']);
        $this->assertSame(self::fecha('2018-10-02')->getFromLocal(), $data['f_juramento'], 'el primero que devuelve el repositorio');
        $this->assertSame([[
            'pendiente' => false,
            'tipo_publicacion' => 'libro',
            'titulo' => 'Un título',
            'editorial' => 'Ed',
            'coleccion' => 'Col',
            'f_publicacion' => self::fecha('2017-05-05')->getFromLocal(),
            'referencia' => 'ref',
            'lugar' => 'Madrid',
            'observ' => 'obs',
        ]], $data['a_publicaciones']);
        $this->assertFalse($data['use_print_phtml']);
    }

    // ---- permisos por dossier ----------------------------------------------------------------------------

    public function test_el_permiso_de_cada_bloque_sale_de_su_tipo_de_dossier_y_de_los_bits_del_usuario(): void
    {
        $this->hayPersona();
        $_SESSION['iPermMenus'] = 16; // est
        $this->tiposDossier->method('findById')->willReturnCallback(function (int $id): ?TipoDossier {
            if ($id === 1025) {
                return null; // tipo de dossier sin dar de alta
            }
            $t = $this->createMock(TipoDossier::class);
            $t->method('getPermiso_lectura')->willReturn($id === 1012 ? 128 : 16);
            $t->method('getPermiso_escritura')->willReturn(match ($id) {
                1017 => 16,
                1018 => null,
                default => 128,
            });
            $t->method('isDepende_modificar')->willReturn(false);

            return $t;
        });

        $perm = $this->sut()->getFichaData(self::ID_NOM, 'n')['aPerm'];

        $this->assertSame(1, $perm['publicaciones'], 'ni lectura ni escritura');
        $this->assertSame(3, $perm['curriculum'], 'lectura y escritura');
        $this->assertSame(2, $perm['nombramientos'], 'solo lectura (sin permiso de escritura definido)');
        $this->assertSame(2, $perm['ampliacion']);
        $this->assertSame(['publicaciones', 'curriculum', 'nombramientos', 'ampliacion', 'director', 'juramento', 'latin', 'congresos'], array_keys($perm));
        $this->assertArrayNotHasKey('docencia', $perm, 'hallazgo: un tipo de dossier inexistente deja el bloque sin clave de permiso');
    }

    // ---- enlaces -----------------------------------------------------------------------------------------

    public function test_los_enlaces_son_especificaciones_sin_firmar_con_el_contexto_recibido(): void
    {
        $this->hayPersona();

        $data = $this->sut()->getFichaData(self::ID_NOM, 'n', false, 'PersonaN', '3', 't');

        $this->assertSame([
            'path' => 'frontend/profesores/controller/ficha_profesor_stgr.php',
            'query' => ['id_nom' => self::ID_NOM, 'id_tabla' => 'n', 'permiso' => '3', 'depende' => 't'],
        ], $data['ficha_self_link_spec']);
        $specs = $data['go_cosas_link_specs'];
        $this->assertSame(
            ['print', 'latin', 'curriculum', 'nombramientos', 'ampliacion', 'congresos', 'docencia', 'director', 'juramento', 'publicaciones'],
            array_keys($specs)
        );
        $this->assertSame([
            'path' => 'frontend/profesores/controller/ficha_profesor_stgr.php',
            'query' => ['id_nom' => self::ID_NOM, 'id_tabla' => 'n', 'print' => '1'],
        ], $specs['print']);
        $this->assertSame([
            'path' => 'frontend/shared/controller/tablaDB_lista_ver.php',
            'query' => ['pau' => 'p', 'id_pau' => self::ID_NOM, 'obj_pau' => 'PersonaN', 'permiso' => '3', 'depende' => 't', 'clase_info' => InfoProfesorLatin::class],
        ], $specs['latin']);
        $this->assertSame(InfoProfesorPublicacion::class, $specs['publicaciones']['query']['clase_info']);
        $clases = array_map(static fn (array $s): string => $s['query']['clase_info'], array_slice($specs, 1));
        $this->assertCount(9, array_unique($clases), 'cada bloque enlaza a su propia clase de datos');
        foreach ($clases as $clase) {
            $this->assertTrue(class_exists($clase), $clase);
        }
    }
}
