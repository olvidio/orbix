<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use frontend\shared\web\Lista;
use PHPUnit\Framework\MockObject\MockObject;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividadcargos\domain\entity\Cargo;
use src\actividadcargos\domain\value_objects\CargoCode;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\ListaPlazasConjuntoActividades;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Relación de asistentes de un conjunto de actividades: un grupo por actividad (nombre, centros encargados en las
 * cv de s, plazas) con los cargos primero y los asistentes numerados; con plazas, las pedidas al final.
 */
final class ListaPlazasConjuntoActividadesCasosTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const TIPO_CA_N = '112025';
    private const TIPO_CV_S = '143025';

    private ActividadRepositoryInterface&MockObject $actividades;
    private ResumenPlazasService&MockObject $resumen;
    private AsistenteRepositoryInterface&MockObject $asistentes;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private CasaRepositoryInterface&MockObject $casas;
    private AsistenteActividadService&MockObject $asistenciaSvc;
    private ActividadCargoRepositoryInterface&MockObject $actividadCargos;
    private CargoRepositoryInterface&MockObject $cargos;
    /** @var array<int, PersonaDl> */
    private array $personas = [];
    /** @var list<ActividadAll> */
    private array $actividadesDevueltas = [];
    /** @var array<int, list<Asistente>> */
    private array $asistentesPorActividad = [];
    /** @var array<int, list<ActividadCargo>> */
    private array $cargosPorActividad = [];
    /** @var array<int, array<string, mixed>> */
    private array $resumenPorActividad = [];
    private int $actividadActual = 0;
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->anterior['config'] = $_SESSION['config'] ?? null;
        $this->apps([]);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (int $id): ?PersonaDl => $this->personas[$id] ?? null);
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividades->method('matching')->willReturnCallback(fn (): array => $this->actividadesDevueltas);
        $this->resumen = $this->createMock(ResumenPlazasService::class);
        $this->resumen->method('setId_activ')->willReturnCallback(function (int $id): void {
            $this->actividadActual = $id;
        });
        $this->resumen->method('getResumen')->willReturnCallback(fn (): array => $this->resumenPorActividad[$this->actividadActual] ?? []);
        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->asistentes->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = \Tests\support\DescribeCriteria::filtros($c);
            $idActiv = (int) substr($f[0], strlen('id_activ eq '));
            $idNom = (int) substr($f[1], strlen('id_nom eq '));

            return array_values(array_filter($this->asistentesPorActividad[$idActiv] ?? [], static fn (Asistente $a): bool => $a->getId_nom() === $idNom));
        });
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->casas = $this->createMock(CasaRepositoryInterface::class);
        $this->asistenciaSvc = $this->createMock(AsistenteActividadService::class);
        $this->asistenciaSvc->method('getAsistentesDeActividad')->willReturnCallback(fn (int $id): array => $this->asistentesPorActividad[$id] ?? []);
        $this->actividadCargos = $this->createMock(ActividadCargoRepositoryInterface::class);
        $this->actividadCargos->method('matching')->willReturnCallback(function (Criteria $c): array {
            $f = \Tests\support\DescribeCriteria::filtros($c);

            return $this->cargosPorActividad[(int) substr($f[0], strlen('id_activ eq '))] ?? [];
        });
        $this->cargos = $this->createMock(CargoRepositoryInterface::class);
        $this->cargos->method('findById')->willReturnCallback(function (int $id): ?Cargo {
            $c = $this->createMock(Cargo::class);
            $c->method('getCargoVo')->willReturn(new CargoCode($id === 2 ? 'sacd' : 'd.est.'));

            return $c;
        });
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        if ($this->anterior['config'] === null) {
            unset($_SESSION['config']);
        } else {
            $_SESSION['config'] = $this->anterior['config'];
        }
        parent::tearDown();
    }

    /** @param list<string> $instaladas */
    private function apps(array $instaladas): void
    {
        $todas = ['actividadcargos' => 15, 'actividadplazas' => 17, 'actividadescentro' => 20];
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $config['a_apps'] = $todas;
        $config['app_installed'] = array_values(array_intersect_key($todas, array_flip($instaladas)));
        $_SESSION['config'] = $config;
    }

    private function sut(string $tipo = self::TIPO_CA_N, bool $sacd = false): ListaPlazasConjuntoActividades
    {
        $l = new ListaPlazasConjuntoActividades(
            $this->actividades,
            $this->resumen,
            $this->asistentes,
            $this->encargados,
            $this->casas,
            $this->asistenciaSvc,
            $this->actividadCargos,
            $this->cargos,
        );
        $l->setMi_dele('dlb');
        $l->setCriteria(Criteria::create());
        $l->setId_tipo_activ($tipo);
        $l->setSacd($sacd);

        return $l;
    }

    private function actividad(int $id, string $nombre, ?int $plazas = 20, string $dlOrg = 'dlb', bool $publicado = false, ?string $observ = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setNom_activ($nombre);
        $a->setPlazas($plazas);
        $a->setDl_org($dlOrg);
        $a->setPublicado($publicado);
        $a->setObserv($observ);
        $this->actividadesDevueltas[] = $a;

        return $a;
    }

    private function persona(int $id, string $nombre, string $dl = 'dlb', bool $sacd = false): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getPrefApellidosNombre')->willReturn($nombre);
        $p->method('getCentro_o_dl')->willReturn('ctr' . $id);
        $p->method('getDl')->willReturn($dl);
        $p->method('isSacd')->willReturn($sacd);
        $this->personas[$id] = $p;
    }

    private function asiste(int $idActiv, int $idNom, ?int $plaza = PlazaId::ASIGNADA): void
    {
        $a = new Asistente();
        $a->setId_activ($idActiv);
        $a->setId_nom($idNom);
        $a->setPlaza($plaza);
        $this->asistentesPorActividad[$idActiv][] = $a;
    }

    private function cargo(int $idActiv, int $idNom, int $idCargo = 1): void
    {
        $c = $this->createMock(ActividadCargo::class);
        $c->method('getId_nom')->willReturn($idNom);
        $c->method('getId_cargo')->willReturn($idCargo);
        $this->cargosPorActividad[$idActiv][] = $c;
    }

    /** @return array{array<int, string>, array<int, array<int, array<string, mixed>>>} grupos y datos */
    private static function contenido(Lista $l): array
    {
        return [
            (new \ReflectionProperty(Lista::class, 'aGrupos'))->getValue($l),
            (new \ReflectionProperty(Lista::class, 'aDatos'))->getValue($l),
        ];
    }

    public function test_sin_actividades_la_lista_esta_vacia(): void
    {
        [$grupos, $datos] = self::contenido($this->sut()->getLista());

        $this->assertSame([[], []], [$grupos, $datos]);
    }

    public function test_un_grupo_por_actividad_con_sus_asistentes_numerados(): void
    {
        $this->actividad(3001, 'ca julio', 20, 'dlb', false, '(cocina)');
        $this->actividad(3002, 'ca agosto');
        $this->persona(10011, 'Pla, Joan');
        $this->persona(10012, 'Bosch, Pau');
        $this->asiste(3001, 10011);
        $this->asiste(3001, 10012);
        $this->asiste(3001, 10019);

        [$grupos, $datos] = self::contenido($this->sut()->getLista());

        $this->assertSame([3001, 3002], array_keys($grupos));
        $this->assertStringStartsWith('ca julio (cocina)<br>', $grupos[3001]);
        $this->assertStringContainsString(': 20, ', $grupos[3001]);
        $this->assertStringEndsWith(': 2', $grupos[3001], 'ocupadas: los dos que se encuentran');
        $this->assertSame([1 => ['cargo' => 1, 'ap_nom' => 'Pla, Joan (ctr10011)'], 2 => ['cargo' => 2, 'ap_nom' => 'Bosch, Pau (ctr10012)']], $datos[3001]);
        $this->assertSame([], $datos[3002]);
    }

    public function test_mas_asistentes_que_plazas_resalta_el_numero(): void
    {
        $this->actividad(3001, 'ca julio', 1);
        $this->persona(10011, 'A');
        $this->persona(10012, 'B');
        $this->asiste(3001, 10011);
        $this->asiste(3001, 10012);

        [$grupos] = self::contenido($this->sut()->getLista());

        $this->assertStringContainsString('<span class="alert">2</span>', $grupos[3001]);
    }

    public function test_si_no_organizo_solo_veo_a_los_de_mi_dl(): void
    {
        $this->actividad(3001, 'ca de dlp', 20, 'dlp');
        $this->persona(10011, 'Pla, Joan');
        $this->persona(20011, 'Otro, De Dlp', 'dlp');
        $this->asiste(3001, 10011);
        $this->asiste(3001, 20011);

        [, $datos] = self::contenido($this->sut()->getLista());

        $this->assertSame(['Pla, Joan (ctr10011)'], array_column($datos[3001], 'ap_nom'));
    }

    public function test_con_cargos_salen_primero_y_no_se_repiten(): void
    {
        $this->apps(['actividadcargos']);
        $this->actividad(3001, 'ca julio');
        $this->persona(20001, 'Vidal, Pere');
        $this->persona(20002, 'Soler, Marc', 'dlb', true);
        $this->persona(10011, 'Pla, Joan');
        $this->cargo(3001, 20001);
        $this->cargo(3001, 20002, 2);
        $this->asiste(3001, 20001);
        $this->asiste(3001, 10011);

        [, $datos] = self::contenido($this->sut()->getLista());

        $this->assertSame([
            1 => ['cargo' => 'd.est.', 'ap_nom' => 'Vidal, Pere (ctr20001)', 'texto' => ''],
            2 => ['cargo' => 'sacd', 'ap_nom' => 'Soler, Marc (ctr20002)', 'texto' => 'No asiste'],
            3 => ['cargo' => 3, 'ap_nom' => 'Pla, Joan (ctr10011)'],
        ], $datos[3001]);
    }

    public function test_solo_sacd_filtra_cargos_y_asistentes_y_quita_el_resumen_de_plazas(): void
    {
        $this->apps(['actividadcargos']);
        $this->actividad(3001, 'ca julio');
        $this->persona(20001, 'Vidal, Pere');
        $this->persona(20002, 'Soler, Marc', 'dlb', true);
        $this->persona(10013, 'Roca, Pere', 'dlb', true);
        $this->cargo(3001, 20001);
        $this->cargo(3001, 20002, 2);
        $this->asiste(3001, 10013);

        [$grupos, $datos] = self::contenido($this->sut(self::TIPO_CA_N, true)->getLista());

        $this->assertSame(['Soler, Marc (ctr20002)', 'Roca, Pere (ctr10013)'], array_column($datos[3001], 'ap_nom'));
        $this->assertSame('ca julio', $grupos[3001]);
    }

    public function test_publicada_muestra_las_plazas_de_la_dl_y_la_diferencia(): void
    {
        $this->actividad(3001, 'ca julio', 20, 'dlb', true);
        $this->resumenPorActividad[3001] = ['dlb' => ['total_disponibles' => 10, 'total_ocupadas' => 12]];

        [$grupos] = self::contenido($this->sut()->getLista());

        $this->assertStringContainsString(': 10, ', $grupos[3001]);
        $this->assertStringContainsString(': 12', $grupos[3001]);
        $this->assertStringEndsWith('<span class="alert">-2</span>', $grupos[3001]);
    }

    public function test_las_plazas_de_la_casa_salen_si_la_actividad_no_tiene(): void
    {
        $a = $this->actividad(3001, 'ca julio', null);
        $a->setId_ubi(1077);
        $casa = $this->createMock(Casa::class);
        $casa->method('getPlazas')->willReturn(40);
        $casa->method('getPlazas_min')->willReturn(25);
        $this->casas->method('findById')->with(1077)->willReturn($casa);

        [$grupos] = self::contenido($this->sut()->getLista());

        $this->assertStringContainsString(': 40 - 25, ', $grupos[3001]);
    }

    public function test_con_plazas_las_pedidas_de_mi_dl_van_al_final_resaltadas(): void
    {
        $this->apps(['actividadplazas']);
        $this->actividad(3001, 'ca julio', 5);
        $this->persona(10011, 'Pla, Joan');
        $this->persona(10012, 'Bosch, Pau');
        $this->persona(20011, 'Otro, De Dlp', 'dlp');
        $this->asiste(3001, 10012, PlazaId::PEDIDA);
        $this->asiste(3001, 10011, PlazaId::ASIGNADA);
        $this->asiste(3001, 20011, PlazaId::EN_ESPERA);

        [, $datos] = self::contenido($this->sut()->getLista());

        $this->assertSame(['Pla, Joan (ctr10011)', '<span class="alert">Bosch, Pau (ctr10012)</span>'], array_column($datos[3001], 'ap_nom'));
        $this->assertSame([1, ''], array_column($datos[3001], 'cargo'));
    }

    /**
     * Regresión: las pedidas iban con la clave `num + plazas + pedidas` para quedar al final; si la actividad no
     * tenía plazas (o había más asignados que plazas), esa clave coincidía con la de un asignado posterior y una de
     * las dos personas desaparecía.
     */
    public function test_sin_plazas_en_la_actividad_una_pedida_no_pisa_a_un_asignado(): void
    {
        $this->apps(['actividadplazas']);
        $this->actividad(3001, 'ca julio', null);
        $this->persona(10011, 'Pla, Joan');
        $this->persona(10012, 'Bosch, Pau');
        $this->persona(10013, 'Roca, Pere');
        $this->asiste(3001, 10012, PlazaId::PEDIDA);
        $this->asiste(3001, 10011, PlazaId::ASIGNADA);
        $this->asiste(3001, 10013, PlazaId::ASIGNADA);

        [, $datos] = self::contenido($this->sut()->getLista());

        $this->assertSame(
            ['Pla, Joan (ctr10011)', 'Roca, Pere (ctr10013)', '<span class="alert">Bosch, Pau (ctr10012)</span>'],
            array_column($datos[3001], 'ap_nom')
        );
    }

    /**
     * Regresión: preguntaba por la app «actividadcentros», que no existe (se llama «actividadescentro»): en las cv de
     * s nunca salían los centros encargados junto al nombre.
     */
    public function test_en_las_cv_de_s_el_nombre_lleva_los_centros_encargados(): void
    {
        $this->apps(['actividadescentro']);
        $this->actividad(3001, 'cv s mayo');
        $encargados = [];
        foreach ([1003 => 'Brafa', 1004 => 'Augusta'] as $id => $nombre) {
            $e = $this->createMock(CentroEncargado::class);
            $e->method('getId_ubi')->willReturn($id);
            $encargados[] = $e;
        }
        $this->encargados->method('matching')->willReturn($encargados);
        $centros = $this->createMock(CentroRepositoryInterface::class);
        $centros->method('findById')->willReturnCallback(function (int $id): ?Centro {
            $c = $this->createMock(Centro::class);
            $c->method('getTipo_ubi')->willReturn('ctrdl');
            $c->method('getDl')->willReturn('otra');
            $c->method('getNombre_ubi')->willReturn($id === 1003 ? 'Brafa' : 'Augusta');

            return $c;
        });
        $this->sustituirServicio(CentroRepositoryInterface::class, $centros);

        [$grupos] = self::contenido($this->sut(self::TIPO_CV_S)->getLista());

        $this->assertStringStartsWith('cv s mayo [Brafa; Augusta]', $grupos[3001]);
    }
}
