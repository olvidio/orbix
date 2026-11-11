<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\application\CaPosiblesData;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/**
 * Cuadro de ca posibles: qué personas y qué ca se cruzan, y qué créditos podría cursar cada persona en cada ca
 * según su nivel de estudios. Dos modos: tabla por centro y lista de una sola persona seleccionada.
 */
final class CaPosiblesDataTest extends myTest
{
    private const PERIODO = ['periodo' => 'otro', 'empiezamin' => '1/6/2026', 'empiezamax' => '30/9/2026'];
    private const ASIGNATURAS = [1101 => ['nombre_asignatura' => 'Lógica', 'creditos' => 4.5]];

    private PosiblesCa&MockObject $posiblesCa;
    private PersonaDlRepositoryInterface&MockObject $personasDl;
    private PersonaNRepositoryInterface&MockObject $personasN;
    private PersonaAgdRepositoryInterface&MockObject $personasAgd;
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadPubRepositoryInterface&MockObject $actividadesPub;
    private ActividadDlRepositoryInterface&MockObject $actividadesDl;
    private ActividadAsignaturaRepositoryInterface&MockObject $asignaturasCa;
    private CentroDlRepositoryInterface&MockObject $centros;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var array<string, list<string>> */
    private array $filtros = [];
    /** @var list<object> */
    private array $personas = [];
    /** @var list<ActividadAll> */
    private array $publicadas = [];
    /** @var list<ActividadAll> */
    private array $sinPublicar = [];
    /** @var list<string> dl del grupo de estudios */
    private array $dlsDelGrupo = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $curso = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $_SESSION['session_auth']['sfsv'] = 1;

        $this->posiblesCa = $this->createMock(PosiblesCa::class);
        $this->posiblesCa->method('contar_creditos')->willReturnCallback(
            static fn (int $idNom, array $asignaturas): array => ['suma' => 4.5, 'lista' => $asignaturas]
        );
        $personas = fn (string $repo): \Closure => function (Criteria $c) use ($repo): array {
            $this->filtros[$repo] = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->personas;
        };
        $this->personasDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->personasDl->method('matching')->willReturnCallback($personas('personas dl'));
        $this->personasN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->personasN->method('matching')->willReturnCallback($personas('personas n'));
        $this->personasAgd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $this->personasAgd->method('matching')->willReturnCallback($personas('personas agd'));
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->delegaciones->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros['delegaciones'] = DescribeCriteria::filtros($c);
            $lista = [];
            foreach ($this->dlsDelGrupo as $dl) {
                $d = $this->createMock(Delegacion::class);
                $d->method('getDlVo')->willReturn(new DelegacionCode($dl));
                $lista[] = $d;
            }

