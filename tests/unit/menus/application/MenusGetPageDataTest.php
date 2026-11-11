<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\menus\application\MenusGetPageData;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\PermisoMenuBits;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Datos de la pantalla de menús: listado de un grupo o formulario de un menú. */
final class MenusGetPageDataTest extends myTest
{
    private RoleRepositoryInterface&MockObject $roles;
    private MenuDbRepositoryInterface&MockObject $menus;
    private mixed $permAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->permAnterior = $_SESSION['iPermMenus'] ?? null;
        unset($_SESSION['iPermMenus']);
        $this->roles = $this->createMock(RoleRepositoryInterface::class);
        $this->roles->method('getArrayRoles')->willReturn([1 => 'admin', 2 => 'secretaria']);
        $this->menus = $this->createMock(MenuDbRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        if ($this->permAnterior === null) {
            unset($_SESSION['iPermMenus']);
        } else {
            $_SESSION['iPermMenus'] = $this->permAnterior;
        }
        parent::tearDown();
    }

    private function sut(): MenusGetPageData
    {
        return new MenusGetPageData($this->roles, $this->menus);
    }

    private function menu(int $id, bool $ok = true): MenuDb
    {
        $m = new MenuDb();
        $m->setId_menu($id);
        $m->setOrden([2, 1]);
        $m->setMenu('Personas');
        $m->setParametros('tipo=n');
        $m->setId_metamenu(40);
        $m->setMenu_perm(8);
        $m->setId_grupmenu(5);
        $m->setOk($ok);

        return $m;
    }

    // ---- listado -----------------------------------------------------------------------------------------

    public function test_sin_grupo_devuelve_el_listado_vacio_sin_consultar_menus(): void
    {
        $this->menus->expects($this->never())->method('matching');
        $this->menus->expects($this->never())->method('findById');

        $data = $this->sut()->execute([]);

        $this->assertSame('list', $data['mode']);
        $this->assertSame([], $data['menu_rows']);
        $this->assertSame('', $data['filtro_grupo']);
        $this->assertSame([1 => 'admin', 2 => 'secretaria'], $data['aRoles']);
        $this->assertSame(PermisoMenuBits::map(), $data['perm_menu_bit_map']);
    }

    public function test_con_grupo_lista_sus_menus_por_orden(): void
    {
        $descripcion = [];
        $this->menus->expects($this->once())->method('matching')->willReturnCallback(function (Criteria $c) use (&$descripcion): array {
            $descripcion = [DescribeCriteria::filtros($c), DescribeCriteria::orden($c)];

            return [$this->menu(11), $this->menu(12, false)];
        });

        $data = $this->sut()->execute(['filtro_grupo' => '5']);

        $this->assertSame(['id_grupmenu eq 5'], $descripcion[0]);
        $this->assertCount(1, $descripcion[1]);
        $this->assertStringStartsWith('orden ', $descripcion[1][0]);
        $this->assertSame('list', $data['mode']);
        $this->assertSame('5', $data['filtro_grupo']);
        $this->assertSame([
            'id_menu' => 11,
            'orden' => [2, 1],
            'menu' => 'Personas',
            'parametros' => 'tipo=n',
            'id_metamenu' => 40,
            'menu_perm' => 8,
            'id_grupmenu' => 5,
            'ok' => true,
        ], $data['menu_rows'][0]);
        $this->assertSame([11, 12], array_column($data['menu_rows'], 'id_menu'));
        $this->assertFalse($data['menu_rows'][1]['ok']);
    }

    // ---- permiso del usuario -----------------------------------------------------------------------------

    /**
     * @dataProvider permisosProvider
     */
    public function test_el_permiso_de_menus_sale_de_la_sesion_y_por_defecto_es_1(mixed $enSesion, int $esperado): void
    {
        if ($enSesion !== null) {
            $_SESSION['iPermMenus'] = $enSesion;
        }

        $this->assertSame(['i_perm_menus' => $esperado], $this->sut()->execute([])['usuario']);
    }

    /** @return array<string, array{mixed, int}> */
    public static function permisosProvider(): array
    {
        return [
            'sin sesión' => [null, 1],
            'entero' => [136, 136],
            'texto numérico' => ['24', 24],
            'no numérico' => ['todos', 1],
        ];
    }

    // ---- formulario de un menú existente -----------------------------------------------------------------

