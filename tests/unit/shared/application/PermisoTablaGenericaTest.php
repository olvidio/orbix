<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\dossiers\domain\entity\TipoDossier;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\menus\domain\PermisoMenuBits;
use src\shared\application\PermisoTablaGenerica;

/**
 * Quién puede modificar una tabla de la pantalla genérica (`tablaDB`): la misma regla con la que se muestra
 * (revisión de seguridad del 2026-11-18). Tablas de catálogo: el permiso de menú de la entrada que la abre;
 * tablas de dossier (la clase declara `getId_dossier()`): el permiso del dossier (lectura y escritura); en ningún
 * menú ni dossier: admin_sv.
 */
final class PermisoTablaGenericaTest extends TestCase
{
    private const CATALOGO = 'src\\ubis\\domain\\InfoTipoCtr';

    /** @var array<string, mixed>|null */
    private ?array $sesionPrevia = null;

    protected function setUp(): void
    {
        $this->sesionPrevia = $_SESSION ?? null;
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        if ($this->sesionPrevia === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->sesionPrevia;
        }
    }

    private static function bit(string $nombre): int
    {
        return PermisoMenuBits::map()[$nombre];
    }

    private function usuario(string ...$permisos): void
    {
        $perm = 0;
        foreach ($permisos as $p) {
            $perm |= self::bit($p);
        }
        $_SESSION['iPermMenus'] = $perm;
    }

    private function servicio(): PermisoTablaGenerica
    {
        $menu = $this->createStub(MenuDb::class);
        $menu->method('getParametros')->willReturn('clase_info=' . self::CATALOGO);
        $menu->method('getMenu_perm')->willReturn(self::bit('scdl'));
        $otro = $this->createStub(MenuDb::class);
        $otro->method('getParametros')->willReturn('clase_info=src\\ubis\\domain\\InfoRegiones&x=1');
        $otro->method('getMenu_perm')->willReturn(self::bit('admin_sv'));
        $menus = $this->createStub(MenuDbRepositoryInterface::class);
        $menus->method('matching')->willReturn([$menu, $otro]);

        $tipo = $this->createStub(TipoDossier::class);
        $tipo->method('getId_tipo_dossier')->willReturn(1004);
        $tipo->method('getTabla_from')->willReturn('p');
        $tipo->method('getPermiso_lectura')->willReturn(self::bit('sg'));
        $tipo->method('getPermiso_escritura')->willReturn(self::bit('sm'));
        $tipo->method('isDepende_modificar')->willReturn(false);
        $tipos = $this->createStub(TipoDossierRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(fn (int $id): ?TipoDossier => $id === 1004 ? $tipo : null);

        return new PermisoTablaGenerica($menus, $tipos);
    }

    public function test_catalogo_con_el_permiso_de_su_menu(): void
    {
        $this->usuario('scdl');

        $this->assertTrue($this->servicio()->puedeModificar(self::CATALOGO, null, 0));
    }

    public function test_catalogo_sin_el_permiso_de_su_menu(): void
    {
        $this->usuario('sm', 'pffe');

        $this->assertFalse($this->servicio()->puedeModificar(self::CATALOGO, null, 0));
        $this->assertFalse($this->servicio()->puedeModificar('src\\ubis\\domain\\InfoRegiones', null, 0));
    }

    /** La clase puede llegar codificada para URL y con la barra inicial. */
    public function test_la_clase_codificada_cuenta_igual(): void
    {
        $this->usuario('scdl');

        $this->assertTrue($this->servicio()->puedeModificar(urlencode('\\' . self::CATALOGO), null, 0));
    }

    public function test_tabla_de_dossier_necesita_escritura(): void
    {
        $this->usuario('sm');
        $this->assertTrue($this->servicio()->puedeModificar('src\\personas\\domain\\InfoTraslado', 1004, 10011));

        $this->usuario('sg');
        $this->assertFalse($this->servicio()->puedeModificar('src\\personas\\domain\\InfoTraslado', 1004, 10011), 'solo lectura');
    }

    public function test_tabla_que_no_esta_en_ningun_menu_ni_dossier_solo_admin_sv(): void
    {
        $this->usuario('scdl', 'sm');
        $this->assertFalse($this->servicio()->puedeModificar('src\\usuarios\\domain\\InfoLocales', null, 0));

        $this->usuario('admin_sv');
        $this->assertTrue($this->servicio()->puedeModificar('src\\usuarios\\domain\\InfoLocales', null, 0));
    }

    /** Un dossier que no existe no da permiso. */
    public function test_dossier_desconocido_no_da_permiso(): void
    {
        $this->usuario('sm', 'sg');

        $this->assertFalse($this->servicio()->puedeModificar('src\\personas\\domain\\InfoTraslado', 9999, 10011));
    }

    public function test_sin_permisos_en_la_sesion_no_puede_nada(): void
    {
        $this->assertFalse($this->servicio()->puedeModificar(self::CATALOGO, null, 0));
        $this->assertFalse($this->servicio()->puedeModificar('src\\personas\\domain\\InfoTraslado', 1004, 10011));
    }
}
