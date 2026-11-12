<?php

declare(strict_types=1);

namespace Tests\unit\menus\application;

use PHPUnit\Framework\TestCase;
use src\menus\application\MenusBurgerLayoutDataUseCase;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\entity\MetaMenu;
use Tests\support\DescribeCriteria;

/**
 * Estructura del menú «burger»: árbol de entradas por grupo según el orden
 * (1, 1.1, 1.1.1…), solo grupos del usuario, sin las de módulos no instalados
 * ni las de metamenús inexistentes; enlace o acción de cliente; menú de utilidades.
 */
final class MenusBurgerLayoutDataUseCaseTest extends TestCase
{
    private mixed $config = null;

    protected function setUp(): void
    {
        $this->config = $_SESSION['config'] ?? null;
        $_SESSION['config'] = ['mod_installed' => [7 => true]];
    }

    protected function tearDown(): void
    {
        $_SESSION['config'] = $this->config;
    }

    private static function menu(int $grupo, array $orden, string $nombre, ?int $meta = null, ?string $param = null): MenuDb
    {
        $m = new MenuDb();
        $m->setId_menu(random_int(1, 99999));
        $m->setId_grupmenu($grupo);
        $m->setOrden($orden);
        $m->setMenu($nombre);
        $m->setId_metamenu($meta);
        $m->setParametros($param);
        return $m;
    }

    /** @param list<MenuDb> $utilidades @param list<MenuDb> $resto */
    private function uc(array $utilidades, array $resto): MenusBurgerLayoutDataUseCase
    {
        $menus = $this->createMock(MenuDbRepositoryInterface::class);
        $menus->method('matching')->willReturnCallback(fn ($c): array => in_array('id_grupmenu eq 1', DescribeCriteria::filtros($c), true) ? $utilidades : $resto);
        $metas = $this->createMock(MetaMenuRepositoryInterface::class);
        $metas->method('findById')->willReturnCallback(static function (int $id): ?MetaMenu {
            $urls = [10 => ['frontend/personas/controller/personas_que.php', null], 11 => ['fnjs_algo()', null], 12 => ['frontend/x.php', 99], 13 => ['frontend/y.php', 7]];
            if (!isset($urls[$id])) {
                return null;
            }
            $m = new MetaMenu();
            $m->setUrl($urls[$id][0]);
            $m->setId_mod($urls[$id][1]);
            return $m;
        });
        return new MenusBurgerLayoutDataUseCase($menus, $metas);
    }

    public function test_arbol_por_grupos_con_enlaces_y_acciones(): void
    {
        $out = ($this->uc([], [
            self::menu(2, [1], 'Personas'),
            self::menu(2, [1, 1], 'Buscar', 10, 'tabla=n'),
            self::menu(2, [1, 2], 'Accion', 11),
            self::menu(2, [1, 2, 1], 'Nieto', 10),
            self::menu(2, [2], 'Otro de modulo instalado', 13),
            self::menu(3, [1], 'De un grupo que no tengo'),
        ]))([2 => 'personas']);

        $this->assertSame(['personas'], array_keys($out['menu_config']));
        $raices = $out['menu_config']['personas'];
        $this->assertSame(['Personas', 'Otro de modulo instalado'], array_column($raices, 'name'));
        $hijos = $raices[0]['submenu'];
        $this->assertSame(['Buscar', 'Accion'], array_column($hijos, 'name'));
        $this->assertSame(['path' => 'frontend/personas/controller/personas_que.php', 'parametros' => 'tabla=n'], $hijos[0]['link_spec']);
        $this->assertSame([null, 'fnjs_algo()'], [$hijos[1]['link_spec'], $hijos[1]['client_action']]);
        $this->assertSame(['Nieto'], array_column($hijos[1]['submenu'], 'name'));
    }

    public function test_quita_las_de_modulos_no_instalados_y_metamenus_inexistentes(): void
    {
        $out = ($this->uc([], [
            self::menu(2, [1], 'Modulo no instalado', 12),
            self::menu(2, [2], 'Metamenu borrado', 999),
            self::menu(2, [3], 'Bien', 10),
            self::menu(2, [], 'Sin orden', 10),
        ]))([2 => 'personas']);
        $this->assertSame(['Bien'], array_column($out['menu_config']['personas'], 'name'));
    }

    public function test_menu_de_utilidades_del_usuario(): void
    {
        $out = ($this->uc([
            self::menu(1, [1], 'Utilidades'),
            self::menu(1, [1, 1], 'Preferencias', 10),
            self::menu(1, [1, 1, 1], 'Sub', 10),
            self::menu(1, [1, 2], 'Borrado', 999),
        ], []))([]);
        $this->assertSame(['Preferencias', 'Sub'], array_column($out['user_menu_nodes'], 'name'), 'sin la raíz ni los de metamenú inexistente');
        $this->assertSame([2, 3], array_column($out['user_menu_nodes'], 'indice'));
        $this->assertSame(['Sub'], array_column($out['user_menu_nodes'][0]['submenu'], 'name'));
        $this->assertSame([], $out['menu_config']);
    }
}
