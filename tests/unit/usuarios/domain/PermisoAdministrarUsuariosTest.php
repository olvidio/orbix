<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\domain;

use PHPUnit\Framework\TestCase;
use src\usuarios\domain\PermisoAdministrarUsuarios;

/**
 * Quién puede administrar usuarios, grupos, permisos y roles (revisión de seguridad del 2026-11-19).
 */
final class PermisoAdministrarUsuariosTest extends TestCase
{
    public function test_solo_los_roles_de_administracion_administran_usuarios(): void
    {
        $this->assertTrue(PermisoAdministrarUsuarios::puedeAdministrar(1));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeAdministrar(2));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeAdministrar(3));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeAdministrar(4));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeAdministrar(6));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeAdministrar(0));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeAdministrar(-1));
    }

    public function test_los_roles_solo_los_crea_modifica_o_borra_el_superadministrador(): void
    {
        $this->assertTrue(PermisoAdministrarUsuarios::puedeModificarRoles(1));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeModificarRoles(2));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeModificarRoles(3));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeModificarRoles(6));
    }

    public function test_los_grupos_de_menu_de_un_rol_los_cambian_el_superadministrador_y_el_administrador(): void
    {
        $this->assertTrue(PermisoAdministrarUsuarios::puedeCambiarGruposDeMenuDeRol(1));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeCambiarGruposDeMenuDeRol(2));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeCambiarGruposDeMenuDeRol(3));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeCambiarGruposDeMenuDeRol(6));
    }

    public function test_a_un_superadministrador_solo_lo_toca_otro_superadministrador(): void
    {
        $this->assertTrue(PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(1, 1));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(2, 1));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(2, 2));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(2, 6));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(2, null));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeTocarUsuarioConRol(6, 6));
    }

    public function test_el_rol_de_superadministrador_solo_lo_asigna_un_superadministrador(): void
    {
        $this->assertTrue(PermisoAdministrarUsuarios::puedeAsignarRol(1, 1));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeAsignarRol(2, 1));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeAsignarRol(2, 2));
        $this->assertTrue(PermisoAdministrarUsuarios::puedeAsignarRol(2, 6));
        $this->assertFalse(PermisoAdministrarUsuarios::puedeAsignarRol(6, 6));
    }
}
