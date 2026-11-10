<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use Tests\myTest;

final class PermDlTest extends myTest
{
    /** @var mixed */
    private mixed $sesionAnterior;
    private bool $habiaSesion;

    public function setUp(): void
    {
        parent::setUp();
        $this->habiaSesion = array_key_exists('iPermMenus', $_SESSION);
        $this->sesionAnterior = $_SESSION['iPermMenus'] ?? null;
    }

    public function tearDown(): void
    {
        if ($this->habiaSesion) {
            $_SESSION['iPermMenus'] = $this->sesionAnterior;
        } else {
            unset($_SESSION['iPermMenus']);
        }
        parent::tearDown();
    }

    public function test_usa_el_mapa_de_bits_de_menu_dl(): void
    {
        $_SESSION['iPermMenus'] = 0;

        $this->assertSame(MenuDlPermissionBits::map(), (new PermDl())->getPermissions());
        $this->assertSame('PermDl', (new PermDl())->classname);
    }

    public function test_toma_el_acceso_de_la_sesion(): void
    {
        $_SESSION['iPermMenus'] = 2 | 4; // agd + aop

        $p = new PermDl();

        $this->assertTrue($p->have_perm_oficina('agd'));
        $this->assertTrue($p->have_perm_oficina('aop'));
        $this->assertFalse($p->have_perm_oficina('sg'));
        $this->assertTrue($p->have_perm_bit(2));
        $this->assertFalse($p->have_perm_bit(1 << 6));
    }

    public function test_acceso_como_string_numerico(): void
    {
        $_SESSION['iPermMenus'] = '4';

        $this->assertTrue((new PermDl())->have_perm_oficina('aop'));
    }

    /**
     * @dataProvider sesionSinAccesoProvider
     */
    public function test_sin_acceso_valido_en_sesion_no_concede_nada(mixed $valor): void
    {
        $_SESSION['iPermMenus'] = $valor;

        $p = new PermDl();

        foreach (array_keys(MenuDlPermissionBits::map()) as $oficina) {
            $this->assertFalse($p->have_perm_oficina($oficina), $oficina);
        }
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function sesionSinAccesoProvider(): iterable
    {
        yield 'cero' => [0];
        yield 'null' => [null];
        yield 'texto no numérico' => ['admin'];
        yield 'array' => [[1]];
    }

    public function test_sin_clave_en_sesion_no_concede_nada(): void
    {
        unset($_SESSION['iPermMenus']);

        $this->assertFalse((new PermDl())->have_perm_oficina('agd'));
    }

    public function test_admin_sv_tiene_acceso_a_todas_las_oficinas(): void
    {
        $_SESSION['iPermMenus'] = -1;

        $p = new PermDl();

        foreach (array_keys(MenuDlPermissionBits::map()) as $oficina) {
            $this->assertTrue($p->have_perm_oficina($oficina), $oficina);
        }
    }

    public function test_oficinas_con_alias_comparten_el_permiso(): void
    {
        $_SESSION['iPermMenus'] = 1 << 5; // scdl / scr

        $p = new PermDl();

        $this->assertTrue($p->have_perm_oficina('scdl'));
        $this->assertTrue($p->have_perm_oficina('scr'));
        $this->assertTrue($p->have_perm_oficina('scdl,sg'));
    }
}
