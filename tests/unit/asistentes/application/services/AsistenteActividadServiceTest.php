<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application\services;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaPub;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Servicio de asistencias que coordina varias tablas (dl, pub, out, ex): actividades de una persona en orden,
 * asistentes de una actividad, plazas ocupadas por dl y tabla en la que se guarda un asistente nuevo.
 *
 * Los demás tests del proyecto lo simulan; aquí se prueba el real con los repositorios simulados. En la sesión de
 * `myTest` mi dl es «dlb» (esquema H-dlbv, sv).
 */
final class AsistenteActividadServiceTest extends myTest
{
    private ActividadRepositoryInterface&MockObject $actividades;
    private ActividadAllRepositoryInterface&MockObject $actividadesAll;
    private AsistenteRepositoryInterface&MockObject $conjunto;
    private AsistenteRepositoryInterface&MockObject $todas;
    private PersonaFinderService&MockObject $finder;
    /** @var array<int, object> */
    private array $personas = [];
    /** @var list<array{list<string>, list<string>}> repos y filtros de cada matchingConjunto */
    private array $consultasConjunto = [];
    /** @var list<Asistente> */
    private array $asistentesConjunto = [];
    /** @var list<Asistente> */
    private array $asistentesTodas = [];
    /** @var list<string> */
    private array $filtrosTodas = [];
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'];
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->actividadesAll = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->conjunto = $this->createMock(AsistenteRepositoryInterface::class);
        $this->conjunto->method('matchingConjunto')->willReturnCallback(function (array $repos, Criteria $c): array {
            $this->consultasConjunto[] = [$repos, DescribeCriteria::filtros($c)];

            return $this->asistentesConjunto;
        });
        $this->todas = $this->createMock(AsistenteRepositoryInterface::class);
        $this->todas->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosTodas = DescribeCriteria::filtros($c);

