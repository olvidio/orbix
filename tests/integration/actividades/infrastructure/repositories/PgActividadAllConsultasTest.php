<?php

declare(strict_types=1);

namespace Tests\integration\actividades\infrastructure\persistence\postgresql;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\permisos\domain\PermisosActividades;
use src\procesos\domain\PermAccion;
use src\procesos\domain\PermAccionBits;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use Tests\myTest;

/**
 * Consultas de PgActividadAllRepository fuera del CRUD: coincidencia con la otra
 * sección, ubis de una selección, desplegables por tipo y de estudios, y las
 * actividades de una casa para el planning.
 *
 * Las actividades de prueba usan ids 99800001..99800020 y fechas en 2091–2092
 * (no hay datos reales); se borran antes y después de cada test.
 */
final class PgActividadAllConsultasTest extends myTest
{
    private const ID_BASE = 99800000;
    private const UBI = 99800123;

    private ActividadAllRepositoryInterface $repo;
    private mixed $permAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $this->permAnterior = $_SESSION['oPermActividades'] ?? null;
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        $_SESSION['oPermActividades'] = $this->permAnterior;
        parent::tearDown();
    }

    private function limpiar(): void
    {
        foreach ($this->repo->matching(Criteria::create()->where(Filter::between(ActividadAllField::IdActiv, self::ID_BASE + 1, self::ID_BASE + 20))) as $a) {
            $this->repo->Eliminar($a, false);
        }
    }

    private function crear(int $n, int $tipo, string $ini, string $fin, ?int $ubi = self::UBI, string $nom = '', int $status = StatusId::ACTUAL, ?int $nivel = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ(self::ID_BASE + $n);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ($nom !== '' ? $nom : 'test consultas ' . $n);
        $a->setStatus($status);
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setDl_org($tipo >= 200000 ? 'dlbf' : 'dlb');
        $a->setId_tabla('dl');
        $a->setId_ubi($ubi);
        $a->setNivel_stgr($nivel);
        $this->assertTrue($this->repo->Guardar($a, false));
        return $a;
    }

    // --- getCoincidencia ---

    public function test_coincidencia_con_una_actividad_de_la_otra_seccion_con_un_dia_de_tolerancia(): void
    {
        $this->crear(1, 241000, '2091-03-10', '2091-03-13');
        $sv = $this->crear(2, 141000, '2091-03-11', '2091-03-14');
        $this->assertTrue($this->repo->getCoincidencia($sv));

        $lejos = $this->crear(3, 141000, '2091-03-12', '2091-03-13');
        $this->assertFalse($this->repo->getCoincidencia($lejos), 'dos días de diferencia en el inicio');
    }

    public function test_coincidencia_solo_cuenta_tipos_4_y_5_de_la_otra_seccion(): void
    {
        $this->crear(1, 231000, '2091-03-10', '2091-03-13');
        $this->crear(2, 141000, '2091-03-10', '2091-03-13');
        $sv = $this->crear(3, 151000, '2091-03-10', '2091-03-13');
        // La de sf es de tipo 3 y la otra de tipo 4 es de la misma sección.
        $this->assertFalse($this->repo->getCoincidencia($sv));

        $sf = $this->crear(4, 251000, '2091-03-10', '2091-03-13');
        $this->assertTrue($this->repo->getCoincidencia($sf), 'desde sf mira las de sv');
    }

    public function test_coincidencia_no_modifica_las_fechas_de_la_actividad(): void
    {
        $sv = $this->crear(1, 141000, '2091-03-11', '2091-03-14');
        $this->repo->getCoincidencia($sv);
        $this->assertSame('2091-03-11', $sv->getF_ini()->format('Y-m-d'));
        $this->assertSame('2091-03-14', $sv->getF_fin()->format('Y-m-d'));
    }

    // --- getUbis ---

    public function test_ubis_de_la_seleccion_ordenadas_y_sin_repetir(): void
    {
        $this->crear(1, 141000, '2091-05-01', '2091-05-02', self::UBI + 1);
        $this->crear(2, 141000, '2091-05-01', '2091-05-02', self::UBI);
        $this->crear(3, 141000, '2091-05-01', '2091-05-02', self::UBI + 1);
        $this->crear(4, 141000, '2091-05-01', '2091-05-02', null);
        $ubis = $this->repo->getUbis(Criteria::create()
            ->where(Filter::between(ActividadAllField::IdActiv, self::ID_BASE + 1, self::ID_BASE + 20))
            ->orderBy(ActividadAllField::NomActiv));
        $this->assertSame([self::UBI, self::UBI + 1, null], $ubis);
    }

    // --- getArrayActividadesDeTipo ---

    public function test_actividades_de_tipo_con_condicion_ordenadas_por_fecha(): void
    {
        $this->crear(1, 141000, '2091-06-10', '2091-06-12', nom: 'zz test tipo b');
        $this->crear(2, 141000, '2091-06-01', '2091-06-03', nom: 'zz test tipo a');
        $this->crear(3, 151000, '2091-06-01', '2091-06-03', nom: 'zz test tipo c');
        $res = $this->repo->getArrayActividadesDeTipo('^141', "AND id_activ BETWEEN " . (self::ID_BASE + 1) . " AND " . (self::ID_BASE + 20));
        $this->assertSame([self::ID_BASE + 2 => 'zz test tipo a', self::ID_BASE + 1 => 'zz test tipo b'], $res);
    }

    public function test_actividades_de_tipo_no_interpreta_el_tipo_como_sql(): void
    {
        $this->crear(1, 141000, '2091-06-10', '2091-06-12');
        $res = $this->repo->getArrayActividadesDeTipo("^9' OR '1'='1", "AND id_activ BETWEEN " . (self::ID_BASE + 1) . " AND " . (self::ID_BASE + 20));
        $this->assertSame([], $res);
    }

    // --- getArrayActividadesEstudios ---

    public function test_actividades_de_estudios_solo_niveles_de_estudios_y_recientes(): void
    {
        $this->crear(1, 112021, '2091-07-01', '2091-07-20', nom: 'test est cuadrienio', nivel: 2);
        $this->crear(2, 112021, '2091-07-01', '2091-07-20', nom: 'test est repaso', nivel: 11);
        $this->crear(3, 112021, '2091-07-01', '2091-07-20', nom: 'test est otro', nivel: 7);
        $this->crear(4, 112021, '2001-07-01', '2001-07-20', nom: 'test est antiguo', nivel: 2);
        $res = $this->repo->getArrayActividadesEstudios();
        $this->assertSame('test est cuadrienio', $res[self::ID_BASE + 1] ?? null);
        $this->assertSame('test est repaso', $res[self::ID_BASE + 2] ?? null);
        $this->assertArrayNotHasKey(self::ID_BASE + 3, $res);
        $this->assertArrayNotHasKey(self::ID_BASE + 4, $res);
    }

    // --- actividadesDeUnaCasa ---

    /** @param list<string> $permisos */
    private function permisos(array $permisos): void
    {
        $accion = $this->createMock(PermAccion::class);
        $accion->method('have_perm_activ')->willReturnCallback(fn (string $p): bool => in_array($p, $permisos, true));
        $perm = $this->createMock(PermisosActividades::class);
        $perm->method('getPermisoActual')->willReturn($accion);
        $_SESSION['oPermActividades'] = $perm;
    }

    public function test_actividades_de_una_casa_con_permiso_de_ver_y_modificar(): void
    {
        $a = $this->crear(1, 112021, '2091-09-05', '2091-09-08', nom: 'ca de prueba');
        $a->setH_ini(TimeLocal::fromString('20:00:00'));
        $a->setH_fin(TimeLocal::fromString('10:30:00'));
        $this->repo->Guardar($a, false);
        $this->crear(2, 112021, '2091-09-05', '2091-09-08', status: StatusId::BORRABLE);
        $this->crear(3, 112021, '2091-10-05', '2091-10-08');
        $this->permisos([PermAccionBits::OCUPADO, PermAccionBits::VER, PermAccionBits::MODIFICAR]);

        $res = $this->repo->actividadesDeUnaCasa(self::UBI, new DateTimeLocal('2091-09-01'), new DateTimeLocal('2091-09-30'));

        $this->assertCount(1, $res);
        $fila = $res[0];
        $this->assertSame('ca de prueba', $fila['nom_llarg']);
        $this->assertSame(self::ID_BASE + 1, $fila['id_activ']);
        $this->assertSame(112021, $fila['id_tipo_activ']);
        $this->assertSame((new DateTimeLocal('2091-09-05'))->getFromLocal(), $fila['f_ini']);
        $this->assertSame('20:00', $fila['h_ini']);
        $this->assertSame((new DateTimeLocal('2091-09-08'))->getFromLocal(), $fila['f_fi']);
        $this->assertSame('10:30', $fila['h_fi']);
        $this->assertSame('programas/actividad_ver.php', $fila['pagina']);
        $this->assertNotSame('', $fila['nom_curt']);
        $this->assertNotSame('', $fila['css']);
    }

    public function test_actividad_que_empieza_antes_del_periodo_se_recorta_al_inicio(): void
    {
        $this->crear(1, 112021, '2091-08-25', '2091-09-03');
        $this->permisos([PermAccionBits::OCUPADO, PermAccionBits::VER]);
        $ini = new DateTimeLocal('2091-09-01');
        $res = $this->repo->actividadesDeUnaCasa(self::UBI, $ini, new DateTimeLocal('2091-09-30'));
        $this->assertSame($ini->getFromLocal(), $res[0]['f_ini']);
        $this->assertSame('1:16', $res[0]['h_ini']);
        $this->assertSame('', $res[0]['pagina'], 'sin permiso de modificar');
    }

    public function test_solo_ocupado_oculta_el_nombre(): void
    {
        $this->crear(1, 112021, '2091-09-05', '2091-09-08', nom: 'nombre secreto');
        $this->permisos([PermAccionBits::OCUPADO]);
        $res = $this->repo->actividadesDeUnaCasa(self::UBI, new DateTimeLocal('2091-09-01'), new DateTimeLocal('2091-09-30'));
        $this->assertStringNotContainsString('nombre secreto', $res[0]['nom_llarg']);
        $this->assertStringContainsString($res[0]['nom_curt'], $res[0]['nom_llarg']);
    }

    public function test_sin_permiso_ni_de_ocupado_no_sale_ninguna(): void
    {
        $this->crear(1, 112021, '2091-09-05', '2091-09-08');
        $this->permisos([]);
        $this->assertSame([], $this->repo->actividadesDeUnaCasa(self::UBI, new DateTimeLocal('2091-09-01'), new DateTimeLocal('2091-09-30')));
    }

    public function test_lugar_sin_determinar_filtra_por_seccion(): void
    {
        $this->crear(1, 112021, '2092-09-05', '2092-09-08', ubi: 1, nom: 'sv sin lugar');
        $this->crear(2, 212021, '2092-09-05', '2092-09-08', ubi: 1, nom: 'sf sin lugar');
        $this->permisos([PermAccionBits::OCUPADO, PermAccionBits::VER]);
        $ini = new DateTimeLocal('2092-09-01');
        $fin = new DateTimeLocal('2092-09-30');
        $this->assertSame(['sv sin lugar'], array_column($this->repo->actividadesDeUnaCasa(1, $ini, $fin, 11), 'nom_llarg'));
        $this->assertSame(['sf sin lugar'], array_column($this->repo->actividadesDeUnaCasa(1, $ini, $fin, 12), 'nom_llarg'));
        $todas = array_column($this->repo->actividadesDeUnaCasa(1, $ini, $fin), 'nom_llarg');
        sort($todas);
        $this->assertSame(['sf sin lugar', 'sv sin lugar'], $todas);
    }
}