            return $lista;
        });
        $this->actividadesPub = $this->createMock(ActividadPubRepositoryInterface::class);
        $this->actividadesPub->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros['publicadas'] = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $this->publicadas;
        });
        $this->actividadesDl = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->actividadesDl->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtros['dl'] = DescribeCriteria::filtros($c);

            return $this->sinPublicar;
        });
        $this->asignaturasCa = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $this->asignaturasCa->method('getAsignaturasCa')->willReturnCallback(
            static fn (int $idActiv): array => $idActiv === 3099 ? [] : self::ASIGNATURAS
        );
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->centros->method('findById')->willReturnCallback(function (int $id): ?CentroDl {
            if ($id === 1999) {
                return null;
            }
            $c = $this->createMock(CentroDl::class);
            $c->method('getNombre_ubi')->willReturn([1003 => 'Brafa', 1004 => 'Augusta'][$id] ?? 'Ctr ' . $id);

            return $c;
        });
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

    private function uc(): CaPosiblesData
    {
        return new CaPosiblesData(
            $this->posiblesCa,
            $this->personasDl,
            $this->personasN,
            $this->personasAgd,
            $this->delegaciones,
            $this->actividadesPub,
            $this->actividadesDl,
            $this->asignaturasCa,
            $this->centros,
        );
    }

    /**
     * @param class-string $clase
     */
    private function persona(int $idNom, string $nombre, ?int $nivel, ?int $idCtr = 1003, string $clase = PersonaN::class, ?string $ce = null): object
    {
        $p = $this->createMock($clase);
        $p->method('getId_nom')->willReturn($idNom);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);
        $p->method('getNivel_stgr')->willReturn($nivel);
        $p->method('getId_ctr')->willReturn($idCtr);
        $p->method('getId_tabla')->willReturn($clase === PersonaAgd::class ? 'a' : 'n');
        if ($clase !== PersonaDl::class) {
            $p->method('getCe')->willReturn($ce);
        }

        return $p;
    }

    private function ca(int $id, string $nombre, ?int $nivel, int $tipo = 112021): ActividadAll&MockObject
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getNom_activ')->willReturn($nombre);
        $a->method('getNivel_stgr')->willReturn($nivel);
        $a->method('getId_tipo_activ')->willReturn($tipo);

        return $a;
    }

    /** @return array<string, mixed> entrada del cuadro por centro de numerarios */
    private function porCentroN(int $idCtr = 1003): array
    {
        return ['id_ctr_n' => (string) $idCtr, 'grupo_estudios' => 'todos'] + self::PERIODO;
    }

    public function test_sin_centro_ni_seleccion_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->uc()->execute(self::PERIODO);
    }

    public function test_un_valor_de_na_desconocido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->uc()->execute(['id_ctr_n' => 1003, 'na' => 'sss'] + self::PERIODO);
    }

    public function test_numerarios_de_un_centro_activos_no_sacd_y_en_bienio_o_cuadrienio(): void
    {
        $this->uc()->execute($this->porCentroN());

        $this->assertSame([
            'nivel_stgr in [1,2,3]',
            'situacion eq A',
            'sacd eq false',
            'id_tabla eq n',
            'id_ctr eq 1003',
            'id_ctr asc',
            'apellido1 asc',
            'apellido2 asc',
            'nom asc',
        ], $this->filtros['personas n']);
        $this->assertArrayNotHasKey('personas agd', $this->filtros);
    }

    /**
     * Regresión: el filtro de nivel pasaba las etiquetas («Bienio»…) en lugar de los ids y la consulta real acababa
     * en error SQL. Se comprueba contra el repositorio real de numerarios.
     */
    public function test_la_consulta_de_numerarios_funciona_contra_el_repositorio_real(): void
    {
        $uc = new CaPosiblesData(
            $this->posiblesCa,
            $this->personasDl,
            $GLOBALS['container']->get(PersonaNRepositoryInterface::class),
            $this->personasAgd,
            $this->delegaciones,
            $this->actividadesPub,
            $this->actividadesDl,
            $this->asignaturasCa,
            $this->centros,
        );

        $r = $uc->execute(['id_ctr_n' => '1', 'grupo_estudios' => 'todos'] + self::PERIODO);

        $this->assertSame('tabla', $r['modo']);
    }

    public function test_el_centro_1_significa_todos_los_centros(): void
    {
        $this->uc()->execute($this->porCentroN(1));

        $this->assertNotContains('id_ctr eq 1', $this->filtros['personas n']);
    }

    public function test_agregados_de_un_centro_sin_filtro_de_nivel(): void
    {
        $this->uc()->execute(['id_ctr_agd' => 1004, 'grupo_estudios' => 'todos'] + self::PERIODO);

        $this->assertSame(
            ['situacion eq A', 'sacd eq false', 'id_tabla eq a', 'id_ctr eq 1004'],
            array_slice($this->filtros['personas agd'], 0, 4)
        );
    }

    /**
     * @dataProvider tiposProvider
     *
     * @param array<string, mixed> $entrada
     */
    public function test_el_tipo_de_ca_depende_del_colectivo_y_de_las_casillas(array $entrada, string $patron): void
    {
        $this->uc()->execute($entrada + ['grupo_estudios' => 'todos'] + self::PERIODO);

        $this->assertSame([
            'id_tipo_activ matches ' . $patron,
            'f_ini between [2026-06-01,2026-09-30]',
            'status eq 2',
            'nivel_stgr asc',
            'f_ini asc',
        ], $this->filtros['publicadas']);
        $this->assertSame('publicado eq false', end($this->filtros['dl']));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string}>
     */
    public static function tiposProvider(): iterable
    {
        yield 'n: todos los ca' => [['id_ctr_n' => 1003], '^112'];
        yield 'n: de estudios' => [['id_ctr_n' => 1003, 'ca_estudios' => 't'], '^1122'];
        yield 'n: de repaso' => [['id_ctr_n' => 1003, 'ca_repaso' => 't'], '^1124'];
        yield 'n: «todos» manda sobre las otras casillas' => [['id_ctr_n' => 1003, 'ca_todos' => 't', 'ca_repaso' => 't'], '^112'];
        yield 'agd: todos' => [['id_ctr_agd' => 1004], '^133'];
        yield 'agd: de estudios' => [['id_ctr_agd' => 1004, 'ca_estudios' => 't'], '^1332'];
        yield 'agd: de repaso' => [['id_ctr_agd' => 1004, 'ca_repaso' => 't'], '^1334'];
        yield 'agd con na explícito' => [['id_ctr_agd' => 1004, 'na' => 'agd'], '^133'];
    }

    public function test_con_grupo_de_estudios_solo_cuentan_los_ca_de_las_dl_del_grupo(): void
    {
        $this->dlsDelGrupo = ['dlb', 'dlp'];

        $this->uc()->execute(['id_ctr_n' => 1003, 'grupo_estudios' => 'g1'] + self::PERIODO);

        $this->assertSame(['grupo_estudios eq g1'], $this->filtros['delegaciones']);
        $this->assertContains('dl_org in [dlb,dlp]', $this->filtros['publicadas']);
    }

    /**
     * Hallazgo (ya comentado en el código): con una sola dl en el grupo se compara `dl_org` con el texto
     * entrecomillado (`'dlb'`), que no coincide nunca; y con ninguna, con la cadena vacía.
     */
    public function test_con_una_sola_dl_en_el_grupo_se_compara_con_el_texto_entrecomillado(): void
    {
        $this->dlsDelGrupo = ['dlb'];

        $this->uc()->execute(['id_ctr_n' => 1003, 'grupo_estudios' => 'g1'] + self::PERIODO);

        $this->assertContains("dl_org eq 'dlb'", $this->filtros['publicadas']);
    }

    public function test_el_cuadro_cruza_cada_persona_con_cada_ca_segun_su_nivel(): void
    {
        $this->personas = [
            $this->persona(10011, 'Pla, Joan', NivelStgrId::B),
            $this->persona(10012, 'Bosch, Pau', NivelStgrId::C1),
            $this->persona(10013, 'Roca, Pere', NivelStgrId::C2),
        ];
        $this->publicadas = [$this->ca(3001, 'ca n bienio', 1), $this->ca(3002, 'ca n cuadrienio I', 2)];
        $this->sinPublicar = [$this->ca(3003, 'ca n cuadrienio II', 3)];

        $r = $this->uc()->execute($this->porCentroN() + ['texto' => 'x', 'ref' => 'r1']);

        $this->assertSame('tabla', $r['modo']);
        $this->assertSame('', $r['msg_txt']);
        $this->assertCount(1, $r['tabla_filas']);
        $fila = $r['tabla_filas'][0];
        $this->assertSame('Brafa', $fila['ctr']);
        $this->assertSame(['x', 'r1'], [$fila['texto'], $fila['ref']]);
        $this->assertSame([1, 1, 1, 2, 0, 0, 0], [$fila['nc_bienio'], $fila['nc_cuadrienio1'], $fila['nc_cuadrienio2'], $fila['nc_cuadrienio'], $fila['nc_repaso'], $fila['nc_ce'], $fila['nc_otros']]);
        $this->assertSame(strlen('ca n cuadrienio II'), $fila['height']);
        $creditos = static fn (string $nombre): array => array_column($fila['cPersonas'][$nombre]['aActividades'], 'creditos', 'nom_activ');
        $this->assertSame(['ca n bienio' => 4.5, 'ca n cuadrienio I' => '-', 'ca n cuadrienio II' => '-'], $creditos('Pla, Joan'));
        // Cuadrienio I puede ir a los dos ca de cuadrienio; cuadrienio II–IV solo al suyo.
        $this->assertSame(['ca n bienio' => '-', 'ca n cuadrienio I' => 4.5, 'ca n cuadrienio II' => 4.5], $creditos('Bosch, Pau'));
        $this->assertSame(['ca n bienio' => '-', 'ca n cuadrienio I' => '-', 'ca n cuadrienio II' => 4.5], $creditos('Roca, Pere'));
        $this->assertSame(NivelStgrId::B, $fila['cPersonas']['Pla, Joan']['stgr']);
        $this->assertSame(self::ASIGNATURAS, $fila['cPersonas']['Pla, Joan']['aActividades'][3001]['aLista']);
        $this->assertSame([], $fila['cPersonas']['Pla, Joan']['aActividades'][3002]['aLista']);
    }

    /**
     * Regresión: las actividades publicadas y las de la dl sin publicar se unían con `+`; al ser listas, las de
     * la dl con el mismo índice que una publicada se perdían.
     */
    public function test_los_ca_sin_publicar_de_la_dl_se_anyaden_a_los_publicados(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::B)];
        $this->publicadas = [$this->ca(3001, 'ca n bienio A', 1)];
        $this->sinPublicar = [$this->ca(3002, 'ca n bienio B', 1)];

        $fila = $this->uc()->execute($this->porCentroN())['tabla_filas'][0];

        $this->assertSame([3001, 3002], array_keys($fila['aActividades']));
        $this->assertSame(2, $fila['nc_bienio']);
    }

    public function test_los_ca_de_repaso_no_necesitan_asignaturas_y_se_marcan_con_x_a_quien_esta_de_repaso(): void
    {
        $this->personas = [
            $this->persona(10011, 'Pla, Joan', NivelStgrId::R),
            $this->persona(10012, 'Bosch, Pau', NivelStgrId::B),
            $this->persona(10013, 'Roca, Pere', NivelStgrId::N),
        ];
        $this->publicadas = [$this->ca(3001, 'ca n repaso', 4), $this->ca(3002, 'ca n otros', 9)];
        $this->asignaturasCa->expects($this->never())->method('getAsignaturasCa');
        $this->posiblesCa->expects($this->never())->method('contar_creditos');

        $fila = $this->uc()->execute($this->porCentroN())['tabla_filas'][0];

        $creditos = static fn (string $nombre): array => array_column($fila['cPersonas'][$nombre]['aActividades'], 'creditos');
        $this->assertSame(['x', '-'], $creditos('Pla, Joan'));
        $this->assertSame(['-', '-'], $creditos('Bosch, Pau'));
        $this->assertSame(['-', 'x'], $creditos('Roca, Pere'));
        $this->assertSame([1, 1], [$fila['nc_repaso'], $fila['nc_otros']]);
    }

    public function test_un_agregado_de_repaso_puede_ir_tambien_a_los_ca_de_otros_niveles_sin_estudios(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::R, 1004, PersonaAgd::class)];
        $this->publicadas = [$this->ca(3001, 'ca agd repaso', 4), $this->ca(3002, 'ca agd otros', 9)];

        $fila = $this->uc()->execute(['id_ctr_agd' => 1004, 'grupo_estudios' => 'todos'] + self::PERIODO)['tabla_filas'][0];

        $this->assertSame(['x', 'x'], array_column($fila['cPersonas']['Pla, Joan']['aActividades'], 'creditos'));
    }

    public function test_un_agregado_de_centro_de_estudios_solo_cuenta_creditos_en_los_ca_de_ce(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::B, 1004, PersonaAgd::class, 'ce1')];
        $this->publicadas = [$this->ca(3001, 'ca agd bienio', 1), $this->ca(3002, 'ca agd ce', 5)];

        $fila = $this->uc()->execute(['id_ctr_agd' => 1004, 'grupo_estudios' => 'todos'] + self::PERIODO)['tabla_filas'][0];

        $this->assertSame(['-', 4.5], array_column($fila['cPersonas']['Pla, Joan']['aActividades'], 'creditos'));
        $this->assertSame(NivelStgrId::CE, $fila['cPersonas']['Pla, Joan']['stgr']);
        $this->assertSame(1, $fila['nc_ce']);
    }

    public function test_un_ca_sin_nivel_se_avisa_y_el_nivel_se_deduce_del_tipo_de_actividad(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::B)];
        $this->publicadas = [$this->ca(3001, 'ca n bienio', null, 112020)];

        $r = $this->uc()->execute($this->porCentroN());

        $this->assertStringContainsString('ca n bienio', $r['msg_txt']);
        $this->assertSame(1, $r['tabla_filas'][0]['nc_bienio']);
        $this->assertSame($r['msg_txt'], $r['tabla_filas'][0]['msg_txt']);
    }

    public function test_un_ca_de_estudios_sin_asignaturas_se_avisa_y_no_entra_en_el_cuadro(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::B)];
        $this->publicadas = [$this->ca(3099, 'ca n vacío', 1), $this->ca(3001, 'ca n bienio', 1)];

        $r = $this->uc()->execute($this->porCentroN());

        $this->assertStringContainsString('ca n vacío', $r['msg_txt']);
        $this->assertSame([3001], array_keys($r['tabla_filas'][0]['aActividades']));
    }

    public function test_las_personas_se_agrupan_por_centro_en_orden_alfabetico_y_se_avisan_los_centros_que_faltan(): void
    {
        $this->personas = [
            $this->persona(10011, 'Pla, Joan', NivelStgrId::B, 1003),
            $this->persona(10012, 'Bosch, Pau', NivelStgrId::B, 1004),
            $this->persona(10013, 'Roca, Pere', NivelStgrId::B, null),
            $this->persona(10014, 'Vidal, Lluc', NivelStgrId::B, 1999),
            $this->persona(10015, 'Soler, Marc', NivelStgrId::B, 1999),
        ];
        $this->publicadas = [$this->ca(3001, 'ca n bienio', 1)];

        $r = $this->uc()->execute($this->porCentroN(1));

        $centros = array_column($r['tabla_filas'], 'ctr');
        $this->assertSame('Augusta', $centros[0]);
        $this->assertSame('Brafa', $centros[1]);
        $this->assertCount(4, $centros);
        $this->assertStringContainsString('1999', $centros[2]);
        $this->assertSame(['Vidal, Lluc', 'Soler, Marc'], array_keys($r['tabla_filas'][2]['cPersonas']));
        // Un aviso por centro desconocido (no uno por persona) y otro por las personas sin centro.
        $this->assertSame(2, substr_count($r['msg_txt'], '<br>'));
        $this->assertSame(1, substr_count($r['msg_txt'], '1999'));
    }

    /**
     * Hallazgo: `stgr` de cada fila de la tabla es el nivel de la última persona de todo el cuadro, no un dato
     * del centro; y las personas se indexan por nombre (dos con el mismo «apellidos, nombre» en un centro se
     * pisan).
     */
    public function test_el_stgr_de_cada_fila_es_el_de_la_ultima_persona_del_cuadro(): void
    {
        $this->personas = [
            $this->persona(10011, 'Pla, Joan', NivelStgrId::B, 1004),
            $this->persona(10012, 'Bosch, Pau', NivelStgrId::C2, 1003),
        ];
        $this->publicadas = [$this->ca(3001, 'ca n bienio', 1)];

        $filas = $this->uc()->execute($this->porCentroN(1))['tabla_filas'];

        $this->assertSame(['Augusta', 'Brafa'], array_column($filas, 'ctr'));
        $this->assertSame([NivelStgrId::C2, NivelStgrId::C2], array_column($filas, 'stgr'));
    }

    public function test_con_idca_no_se_calcula_ningun_ca(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::B)];
        $this->publicadas = [$this->ca(3001, 'ca n bienio', 1)];

        $fila = $this->uc()->execute($this->porCentroN() + ['idca' => '3001'])['tabla_filas'][0];

        $this->assertSame([], $fila['aActividades']);
        $this->assertSame(0, $fila['nc_bienio']);
    }

    public function test_el_cuadro_vacio_explica_por_que(): void
    {
        $r = $this->uc()->execute($this->porCentroN());

        $this->assertSame([], $r['tabla_filas']);
        $this->assertStringContainsString('Brafa', $r['msg_txt']);
        $this->assertStringContainsString('2026-06-01', $r['msg_txt']);
        $this->assertSame(3, substr_count($r['msg_txt'], '<br>'));

        $agd = $this->uc()->execute(['id_ctr_agd' => 1, 'grupo_estudios' => 'todos'] + self::PERIODO);
        $this->assertSame(2, substr_count($agd['msg_txt'], '<br>'));
        $this->assertStringNotContainsString($r['msg_txt'], $agd['msg_txt']);
    }

    /**
     * Regresión: la variable que contaba las personas seleccionadas se perdió en una migración y el modo «lista»
     * dejó de ser alcanzable (y cada actividad emitía un aviso de variable sin definir).
     */
    public function test_con_una_sola_persona_seleccionada_se_devuelve_la_lista_de_sus_ca(): void
    {
        $this->personas = [$this->persona(10011, 'Pla, Joan', NivelStgrId::B, 1003, PersonaDl::class)];
        $this->publicadas = [$this->ca(3001, 'ca n bienio', 1), $this->ca(3002, 'ca n cuadrienio', 2)];

        $r = $this->uc()->execute(['sel' => ['10011#n'], 'obj_pau' => 'PersonaN', 'grupo_estudios' => 'g1']);

        $this->assertSame('lista', $r['modo']);
        $this->assertSame(['id_nom in [10011]', 'apellido1 asc', 'apellido2 asc', 'nom asc'], $this->filtros['personas dl']);
        $this->assertStringContainsString('Pla, Joan', $r['titulo']);
        $this->assertStringContainsString('Brafa', $r['titulo']);
        $this->assertSame(NivelStgrId::B, $r['stgr']);
        $this->assertSame(['ca n bienio' => 4.5, 'ca n cuadrienio' => '-'], array_column($r['aActividades'], 'creditos', 'nom_activ'));
        $this->assertSame([
            'path' => 'frontend/dossiers/controller/dossiers_ver.php',
            'query' => ['que' => 'activ', 'pau' => 'p', 'id_pau' => 10011, 'obj_pau' => 'PersonaN', 'id_dossier' => '1301y1302'],
        ], $r['pagina_link_spec']);
        // Con selección el grupo de estudios se ignora (todos) y el tipo de ca sale del segundo trozo de `sel`.
        $this->assertArrayNotHasKey('delegaciones', $this->filtros);
        $this->assertSame('id_tipo_activ matches ^112', $this->filtros['publicadas'][0]);
    }

    public function test_con_seleccion_el_periodo_va_de_hoy_al_final_del_mes_de_inicio_del_curso(): void
    {
        $this->uc()->execute(['sel' => ['10011#n']]);

        $any = (int) date('m') < 10 ? (int) date('Y') : (int) date('Y') + 1;
        $this->assertSame('f_ini between [' . date('Y-m-d') . ",$any-10-31]", $this->filtros['publicadas'][1]);
    }

    public function test_con_varias_personas_seleccionadas_se_devuelve_la_tabla_con_los_nombres_de_ca_abreviados(): void
    {
        $this->personas = [
            $this->persona(10011, 'Pla, Joan', NivelStgrId::B, 1003, PersonaDl::class),
            $this->persona(10012, 'Bosch, Pau', NivelStgrId::B, 1003, PersonaDl::class),
        ];
        $this->publicadas = [$this->ca(3001, 'ca n bienio Castelldaura', 1)];

        $r = $this->uc()->execute(['sel' => ['10011#n', '10012#n', '#n']]);

        $this->assertSame('tabla', $r['modo']);
        $this->assertSame(['id_nom in [10011,10012]', 'apellido1 asc', 'apellido2 asc', 'nom asc'], $this->filtros['personas dl']);
        $fila = $r['tabla_filas'][0];
        $this->assertSame('image', $fila['texto']);
        $this->assertSame('Castelldaura', $fila['aActividades'][3001]['nom_activ']);
        $this->assertSame(['Pla, Joan', 'Bosch, Pau'], array_keys($fila['cPersonas']));
    }

    public function test_una_seleccion_sin_el_colectivo_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->uc()->execute(['sel' => ['10011']]);
    }
}