            return $this->asistentesTodas;
        });
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (int $id): ?object => $this->personas[$id] ?? null);
    }

    public function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->sesionAnterior;
        parent::tearDown();
    }

    private function sut(): AsistenteActividadService
    {
        $factory = $this->createMock(ConnectionRepositoryFactoryInterface::class);
        $factory->method('createWithConnection')->with(AsistenteRepositoryInterface::class)->willReturn($this->conjunto);
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with(AsistenteRepositoryInterface::class)->willReturn($this->todas);

        return new AsistenteActividadService($this->actividades, $this->actividadesAll, $factory, $container, $this->finder);
    }

    private function asistente(int $idActiv, int $idNom, ?string $propietario = null, ?int $plaza = PlazaId::ASIGNADA): Asistente
    {
        $a = new Asistente();
        $a->setId_activ($idActiv);
        $a->setId_nom($idNom);
        $a->setPropietarioVo($propietario);
        $a->setPlaza($plaza);

        return $a;
    }

    private function actividad(int $id, string $dlOrg, string $idTabla = 'dl'): void
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setDl_org($dlOrg);
        $a->setId_tabla($idTabla);
        $this->actividadesAll->method('findById')->willReturnCallback(static fn (int $i): ?ActividadAll => $i === $id ? $a : null);
    }

    private function personaDl(int $id, string $apellido, string $dl = 'dlb'): PersonaDl
    {
        $p = new PersonaDl();
        $p->setId_nom($id);
        $p->setApellido1($apellido);
        $p->setNom('X');
        $p->setDl($dl);
        $this->personas[$id] = $p;

        return $p;
    }

    private const REPOS_PERSONA = [AsistenteDlRepositoryInterface::class, AsistenteOutRepositoryInterface::class, AsistenteExRepositoryInterface::class];

    // ---------------------------------------------------------------- actividades de una persona

    public function test_actividades_de_una_persona_en_orden_de_fecha_y_solo_las_del_filtro(): void
    {
        $this->actividades->method('idsConFechaInicio')->willReturn(['2026-07-01#1' => 3002, '2026-02-01#1' => 3001, '2026-09-01#1' => 3003]);
        $this->asistentesConjunto = [$this->asistente(3003, 10011), $this->asistente(3001, 10011), $this->asistente(3999, 10011)];
        $criterio = Criteria::create()->where(\src\shared\domain\criteria\Filter::eq(\src\asistentes\domain\criteria\AsistenteField::IdNom, 10011));

        $normal = $this->sut()->getActividadesDeAsistente($criterio);
        $inverso = $this->sut()->getActividadesDeAsistente($criterio, null, true);

        $this->assertSame([3001, 3003], array_map(static fn (Asistente $a): int => $a->getId_activ(), $normal));
        $this->assertSame([3003, 3001], array_map(static fn (Asistente $a): int => $a->getId_activ(), $inverso));
        $this->assertSame([self::REPOS_PERSONA, ['id_nom eq 10011']], $this->consultasConjunto[0]);
    }

    public function test_sin_actividades_en_el_filtro_no_se_buscan_asistencias(): void
    {
        $this->actividades->method('idsConFechaInicio')->willReturn([]);
        $this->conjunto->expects($this->never())->method('matchingConjunto');

        $this->assertSame([], $this->sut()->getActividadesDeAsistente(Criteria::create()));
    }

    /**
     * Regresión: «importa el orden, se queda con la primera» solo saltaba duplicados consecutivos; si la misma
     * actividad salía en dos tablas no seguidas, se quedaba con la última.
     */
    public function test_si_una_actividad_sale_en_dos_tablas_se_queda_con_la_primera(): void
    {
        $this->actividades->method('idsConFechaInicio')->willReturn(['2026-02-01#1' => 3001, '2026-07-01#1' => 3002]);
        $deDl = $this->asistente(3001, 10011);
        $deOut = $this->asistente(3001, 10011);
        $this->asistentesConjunto = [$deDl, $this->asistente(3002, 10011), $deOut];

        $r = $this->sut()->getAsistenciasPersonaDeActividades(10011, ['2026-02-01#1' => 3001, '2026-07-01#1' => 3002]);

        $this->assertCount(2, $r);
        $this->assertSame($deDl, $r[0]);
        $this->assertSame([self::REPOS_PERSONA, ['id_nom eq 10011']], $this->consultasConjunto[0]);
    }

    // ---------------------------------------------------------------- plazas ocupadas por dl

    /**
     * @return iterable<string, array{string, string, string, ?string}>
     */
    public static function fuentesPlazasProvider(): iterable
    {
        yield 'mi dl en mi actividad: todas las tablas (vista all)' => ['dlb', 'dlb', '', 'todas'];
        yield 'mi dl en actividad de otra: ex y out' => ['dlp', 'dlb', '', 'ex,out'];
        yield 'la organizadora en su propia actividad (no soy yo): nada' => ['dlp', 'dlp', '', null];
        yield 'otra dl en mi actividad: todas' => ['dlb', 'dlp', '', 'todas'];
        yield 'otra dl en actividad de una tercera: out y ex' => ['dlz', 'dlp', '', 'out,ex'];
    }

    #[DataProvider('fuentesPlazasProvider')]
    public function test_las_plazas_se_cuentan_en_las_tablas_que_tocan(string $dlOrg, string $sdl, string $hub, ?string $fuente): void
    {
        $this->actividad(3001, $dlOrg);
        if ($fuente === null) {
            $this->conjunto->expects($this->never())->method('matchingConjunto');
            $this->todas->expects($this->never())->method('matching');
        }

        $this->assertSame(0, $this->sut()->getPlazasOcupadasPorDl(3001, $sdl, $hub));

        $repos = ['ex' => AsistenteExRepositoryInterface::class, 'out' => AsistenteOutRepositoryInterface::class];
        if ($fuente === 'todas') {
            $this->assertSame(['id_activ eq 3001'], $this->filtrosTodas);
            $this->assertSame([], $this->consultasConjunto);
        } elseif ($fuente !== null) {
            $this->assertSame([array_map(static fn (string $k): string => $repos[$k], explode(',', $fuente)), ['id_activ eq 3001']], $this->consultasConjunto[0]);
        }
    }

    public function test_cuenta_las_plazas_asignadas_o_confirmadas_de_la_dl_y_su_cupo(): void
    {
        $this->actividad(3001, 'dlb');
        foreach ([10011, 10012, 10013, 10014, 10015, 10016, 10017] as $id) {
            $this->personaDl($id, 'P' . $id);
        }
        $this->asistentesTodas = [
            $this->asistente(3001, 10011, 'dlb>dlb', PlazaId::ASIGNADA),
            $this->asistente(3001, 10012, 'dlb>dlb', PlazaId::CONFIRMADA),
            $this->asistente(3001, 10012, 'dlb>dlb', PlazaId::CONFIRMADA),
            $this->asistente(3001, 10013, 'dlb>dlb', PlazaId::EN_ESPERA),
            $this->asistente(3001, 10014, 'dlb>dlb', null),
            $this->asistente(3001, 10015, 'dlb>dlp', PlazaId::ASIGNADA),
            $this->asistente(3001, 10016, 'xxx', PlazaId::ASIGNADA),
            $this->asistente(3001, 10019, 'dlb>dlb', PlazaId::ASIGNADA),
        ];

        // 10011 y 10012 (una vez); 10013 en espera, 10014 sin plaza (pedida), 10015 de otro cupo, 10016 sin
        // propietario, 10019 no existe como persona.
        $this->assertSame(2, $this->sut()->getPlazasOcupadasPorDl(3001, 'dlb'));
    }

    public function test_con_hub_solo_cuentan_las_plazas_de_ese_propietario(): void
    {
        $this->actividad(3001, 'dlb');
        $this->personaDl(10011, 'A');
        $this->personaDl(10012, 'B');
        $this->asistentesTodas = [
            $this->asistente(3001, 10011, 'dlb>dlp'),
            $this->asistente(3001, 10012, 'dlz>dlp'),
        ];

        $this->assertSame(1, $this->sut()->getPlazasOcupadasPorDl(3001, 'dlp', 'dlb'));
        $this->assertSame(2, $this->sut()->getPlazasOcupadasPorDl(3001, 'dlp'));
    }

    public function test_sin_dl_cuenta_todas_las_plazas_ocupadas(): void
    {
        $this->actividad(3001, 'dlp');
        $this->personaDl(10011, 'A');
        $this->asistentesConjunto = [$this->asistente(3001, 10011, 'dlp>dlb')];

        $this->assertSame(1, $this->sut()->getPlazasOcupadasPorDl(3001));
    }

    public function test_una_actividad_que_no_existe_no_tiene_plazas_ocupadas(): void
    {
        $this->actividadesAll->method('findById')->willReturn(null);

        $this->assertSame(0, $this->sut()->getPlazasOcupadasPorDl(3999, 'dlb'));
    }

    // ---------------------------------------------------------------- asistentes de una actividad

    public function test_en_mi_actividad_se_buscan_los_asistentes_en_las_cuatro_tablas_y_se_ordenan_sin_acentos(): void
    {
        $this->actividad(3001, 'dlb');
        $this->personaDl(10011, 'Zamora');
        $this->personaDl(10012, 'Álvarez');
        $this->personaDl(10013, 'Bosch');
        $this->asistentesConjunto = [$this->asistente(3001, 10011), $this->asistente(3001, 10012), $this->asistente(3001, 10013), $this->asistente(3001, 10019)];

        $r = $this->sut()->getAsistentesDeActividad(3001);

        $this->assertSame([[
            AsistenteDlRepositoryInterface::class,
            AsistentePubRepositoryInterface::class,
            AsistenteOutRepositoryInterface::class,
            AsistenteExRepositoryInterface::class,
        ], ['id_activ eq 3001']], $this->consultasConjunto[0]);
        $this->assertSame([10012, 10013, 10011], array_map(static fn (Asistente $a): int => $a->getId_nom(), array_values($r)));
        $this->assertStringStartsWith('Álvarez', array_key_first($r));
    }

    /**
     * Regresión: en sf, `dl_org` lleva la «f» (como `mi_delef()`); el código la quitaba y comparaba con
     * `mi_delef()` (con «f»), de modo que la propia actividad nunca se reconocía y faltaban los asistentes de las
     * tablas dl y pub. Lo introdujo el cambio mecánico de `mi_dele()` por `mi_delef()` de 2019.
     */
    public function test_en_sf_mi_propia_actividad_tambien_busca_en_las_cuatro_tablas(): void
    {
        $_SESSION['session_auth']['esquema'] = 'H-dlbf';
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->actividad(3001, 'dlbf');

        $this->sut()->getAsistentesDeActividad(3001);

        $this->assertCount(4, $this->consultasConjunto[0][0]);
    }

    /**
     * @return iterable<string, array{string, list<class-string>}>
     */
    public static function actividadAjenaProvider(): iterable
    {
        yield 'publicada por otra dl: solo out' => ['dl', [AsistenteOutRepositoryInterface::class]];
        yield 'de fuera (ex): out y ex' => ['ex', [AsistenteOutRepositoryInterface::class, AsistenteExRepositoryInterface::class]];
    }

    #[DataProvider('actividadAjenaProvider')]
    public function test_en_una_actividad_de_otra_dl_solo_se_buscan_las_tablas_de_fuera(string $idTabla, array $repos): void
    {
        $this->actividad(3001, 'dlp', $idTabla);

        $this->sut()->getAsistentesDeActividad(3001);

        $this->assertSame($repos, $this->consultasConjunto[0][0]);
    }

    /** Hallazgo: los asistentes se indexan por apellidos y nombre; dos personas que se llamen igual quedan en una. */
    public function test_dos_asistentes_con_el_mismo_nombre_quedan_en_uno(): void
    {
        $this->actividad(3001, 'dlb');
        $this->personaDl(10011, 'Pla');
        $this->personaDl(10012, 'Pla');
        $this->asistentesConjunto = [$this->asistente(3001, 10011), $this->asistente(3001, 10012)];

        $this->assertCount(1, $this->sut()->getAsistentesDeActividad(3001));
    }

    public function test_una_actividad_que_no_existe_no_tiene_asistentes(): void
    {
        $this->actividadesAll->method('findById')->willReturn(null);
        $this->conjunto->expects($this->never())->method('matchingConjunto');

        $this->assertSame([], $this->sut()->getAsistentesDeActividad(3999));
    }

    // ---------------------------------------------------------------- buscar una asistencia

    public function test_buscar_una_asistencia_en_la_vista_de_todas_las_tablas(): void
    {
        $a = $this->asistente(3001, 10011);
        $this->asistentesTodas = [$a];

        $this->assertSame($a, $this->sut()->buscarAsistencia(10011, 3001));
        $this->assertSame(['id_nom eq 10011', 'id_activ eq 3001'], $this->filtrosTodas);

        $this->asistentesTodas = [];
        $this->assertFalse($this->sut()->buscarAsistencia(10011, 3001));
    }

    // ---------------------------------------------------------------- tabla de un asistente nuevo

    public function test_los_ids_negativos_de_paso_van_a_la_tabla_ex_sin_buscar_nada(): void
    {
        $this->finder->expects($this->never())->method('findPersonaEnGlobalODePaso');

        $this->assertSame(AsistenteExRepositoryInterface::class, $this->sut()->getRepoAsistente(-1005, 3001));
        $this->assertSame(AsistenteExRepositoryInterface::class, $this->sut()->getRepoAsistente(10011, -3001));
    }

    /**
     * @return iterable<string, array{string, string, string, class-string}>
     */
    public static function tablaDestinoProvider(): iterable
    {
        yield 'persona de mi dl en mi actividad' => ['dl', 'dlb', 'dl', AsistenteDlRepositoryInterface::class];
        yield 'persona publicada de mi dl en mi actividad' => ['pub', 'dlb', 'dl', AsistentePubRepositoryInterface::class];
        yield 'persona de paso (id positivo) de mi dl en mi actividad' => ['ex', 'dlb', 'dl', AsistenteExRepositoryInterface::class];
        yield 'persona de mi dl en actividad publicada de otra dl' => ['dl', 'dlp', 'dl', AsistenteOutRepositoryInterface::class];
        yield 'persona de otra dl en mi actividad' => ['pub-otra', 'dlb', 'dl', AsistentePubRepositoryInterface::class];
        yield 'persona de paso de otra dl en mi actividad' => ['ex-otra', 'dlb', 'dl', AsistenteDlRepositoryInterface::class];
    }

    #[DataProvider('tablaDestinoProvider')]
    public function test_la_tabla_de_un_asistente_nuevo_depende_de_la_persona_y_de_la_actividad(string $persona, string $dlOrg, string $idTabla, string $repo): void
    {
        $this->personas[10011] = match ($persona) {
            'dl' => $this->personaDl(10011, 'A'),
            'pub' => self::conDl(new PersonaPub(), 'dlb'),
            'ex' => self::conDl(new PersonaEx(), 'dlb'),
            'pub-otra' => self::conDl(new PersonaPub(), 'dlp'),
            'ex-otra' => self::conDl(new PersonaEx(), 'dlp'),
        };
        $this->actividad(3001, $dlOrg, $idTabla);

        $this->assertSame($repo, $this->sut()->getRepoAsistente(10011, 3001));
    }

    private static function conDl(PersonaPub|PersonaEx $p, string $dl): PersonaPub|PersonaEx
    {
        $p->setDl($dl);

        return $p;
    }

    public function test_en_sf_la_dl_de_la_actividad_se_compara_sin_la_f(): void
    {
        $_SESSION['session_auth']['esquema'] = 'H-dlbf';
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->personaDl(10011, 'A', 'dlb');
        $this->actividad(3001, 'dlbf');

        $this->assertSame(AsistenteDlRepositoryInterface::class, $this->sut()->getRepoAsistente(10011, 3001));
    }

    public function test_una_persona_de_otra_dl_en_una_actividad_que_no_es_mia_no_se_puede_apuntar(): void
    {
        $this->personas[10011] = self::conDl(new PersonaPub(), 'dlp');
        $this->actividad(3001, 'dlz');

        $this->expectException(\RuntimeException::class);

        $this->sut()->getRepoAsistente(10011, 3001);
    }

    /**
     * Las actividades de fuera de Orbix («ex») no tienen tratamiento propio: como en el modelo antiguo
     * (`AsistentePub::getClaseAsistente`), una persona de mi dl en una actividad que no es de mi dl va a
     * `d_asistentes_out`, y una persona de otra dl no se puede apuntar. (Había unas ramas que comparaban la clase
     * con `ActividadEx`, que ya no existe; nunca se cumplían y se han quitado.)
     */
    public function test_las_actividades_de_fuera_van_como_cualquier_actividad_de_otra_dl(): void
    {
        $this->personaDl(10011, 'A');
        $this->actividad(3001, 'dlz', 'ex');

        $this->assertSame(AsistenteOutRepositoryInterface::class, $this->sut()->getRepoAsistente(10011, 3001));

        $this->personas[10012] = self::conDl(new PersonaPub(), 'dlp');
        $this->expectException(\RuntimeException::class);
        $this->sut()->getRepoAsistente(10012, 3001);
    }

    public function test_si_no_se_encuentra_la_persona_o_la_actividad_lanza_excepcion(): void
    {
        $this->actividad(3001, 'dlb');
        try {
            $this->sut()->getRepoAsistente(10019, 3001);
            $this->fail('debería lanzar');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('10019', $e->getMessage());
        }

        $this->personaDl(10011, 'A');
        $this->expectException(\RuntimeException::class);
        $this->sut()->getRepoAsistente(10011, 3999);
    }
}
