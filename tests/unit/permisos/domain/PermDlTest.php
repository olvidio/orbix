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

    /**
     * Semántica a tener presente (docs/dev/revision_tests_plan.md, sección 15): `have_perm_oficina()` es «cualquiera de»
     * (comparte algún bit) y `admin_sv` vale -1 (todos los bits), así que es verdadero para CUALQUIER usuario
     * con algún permiso. Para saber si alguien es administrador hay que usar `only_perm()` (igualdad exacta): así se hace en todos los sitios desde que se corrigió.
     */
    public function test_have_perm_oficina_admin_es_verdadero_para_cualquiera_con_algun_permiso(): void
    {
        $_SESSION['iPermMenus'] = MenuDlPermissionBits::map()[MenuDlPermissionBits::AGD];

        $p = new PermDl();

        $this->assertTrue($p->have_perm_oficina(MenuDlPermissionBits::ADMIN_SV), 'un usuario solo de agd «es» admin_sv con have_perm_oficina');
        $this->assertTrue($p->have_perm_oficina(MenuDlPermissionBits::ADMIN_SF));
        $this->assertFalse($p->only_perm(MenuDlPermissionBits::ADMIN_SV), 'only_perm sí distingue');
        $this->assertFalse($p->only_perm(MenuDlPermissionBits::ADMIN_SF));
    }

    public function test_only_perm_identifica_a_los_administradores(): void
    {
        $_SESSION['iPermMenus'] = -1;
        $this->assertTrue((new PermDl())->only_perm(MenuDlPermissionBits::ADMIN_SV));
        $this->assertFalse((new PermDl())->only_perm(MenuDlPermissionBits::ADMIN_SF));

        $_SESSION['iPermMenus'] = 16776183;
        $this->assertTrue((new PermDl())->only_perm(MenuDlPermissionBits::ADMIN_SF));
        $this->assertFalse((new PermDl())->only_perm(MenuDlPermissionBits::ADMIN_SV));
    }

    public function test_usuario_sin_permisos_no_es_admin_de_ninguna_forma(): void
    {
        $_SESSION['iPermMenus'] = 0;

        $p = new PermDl();

        $this->assertFalse($p->have_perm_oficina(MenuDlPermissionBits::ADMIN_SV));
        $this->assertFalse($p->only_perm(MenuDlPermissionBits::ADMIN_SV));
    }
}

