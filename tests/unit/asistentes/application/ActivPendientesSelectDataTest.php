<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asistentes\application\ActivPendientesSelectData;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Quién no ha ido todavía a una actividad del curso: n, agd o sacd, de mi dl y de otras. */
final class ActivPendientesSelectDataTest extends myTest
{
    private ActividadRepositoryInterface&MockObject $actividades;
    private AsistenteRepositoryInterface&MockObject $asistentes;
    private PersonaNRepositoryInterface&MockObject $personasN;
    private PersonaAgdRepositoryInterface&MockObject $personasAgd;
    private PersonaDlRepositoryInterface&MockObject $personasDl;
    private CentroDlRepositoryInterface&MockObject $centros;
    private mixed $configAnterior;
    private mixed $sesionAnterior;
    /** @var list<string> */
    private array $filtrosActividades = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['oConfig'] ?? null;
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosActividades = DescribeCriteria::filtros($c);

            return $this->actividadesDevueltas;
        });
        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->personasN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->personasAgd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $this->personasDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
    }

    /** @var list<ActividadAll> */
    private array $actividadesDevueltas = [];

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

    private function uc(): ActivPendientesSelectData
    {
        return new ActivPendientesSelectData($this->actividades, $this->asistentes, $this->personasN, $this->personasAgd, $this->personasDl, $this->centros);
    }

    private function persona(string $clase, int $id, string $nombre, ?int $ctr = 1003, int $nivel = 1): PersonaN|PersonaDl
    {
        $p = $this->createMock($clase);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);
        $p->method('getId_ctr')->willReturn($ctr);
        $p->method('getNivel_stgr')->willReturn($nivel);

        return $p;
    }

    private function actividad(int $id): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);

        return $a;
    }

    private function asistente(int $idNom): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_nom')->willReturn($idNom);

        return $a;
    }

    /**
     * @dataProvider seleccionesProvider
     */
    public function test_el_tipo_de_actividad_depende_de_las_personas_y_de_ca_o_crt(string $personas, string $actividad, string $patron): void
    {
        $this->uc()->build(['tipo_personas' => $personas, 'sactividad' => $actividad, 'any' => '2026']);

        $this->assertSame('id_tipo_activ matches ' . $patron, $this->filtrosActividades[0]);
        $this->assertStringStartsWith('f_ini between', $this->filtrosActividades[1]);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function seleccionesProvider(): iterable
    {
        yield 'n en ca' => ['n', 'ca', '(112...)|(133...)'];
        yield 'sacd en ca' => ['sacd', 'ca', '(112...)|(133...)'];
        yield 'agd en ca' => ['agd', 'ca', '133...'];
        yield 'n en crt' => ['n', 'crt', '1[1376]1...'];
        yield 'agd en crt' => ['agd', 'crt', '131...'];
        yield 'sacd en crt' => ['sacd', 'crt', '1[136]1...'];
    }

    public function test_sin_ca_ni_crt_no_se_buscan_actividades(): void
    {
        $this->actividades->expects($this->never())->method('matching');

        $this->uc()->build(['tipo_personas' => 'n', 'sactividad' => 'otra']);
    }

    /**
     * Hallazgo: con una combinación sin patrón (p. ej. `sacd`/`stgr` en crt sin caso, o un tipo de personas
     * desconocido) el patrón queda vacío y `matches ''` casa con todas las actividades del curso.
     */
    public function test_un_tipo_de_personas_sin_patron_busca_con_el_patron_vacio(): void
    {
        $this->uc()->build(['tipo_personas' => 'stgr', 'sactividad' => 'crt']);

        $this->assertSame('id_tipo_activ matches ', $this->filtrosActividades[0]);
    }

    public function test_los_selectores_marcan_el_curso_el_tipo_de_personas_y_la_actividad(): void
    {
        $actual = (int) date('Y');

        $r = $this->uc()->build(['tipo_personas' => 'agd', 'sactividad' => 'crt', 'any' => (string) ($actual + 1)]);

        $this->assertSame(['', 'selected', 'selected', '', '', 'selected'], [$r['chk_any_1'], $r['chk_any_2'], $r['chk_agd'], $r['chk_n'], $r['chk_ca'], $r['chk_crt']]);
        $this->assertSame(($actual - 1) . '/' . $actual, $r['txt_curso_1']);
        $this->assertStringContainsString($actual . '/' . ($actual + 1), $r['titulo']);
        $this->assertSame('tipo_personas!sactividad!any', $r['hash_main']['campos_form']);
    }

    public function test_sin_curso_se_usa_el_actual(): void
    {
        $r = $this->uc()->build(['tipo_personas' => 'n', 'sactividad' => 'ca']);

        $this->assertSame('selected', $r['chk_any_1']);
        $this->assertSame('', $r['chk_any_2']);
    }

    public function test_los_que_faltan_son_los_de_mi_dl_que_no_asistieron_en_orden_sin_acentos(): void
    {
        $this->actividadesDevueltas = [$this->actividad(3001)];
        $this->asistentes->method('matching')->willReturn([$this->asistente(10012)]);
        $filtros = [];
        $this->personasN->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros[] = DescribeCriteria::filtros($c);

            return $filtros === [DescribeCriteria::filtros($c)] ? [
                $this->persona(PersonaN::class, 10011, 'Ñoño, B'),
                $this->persona(PersonaN::class, 10012, 'Asistió, A'),
                $this->persona(PersonaN::class, 10013, 'Álvarez, C'),
                $this->persona(PersonaN::class, 10014, 'Sin centro, D', null),
                $this->persona(PersonaN::class, 10015, '', 1003),
            ] : [];
        });
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $this->centros->method('findById')->willReturnMap([[1003, $c]]);

        $r = $this->uc()->build(['tipo_personas' => 'n', 'sactividad' => 'ca', 'any' => (string) date('Y')]);

        $fila = $r['a_valores_activ_pendientes_dl'];
        $this->assertContains('situacion eq A', $filtros[0]);
        $nombres = array_column(array_column($fila, 2), 'valor');
        $this->assertSame(['Álvarez, C', 'Ñoño, B'], $nombres);
        $this->assertSame([1, 'Centro A'], [$fila[1][1], $fila[1][3]]);
        $this->assertSame(['obj_pau' => 'PersonaN', 'id_nom' => 10013], $fila[1][2]['link_spec']['query']);
        $this->assertSame('frontend/personas/controller/home_persona.php', $fila[1][2]['link_spec']['path']);
    }

    public function test_un_centro_inexistente_sale_como_interrogante(): void
    {
        $this->actividadesDevueltas = [];
        $this->personasN->method('matching')->willReturnOnConsecutiveCalls([$this->persona(PersonaN::class, 10011, 'Gil, A', 9999)], []);
        $this->centros->method('findById')->willReturn(null);

        $r = $this->uc()->build(['tipo_personas' => 'n', 'sactividad' => 'ca']);

        $this->assertSame('?', $r['a_valores_activ_pendientes_dl'][1][3]);
    }

    public function test_los_de_otras_dl_siguen_la_numeracion_y_muestran_el_nivel_sin_traducir(): void
    {
        $this->actividadesDevueltas = [];
        $this->personasN->method('matching')->willReturnOnConsecutiveCalls(
            [$this->persona(PersonaN::class, 10011, 'Gil, A')],
            [$this->persona(PersonaN::class, 20011, 'Otra, B', 1003, 4)]
        );
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $this->centros->method('findById')->willReturn($c);

        $r = $this->uc()->build(['tipo_personas' => 'n', 'sactividad' => 'ca']);

        $this->assertSame([2], array_keys($r['a_valores_activ_pendientes_otras']));
        $this->assertSame(4, $r['a_valores_activ_pendientes_otras'][2][4], 'sin traducir al texto del nivel (a diferencia de la tabla de mi dl)');
        $this->assertSame(NivelStgrId::getArrayNivelStgr()[1] ?? '', $r['a_valores_activ_pendientes_dl'][1][4]);
    }

    public function test_cada_tipo_de_personas_usa_su_repositorio(): void
    {
        $this->actividadesDevueltas = [];
        $this->personasAgd->expects($this->exactly(2))->method('matching')->willReturn([]);
        $this->personasDl->expects($this->exactly(2))->method('matching')->willReturn([]);
        $this->personasN->expects($this->never())->method('matching');

        $agd = $this->uc()->build(['tipo_personas' => 'agd', 'sactividad' => 'ca']);
        $sacd = $this->uc()->build(['tipo_personas' => 'sacd', 'sactividad' => 'ca']);
        $desconocido = $this->uc()->build(['tipo_personas' => 'zz', 'sactividad' => 'ca']);

        $this->assertSame([], $agd['a_valores_activ_pendientes_dl']);
        $this->assertSame([], $sacd['a_valores_activ_pendientes_otras']);
        $this->assertSame([], $desconocido['a_valores_activ_pendientes_dl']);
    }
}
