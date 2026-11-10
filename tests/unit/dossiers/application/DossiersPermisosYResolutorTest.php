<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use src\dossiers\application\DossierVerDatosTablaInfoClassResolver;
use src\dossiers\application\PermDossiersListaData;
use src\dossiers\application\PermisoDossier;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\profesores\domain\InfoProfesorStgr;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Permisos de dossier, lista de tipos para la pantalla de permisos y resolución de la clase de datos de un tipo. */
final class DossiersPermisosYResolutorTest extends myTest
{
    private mixed $permMenusAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->permMenusAnterior = $_SESSION['iPermMenus'] ?? null;
    }

    public function tearDown(): void
    {
        if ($this->permMenusAnterior === null) {
            unset($_SESSION['iPermMenus']);
        } else {
            $_SESSION['iPermMenus'] = $this->permMenusAnterior;
        }
        parent::tearDown();
    }

    // ---- PermisoDossier ----------------------------------------------------------------------

    public function test_los_permisos_de_dossier_se_construyen_con_la_acion_de_sesion_o_cero(): void
    {
        $_SESSION['iPermMenus'] = '5';
        $conSesion = new PermisoDossier();
        unset($_SESSION['iPermMenus']);
        $sinSesion = new PermisoDossier();
        $_SESSION['iPermMenus'] = 'x';
        $invalida = new PermisoDossier();

        $this->assertInstanceOf(\src\permisos\domain\XPermisos::class, $conSesion);
        $this->assertNotSame([], (new \ReflectionProperty($conSesion, 'permissions'))->getValue($conSesion));
        $this->assertSame(0, (new \ReflectionProperty($sinSesion, 'iaccion'))->getValue($sinSesion));
        $this->assertSame(0, (new \ReflectionProperty($invalida, 'iaccion'))->getValue($invalida));
        $this->assertSame(5, (new \ReflectionProperty($conSesion, 'iaccion'))->getValue($conSesion));
    }

    // ---- PermDossiersListaData ----------------------------------------------------------------------

    private function tipo(int $id, string $descripcion, bool $depende): TipoDossier
    {
        $t = $this->createMock(TipoDossier::class);
        $t->method('getId_tipo_dossier')->willReturn($id);
        $t->method('getDescripcion')->willReturn($descripcion);
        $t->method('isDepende_modificar')->willReturn($depende);

        return $t;
    }

    public function test_cada_tipo_de_dossier_es_una_fila_con_su_enlace_a_la_pantalla_de_permisos(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $filtros = [];
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros): array {
            $filtros = [DescribeCriteria::filtros($c), DescribeCriteria::orden($c)];

            return [$this->tipo(1001, 'datos personales', true), $this->tipo(1002, 'vacío', false)];
        });

        $r = (new PermDossiersListaData($repo))->build('p');

        $this->assertSame(['tabla_from eq p'], $filtros[0]);
        $this->assertSame(['id_tipo_dossier asc'], $filtros[1]);
        $this->assertCount(2, $r['a_filas']);
        $this->assertSame('datos personales', $r['a_filas'][0]['descripcion']);
        $this->assertSame('frontend/dossiers/controller/perm_dossier_ver.php', $r['a_filas'][0]['pagina_link_spec']['path']);
        $this->assertSame(['id_tipo_dossier' => 1001, 'depende_modificar' => true, 'tipo' => 'p'], $r['a_filas'][0]['pagina_link_spec']['query']);
        $this->assertFalse($r['a_filas'][1]['pagina_link_spec']['query']['depende_modificar']);
    }

    public function test_sin_tipos_la_lista_esta_vacia(): void
    {
        $repo = $this->createMock(TipoDossierRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $this->assertSame(['a_filas' => []], (new PermDossiersListaData($repo))->build('u'));
    }

    // ---- DossierVerDatosTablaInfoClassResolver ----------------------------------------------------------------------

    private function tipoConApp(int $id, ?string $app, ?string $clase): TipoDossier
    {
        $t = $this->createMock(TipoDossier::class);
        $t->method('getId_tipo_dossier')->willReturn($id);
        $t->method('getApp')->willReturn($app);
        $t->method('getClass')->willReturn($clase);

        return $t;
    }

    public function test_los_tipos_de_profesor_tienen_su_propia_clase_aunque_compartan_class(): void
    {
        $this->assertSame(InfoProfesorStgr::class, DossierVerDatosTablaInfoClassResolver::tryResolveFullyQualifiedClassName($this->tipoConApp(1018, 'profesores', 'Profesor')));
    }

    public function test_el_resto_se_deduce_de_app_y_clase_y_solo_si_existe(): void
    {
        $this->assertNull(DossierVerDatosTablaInfoClassResolver::tryResolveFullyQualifiedClassName($this->tipoConApp(9999, 'modulo', 'NoExiste')));
        $this->assertNull(DossierVerDatosTablaInfoClassResolver::tryResolveFullyQualifiedClassName($this->tipoConApp(9999, null, 'X')));
        $this->assertNull(DossierVerDatosTablaInfoClassResolver::tryResolveFullyQualifiedClassName($this->tipoConApp(9999, 'a', '')));
    }

    public function test_resolver_sin_clase_lanza_excepcion_con_el_id(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('9999');
        DossierVerDatosTablaInfoClassResolver::resolveFullyQualifiedClassName($this->tipoConApp(9999, 'modulo', 'NoExiste'));
    }
}
