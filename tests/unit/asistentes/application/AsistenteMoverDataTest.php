<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\AsistenteMoverData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Modal «mover asistente»: actividades del mismo tipo en el curso a las que se puede mover, con las preferidas
 * (peticiones de plaza) primero, plazas libres de mi dl y créditos que podría cursar.
 *
 * Las ramas de aviso (persona/actividad no válidas, asistente inexistente, de otra dl) están en
 * `AsistentesListasQueTest`; aquí, el camino feliz que quedó pendiente en la fase 1.
 */
final class AsistenteMoverDataTest extends myTest
{
    private const ID_NOM = 10011;
    private const ACTIV_ORIGEN = 3001;
    private const TIPO_CA = 112025;
    private const TIPO_CRT = 111025;
    private const TIPO_CVE = 114025;

    private AsistenteActividadService&MockObject $servicio;
    private AsistenteApplicationService&MockObject $app;
    private ActividadAllRepositoryInterface&MockObject $actividadesAll;
    private ActividadRepositoryInterface&MockObject $actividades;
    private ResumenPlazasService&MockObject $plazas;
    private PlazaPeticionRepositoryInterface&MockObject $peticiones;
    private ActividadAsignaturaDlRepositoryInterface&MockObject $asignaturas;
    private PosiblesCa&MockObject $posiblesCa;
    /** @var array<int, ActividadAll> */
    private array $porId = [];
    /** @var list<ActividadAll> */
    private array $delCurso = [];
    /** @var list<string> */
    private array $filtros = [];
    /** @var list<string> */
    private array $filtrosPeticiones = [];
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'config'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $stgr = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $crt = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $stgr, $crt);
        $this->instalarPlazas(true);

        $this->servicio = $this->createMock(AsistenteActividadService::class);
        $this->app = $this->createMock(AsistenteApplicationService::class);
        $asistente = new Asistente();
        $asistente->setId_activ(self::ACTIV_ORIGEN);
        $asistente->setId_nom(self::ID_NOM);
        $asistente->setDl_responsable('dlb');
        $this->app->method('findById')->willReturn($asistente);
        $this->actividadesAll = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->actividadesAll->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => $this->porId[$id] ?? null);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->delCurso;
        });
        $this->plazas = $this->createMock(ResumenPlazasService::class);
        $this->peticiones = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $this->asignaturas = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $this->asignaturas->method('getAsignaturasCa')->willReturn([]);
        $this->posiblesCa = $this->createMock(PosiblesCa::class);
        $this->posiblesCa->method('contar_creditos')->willReturn(['suma' => 0, 'lista' => []]);
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function instalarPlazas(bool $si): void
    {
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $config['a_apps'] = ['actividadplazas' => 941];
        $config['app_installed'] = $si ? [941] : [];
        $_SESSION['config'] = $config;
    }

    private function uc(): AsistenteMoverData
    {
        return new AsistenteMoverData(
            $this->servicio,
            $this->app,
            $this->actividadesAll,
            $this->actividades,
            $this->plazas,
            $this->peticiones,
            $this->asignaturas,
            $this->posiblesCa,
        );
    }

    private function actividad(int $id, string $nombre, int $tipo = self::TIPO_CA, int $status = StatusId::ACTUAL, string $fIni = '2099-07-01'): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setNom_activ($nombre);
        $a->setId_tipo_activ($tipo);
        $a->setStatus($status);
        $a->setF_ini(new DateTimeLocal($fIni));
        $this->porId[$id] = $a;

        return $a;
    }

    /** @param list<int> $ids */
    private function peticiones(array $ids): void
    {
        $lista = [];
        foreach ($ids as $id) {
            $p = $this->createMock(PlazaPeticion::class);
            $p->method('getId_activ')->willReturn($id);
            $lista[] = $p;
        }
        $this->peticiones->method('matching')->willReturnCallback(function (Criteria $c) use ($lista): array {
            $this->filtrosPeticiones = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $lista;
        });
    }

    /** @return array{string, string} */
    private static function curso(string $tipo): array
    {
        $oConfig = $_SESSION['oConfig'];
        $any = $oConfig->any_final_curs($tipo);

        return [
            FuncTablasSupport::cursoEst('inicio', $any, $tipo)->format('Y-m-d'),
            FuncTablasSupport::cursoEst('fin', $any, $tipo)->format('Y-m-d'),
        ];
    }

    private function entrada(): array
    {
        return ['id_activ' => (string) self::ACTIV_ORIGEN, 'id_pau' => (string) self::ID_NOM];
    }

    public function test_ofrece_las_actividades_del_mismo_tipo_del_curso_menos_la_de_origen(): void
    {
        $this->instalarPlazas(false);
        $this->actividad(self::ACTIV_ORIGEN, 'ca origen');
        $this->delCurso = [$this->actividad(3002, 'ca julio'), $this->porId[self::ACTIV_ORIGEN], $this->actividad(3003, 'ca agosto')];
        $this->peticiones->expects($this->never())->method('matching');
        $this->plazas->expects($this->never())->method('getPlazasConcedidasYLibres');

        $r = $this->uc()->build($this->entrada());

        [$ini, $fin] = self::curso('est');
        $this->assertSame([
            "f_ini between [$ini,$fin]",
            'id_tipo_activ matches ^' . self::TIPO_CA,
            'status eq ' . StatusId::ACTUAL,
            'f_ini asc',
        ], $this->filtros);
        $this->assertSame('', $r['aviso_txt']);
        $this->assertSame([3002, 3003], array_keys($r['opciones_actividades']));
        $this->assertSame('ca julio', trim($r['opciones_actividades'][3002]));
        $this->assertSame([
            'campos_no' => 'falta!est_ok',
            'campos_form' => 'observ!id_activ',
            'campos_hidden' => [
                'id_nom' => self::ID_NOM,
                'id_activ_old' => self::ACTIV_ORIGEN,
                'mod' => 'mover',
                'propio' => 't',
                'plaza' => PlazaId::ASIGNADA,
                'propietario' => '',
            ],
        ], $r['hash_main']);
    }

    public function test_un_crt_usa_el_curso_de_crt(): void
    {
        $this->instalarPlazas(false);
        $this->actividad(self::ACTIV_ORIGEN, 'crt origen', self::TIPO_CRT);

        $this->uc()->build($this->entrada());

        [$ini, $fin] = self::curso('crt');
        $this->assertSame("f_ini between [$ini,$fin]", $this->filtros[0]);
    }

    /**
     * Hallazgo (anotado en la fase 1): para un tipo que no es ca, cv ni crt no hay curso y el filtro de fechas se
     * monta entre dos cadenas vacías.
     */
    public function test_otro_tipo_de_actividad_filtra_entre_fechas_vacias(): void
    {
        $this->instalarPlazas(false);
        $this->actividad(self::ACTIV_ORIGEN, 'cve origen', self::TIPO_CVE);

        $this->uc()->build($this->entrada());

        $this->assertSame('f_ini between [,]', $this->filtros[0]);
    }

    public function test_las_actividades_pedidas_salen_primero_separadas_y_solo_si_siguen_valiendo(): void
    {
        [$ini] = self::curso('est');
        $this->actividad(self::ACTIV_ORIGEN, 'ca origen');
        $this->actividad(3010, 'ca pedida');
        $this->actividad(3011, 'ca pedida pero anulada', self::TIPO_CA, StatusId::BORRABLE);
        $this->actividad(3012, 'ca pedida del curso pasado', self::TIPO_CA, StatusId::ACTUAL, (new DateTimeLocal($ini))->modify('-1 day')->format('Y-m-d'));
        $this->delCurso = [$this->actividad(3002, 'ca julio')];
        $this->peticiones([3010, 3011, 3012, 3999]);
        $this->plazas->method('getPlazasConcedidasYLibres')->willReturn(['concedidas' => 0, 'libres' => 0]);

        $r = $this->uc()->build($this->entrada());

        $this->assertSame(['id_nom eq ' . self::ID_NOM, 'tipo eq ca', 'orden asc'], $this->filtrosPeticiones);
        $this->assertSame([3010, 0, 3002], array_keys($r['opciones_actividades']));
        $this->assertStringStartsWith('---', $r['opciones_actividades'][0]);
    }

    public function test_sin_peticiones_validas_no_hay_separador(): void
    {
        $this->actividad(self::ACTIV_ORIGEN, 'ca origen');
        $this->delCurso = [$this->actividad(3002, 'ca julio')];
        $this->peticiones([]);
        $this->plazas->method('getPlazasConcedidasYLibres')->willReturn(['concedidas' => 0, 'libres' => 0]);

        $this->assertSame([3002], array_keys($this->uc()->build($this->entrada())['opciones_actividades']));
    }

    public function test_cada_opcion_lleva_las_plazas_libres_de_mi_dl_y_los_creditos_que_podria_cursar(): void
    {
        $this->actividad(self::ACTIV_ORIGEN, 'ca origen');
        $this->delCurso = [$this->actividad(3002, 'ca julio'), $this->actividad(3003, 'ca agosto')];
        $this->peticiones([]);
        $vistas = [];
        $this->plazas->method('setId_activ')->willReturnCallback(function (int $id) use (&$vistas): void {
            $vistas[] = $id;
        });
        $this->plazas->method('getPlazasConcedidasYLibres')->with('dlb')->willReturnOnConsecutiveCalls(
            ['concedidas' => 10, 'libres' => 3],
            ['concedidas' => 0, 'libres' => 0],
        );
        $asig = [1101 => ['nombre_asignatura' => 'Lógica', 'creditos' => 4.5]];
        $asignaturas = $this->createMock(ActividadAsignaturaDlRepositoryInterface::class);
        $asignaturas->method('getAsignaturasCa')->willReturnMap([[3002, '', $asig], [3003, '', []]]);
        $this->asignaturas = $asignaturas;
        $posibles = $this->createMock(PosiblesCa::class);
        $posibles->method('contar_creditos')->willReturnCallback(static fn (int $idNom, array $a): array => ['suma' => $a === [] ? 0 : 4.5, 'lista' => $a]);
        $this->posiblesCa = $posibles;

        $opciones = $this->uc()->build($this->entrada())['opciones_actividades'];

        $this->assertSame([3002, 3003], $vistas);
        $this->assertStringContainsString('3/10', $opciones[3002]);
        $this->assertStringContainsString('4.5', $opciones[3002]);
        $this->assertSame('ca agosto', trim($opciones[3003]), 'sin plazas concedidas ni créditos, solo el nombre');
    }

    public function test_si_la_actividad_de_origen_no_existe_no_hay_opciones(): void
    {
        $this->actividades->expects($this->never())->method('matching');

        $r = $this->uc()->build($this->entrada());

        $this->assertSame([], $r['opciones_actividades']);
        $this->assertSame('mover', $r['hash_main']['campos_hidden']['mod']);
    }

    public function test_la_persona_puede_llegar_en_la_seleccion_de_la_lista(): void
    {
        $this->instalarPlazas(false);
        $this->actividad(self::ACTIV_ORIGEN, 'ca origen');
        $this->servicio->expects($this->once())->method('getRepoAsistente')->with(self::ID_NOM, self::ACTIV_ORIGEN);

        $r = $this->uc()->build(['sel' => [self::ID_NOM . '#x'], 'id_activ' => self::ACTIV_ORIGEN]);

        $this->assertSame(self::ID_NOM, $r['hash_main']['campos_hidden']['id_nom']);
    }
}