    public function test_editar_devuelve_los_datos_del_menu_y_las_capsulas_de_cada_accion(): void
    {
        $this->menus->method('findById')->with(11)->willReturn($this->menu(11));
        $this->menus->expects($this->never())->method('matching');

        $data = $this->sut()->execute(['id_menu' => '11', 'filtro_grupo' => '5']);

        $this->assertSame('edit', $data['mode']);
        $this->assertSame('5', $data['filtro_grupo']);
        $this->assertSame('11', $data['id_menu']);
        $this->assertSame('', $data['nuevo']);
        $this->assertSame('2,1', $data['orden_txt']);
        $this->assertSame('Personas', $data['menu']);
        $this->assertSame('tipo=n', $data['parametros']);
        $this->assertSame(40, $data['id_metamenu']);
        $this->assertSame(8, $data['menu_perm']);
        $this->assertSame(5, $data['id_grupmenu']);
        $this->assertSame('ok', $data['campos_chk']);
        $this->assertArrayHasKey('aRoles', $data);
        $this->assertArrayHasKey('usuario', $data);
        $this->assertArrayHasKey('perm_menu_bit_map', $data);
        foreach (['guardar', 'eliminar', 'copiar', 'mover'] as $accion) {
            $this->assertSame(['id_menu' => 11], HashB::open($data['ctx_' . $accion], 'menu_' . $accion), $accion);
        }
    }

    public function test_la_casilla_ok_sale_marcada_solo_si_el_menu_esta_ok(): void
    {
        $this->menus->method('findById')->willReturnCallback(fn (int $id): MenuDb => $this->menu($id, $id === 11));

        $this->assertStringContainsString("name='ok' checked >", $this->sut()->execute(['id_menu' => '11'])['txt_ok']);
        $this->assertStringNotContainsString('checked', $this->sut()->execute(['id_menu' => '12'])['txt_ok']);
    }

    public function test_un_menu_sin_orden_ni_textos_devuelve_cadenas_vacias(): void
    {
        $m = new MenuDb();
        $m->setId_menu(11);
        $this->menus->method('findById')->willReturn($m);

        $data = $this->sut()->execute(['id_menu' => '11']);

        $this->assertSame(['', '', ''], [$data['orden_txt'], $data['menu'], $data['parametros']]);
        $this->assertNull($data['id_metamenu']);
        $this->assertNull($data['id_grupmenu']);
    }

    public function test_un_menu_que_no_existe_lanza_excepcion(): void
    {
        $this->menus->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->sut()->execute(['id_menu' => '99']);
    }

    // ---- formulario de alta ------------------------------------------------------------------------------

    public function test_nuevo_devuelve_el_formulario_en_blanco_con_capsula_solo_para_guardar(): void
    {
        $this->menus->expects($this->never())->method('findById');

        $data = $this->sut()->execute(['nuevo' => '1', 'filtro_grupo' => '5']);

        $this->assertSame('edit', $data['mode']);
        $this->assertSame('', $data['id_menu']);
        $this->assertSame('1', $data['nuevo']);
        $this->assertSame(['', '', '', 0], [$data['orden_txt'], $data['menu'], $data['parametros'], $data['menu_perm']]);
        $this->assertNull($data['id_metamenu']);
        $this->assertNull($data['id_grupmenu'], 'hallazgo: el alta no hereda el grupo que se estaba listando');
        $this->assertStringNotContainsString('checked', $data['txt_ok']);
        $this->assertSame(['id_menu' => 0], HashB::open($data['ctx_guardar'], 'menu_guardar'));
        $this->assertArrayNotHasKey('ctx_eliminar', $data);
        $this->assertArrayNotHasKey('ctx_copiar', $data);
        $this->assertArrayNotHasKey('ctx_mover', $data);
    }

    public function test_si_llegan_id_y_nuevo_a_la_vez_manda_el_id(): void
    {
        $this->menus->expects($this->once())->method('findById')->with(11)->willReturn($this->menu(11));

        $data = $this->sut()->execute(['id_menu' => '11', 'nuevo' => '1']);

        $this->assertSame('Personas', $data['menu']);
        $this->assertArrayHasKey('ctx_eliminar', $data);
    }

    /** Hallazgo: un id no numérico se convierte en 0 y se busca el menú 0 en vez de rechazar la entrada. */
    public function test_un_id_no_numerico_se_busca_como_menu_cero(): void
    {
        $this->menus->expects($this->once())->method('findById')->with(0)->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->sut()->execute(['id_menu' => 'abc']);
    }
}
