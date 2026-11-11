<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\CargoCode;
use src\actividadcargos\domain\value_objects\TipoCargoCode;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\ListaAsistentesData;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Listado de asistentes de una actividad: primero los que tienen cargo (con su cargo y si asisten), después los
 * demás asistentes numerados por orden alfabético; con `listcl`, los datos de cada persona.
 */
final class ListaAsistentesDataBuildTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;

    private ActividadAllRepositoryInterface&MockObject $actividades;
    private AsistenteRepositoryInterface&MockObject $asistentes;
    private ActividadCargoRepositoryInterface&MockObject $actividadCargos;
    private CargoRepositoryInterface&MockObject $cargos;
    /** @var array<int, PersonaDl> */
    private array $personas = [];
    /** @var list<Asistente> */
    private array $asistentesActividad = [];
    /** @var list<ActividadCargo> */
    private array $cargosActividad = [];
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $this->apps([]);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (int $id): ?PersonaDl => $this->personas[$id] ?? null);
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->asistentes->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = DescribeCriteria::filtros($c);
            if (count($f) === 1) {
                return $this->asistentesActividad;
            }
            $idNom = (int) substr($f[1], strlen('id_nom eq '));

            return array_values(array_filter($this->asistentesActividad, static fn (Asistente $a): bool => $a->getId_nom() === $idNom));
        });
        $this->actividadCargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->actividadCargos->method('matching')->willReturnCallback(fn (): array => $this->cargosActividad);
        $this->cargos = $this->createMock(CargoRepositoryInterface::class);
        $this->cargos->method('findById')->willReturnCallback(function (int $id): ?Cargo {
            $nombres = [1 => ['d.est.', 'd.est.'], 2 => ['sacd', 'sacd'], 3 => ['asist.', null]];
            if (!isset($nombres[$id])) {
                return null;
            }
            $c = $this->createMock(Cargo::class);
            $c->method('getCargoVo')->willReturn(new CargoCode($nombres[$id][0]));
            $c->method('getTipoCargoVo')->willReturn($nombres[$id][1] === null ? null : new TipoCargoCode($nombres[$id][1]));

            return $c;
        });
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    /** @param list<string> $instaladas */
    private function apps(array $instaladas): void
    {
        $todas = ['actividadcargos' => 951, 'actividadplazas' => 952];
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $config['a_apps'] = $todas;
        $config['app_installed'] = array_values(array_intersect_key($todas, array_flip($instaladas)));
        $_SESSION['config'] = $config;
    }

    private function uc(): ListaAsistentesData
    {
        return new ListaAsistentesData($this->actividades, $this->asistentes, $this->actividadCargos, $this->cargos);
    }

    private function persona(int $id, string $apellido, ?string $profesion = null): PersonaDl
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn($apellido);
        $p->method('getCentro_o_dl')->willReturn('ctr' . $id);
        $p->method('getProfesion')->willReturn($profesion);
        $p->method('getF_nacimiento')->willReturn(null);
        $p->method('getInc')->willReturn(null);
        $p->method('getEap')->willReturn(null);
        $this->personas[$id] = $p;

        return $p;
    }

    private function asistente(int $idNom, bool $propio = true, ?int $plaza = PlazaId::ASIGNADA, ?string $observ = null): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(self::ID_ACTIV);
        $a->setId_nom($idNom);
        $a->setPropio($propio);
        $a->setEst_ok(true);
        $a->setPlaza($plaza);
        $a->setObserv($observ);
        $this->asistentesActividad[] = $a;

        return $a;
    }

    private function cargo(int $idNom, int $idCargo, ?string $observ = null): void
    {
        $c = $this->createMock(ActividadCargo::class);
        $c->method('getId_nom')->willReturn($idNom);
        $c->method('getId_cargo')->willReturn($idCargo);
        $c->method('getObserv')->willReturn($observ);
        $this->cargosActividad[] = $c;
    }

    public function test_los_asistentes_salen_numerados_por_orden_alfabetico_sin_acentos(): void
    {
        $nombre = new ActividadAll();
        $nombre->setNom_activ('ca n verano');
        $this->actividades->method('findById')->with(self::ID_ACTIV)->willReturn($nombre);
        $this->persona(10011, 'Zamora, Luis');
        $this->persona(10012, 'Álvarez, Juan');
        $this->persona(10013, 'Bosch, Pau');
        $this->asistente(10011);
        $this->asistente(10012);
        $this->asistente(10013);
        $this->asistente(10019);

        $r = $this->uc()->build(['id_pau' => (string) self::ID_ACTIV, 'queSel' => 'lista']);

        $this->assertSame('ca n verano', $r['nom_activ']);
        $this->assertSame('lista', $r['queSel']);
        $this->assertSame(['1.-', '2.-', '3.-'], array_keys($r['aAsistentes']));
        $this->assertSame(['Álvarez, Juan  (ctr10012)', 'Bosch, Pau  (ctr10013)', 'Zamora, Luis  (ctr10011)'], array_column($r['aAsistentes'], 'nombre'));
        $this->assertSame([], $r['aAsistentes']['1.-']['a_datos_cl']);
    }

    public function test_la_actividad_puede_llegar_en_la_seleccion_con_su_nombre(): void
    {
        $this->actividades->expects($this->never())->method('findById');

        $r = $this->uc()->build(['sel' => [self::ID_ACTIV . '#ca n verano']]);

        $this->assertSame('ca n verano', $r['nom_activ']);
    }

    public function test_con_plazas_solo_salen_los_que_tienen_plaza_asignada_o_confirmada(): void
    {
        $this->apps(['actividadplazas']);
        $this->persona(10011, 'A');
        $this->persona(10012, 'B');
        $this->persona(10013, 'C');
        $this->persona(10014, 'D');
        $this->asistente(10011, true, PlazaId::ASIGNADA);
        $this->asistente(10012, true, PlazaId::CONFIRMADA);
        $this->asistente(10013, true, PlazaId::EN_ESPERA);
        $this->asistente(10014, true, null);

        $r = $this->uc()->build(['id_pau' => self::ID_ACTIV]);

        $this->assertSame(['A  (ctr10011)', 'B  (ctr10012)'], array_column($r['aAsistentes'], 'nombre'));
    }

    public function test_con_cargos_salen_primero_con_su_cargo_y_despues_el_resto_numerados_a_continuacion(): void
    {
        $this->apps(['actividadcargos']);
        $this->persona(10011, 'Pla, Joan');
        $this->persona(20001, 'Vidal, Pere');
        $this->persona(10012, 'Bosch, Pau');
        $this->persona(10013, 'Roca, Pere');
        $this->cargo(20001, 1, 'llega tarde');
        $this->cargo(10011, 3);
        $this->asistente(10011, true, PlazaId::ASIGNADA, 'observación propia');
        $this->asistente(10012);
        $this->asistente(10013);

        $r = $this->uc()->build(['id_pau' => self::ID_ACTIV]);

        $this->assertSame(['d.est.', 'asist.', '3.-', '4.-'], array_keys($r['aAsistentes']));
        $this->assertSame('Vidal, Pere  (ctr20001)', $r['aAsistentes']['d.est.']['nombre']);
        $this->assertSame('Pla, Joan  (ctr10011)', $r['aAsistentes']['asist.']['nombre']);
        $this->assertSame(['Bosch, Pau  (ctr10012)', 'Roca, Pere  (ctr10013)'], [$r['aAsistentes']['3.-']['nombre'], $r['aAsistentes']['4.-']['nombre']]);
    }

    public function test_con_listcl_cada_fila_lleva_los_datos_de_la_persona_y_las_observaciones(): void
    {
        $this->apps(['actividadcargos']);
        $this->persona(20001, 'Vidal, Pere', 'médico');
        $this->persona(10012, 'Bosch, Pau', 'arquitecto');
        $this->cargo(20001, 1, 'llega tarde');
        $this->asistente(10012, true, PlazaId::ASIGNADA, 'vegetariano');

        $r = $this->uc()->build(['id_pau' => self::ID_ACTIV, 'queSel' => 'listcl']);

        $cargo = $r['aAsistentes']['d.est.']['a_datos_cl'];
        $this->assertSame('médico', $cargo['profesion']);
        $this->assertSame('llega tarde', trim($cargo['observ']), 'el director no asiste: solo la observación del cargo');
        $this->assertSame(['arquitecto', 'vegetariano', '?'], [$r['aAsistentes']['2.-']['a_datos_cl']['profesion'], $r['aAsistentes']['2.-']['a_datos_cl']['observ'], $r['aAsistentes']['2.-']['a_datos_cl']['eap']]);
    }

    /**
     * Hallazgo: las filas de cargos se indexan por el nombre del cargo; dos personas con el mismo cargo (p. ej. dos
     * «asist.») quedan en una sola fila.
     */
    public function test_dos_personas_con_el_mismo_cargo_quedan_en_una_fila(): void
    {
        $this->apps(['actividadcargos']);
        $this->persona(20001, 'Vidal, Pere');
        $this->persona(20002, 'Soler, Marc');
        $this->cargo(20001, 3);
        $this->cargo(20002, 3);

        $r = $this->uc()->build(['id_pau' => self::ID_ACTIV]);

        $this->assertSame(['asist.'], array_keys($r['aAsistentes']));
        $this->assertSame('Soler, Marc  (ctr20002)', $r['aAsistentes']['asist.']['nombre']);
    }

    /**
     * Hallazgo: una persona con cargo cuyo cargo ya no existe, o a quien no se encuentra, se apunta como «tiene
     * cargo» antes de saltarla: desaparece del bloque de cargos y también del de asistentes.
     */
    public function test_una_persona_con_un_cargo_que_no_existe_desaparece_del_listado(): void
    {
        $this->apps(['actividadcargos']);
        $this->persona(10011, 'Pla, Joan');
        $this->cargo(10011, 99);
        $this->asistente(10011);

        $this->assertSame([], $this->uc()->build(['id_pau' => self::ID_ACTIV])['aAsistentes']);
    }

    public function test_en_sf_el_sacd_no_sale_en_ningun_bloque(): void
    {
        $this->apps(['actividadcargos']);
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->persona(20001, 'Vidal, Pere');
        $this->cargo(20001, 2);
        $this->asistente(20001);

        $this->assertSame([], $this->uc()->build(['id_pau' => self::ID_ACTIV])['aAsistentes']);
    }

    public function test_en_sv_el_sacd_sale_con_su_cargo(): void
    {
        $this->apps(['actividadcargos']);
        $this->persona(20001, 'Vidal, Pere');
        $this->cargo(20001, 2);

        $this->assertSame(['sacd'], array_keys($this->uc()->build(['id_pau' => self::ID_ACTIV])['aAsistentes']));
    }

    public function test_sin_la_app_de_cargos_no_se_consultan_los_cargos(): void
    {
        $this->actividadCargos->expects($this->never())->method('matching');
        $this->persona(10011, 'Pla, Joan');
        $this->asistente(10011);

        $this->assertSame(['1.-'], array_keys($this->uc()->build(['id_pau' => self::ID_ACTIV])['aAsistentes']));
    }

    public function test_la_persona_con_cargo_de_paso_sin_fecha_de_nacimiento_tiene_edad_vacia(): void
    {
        $this->apps(['actividadcargos']);
        $p = $this->persona(20001, 'Vidal, Pere');
        $this->cargo(20001, 1);

        $r = $this->uc()->build(['id_pau' => self::ID_ACTIV, 'queSel' => 'listcl']);

        $this->assertSame('', $r['aAsistentes']['d.est.']['a_datos_cl']['edad']);
        $this->assertInstanceOf(PersonaDl::class, $p);
    }

    public function test_la_edad_se_calcula_con_la_fecha_de_nacimiento(): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn('Pla, Joan');
        $p->method('getCentro_o_dl')->willReturn('ctr');
        $p->method('getF_nacimiento')->willReturn((new DateTimeLocal())->modify('-30 years -1 day'));
        $p->method('getInc')->willReturn('fl');
        $p->method('getF_inc')->willReturn(new DateTimeLocal('2020-05-04'));
        $this->personas[10011] = $p;
        $this->asistente(10011);

        $datos = $this->uc()->build(['id_pau' => self::ID_ACTIV, 'queSel' => 'listcl'])['aAsistentes']['1.-']['a_datos_cl'];

        $this->assertSame('30', $datos['edad']);
        $this->assertSame('fl : 4/5/2020', $datos['inc_f_inc']);
    }
}
