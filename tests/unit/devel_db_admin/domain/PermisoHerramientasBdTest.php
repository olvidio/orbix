<?php

declare(strict_types=1);

namespace Tests\unit\devel_db_admin\domain;

use PHPUnit\Framework\TestCase;
use src\devel_db_admin\domain\PermisoHerramientasBd;
use src\menus\domain\PermisoMenuBits;

/**
 * Quién puede usar las herramientas de base de datos: los mismos que las ven en el menú (permiso de menú
 * `admin_sv`; las migraciones, también `admin_sf`, como la entrada «actualizar DB» de sf).
 */
final class PermisoHerramientasBdTest extends TestCase
{
    private static function bit(string $nombre): int
    {
        return (int) PermisoMenuBits::map()[$nombre];
    }

    public function test_admin_sv_puede_todo(): void
    {
        $perm = self::bit('admin_sv') | self::bit('sm');

        $this->assertTrue(PermisoHerramientasBd::puedeEsquemas($perm));
        $this->assertTrue(PermisoHerramientasBd::puedeMigraciones($perm));
    }

    public function test_admin_sf_solo_las_migraciones(): void
    {
        $perm = self::bit('admin_sf');

        $this->assertFalse(PermisoHerramientasBd::puedeEsquemas($perm));
        $this->assertTrue(PermisoHerramientasBd::puedeMigraciones($perm));
    }

    public function test_una_oficina_sin_administracion_no_puede_nada(): void
    {
        $perm = self::bit('sm') | self::bit('pffe') | self::bit('casa') | self::bit('jefeZona');

        $this->assertFalse(PermisoHerramientasBd::puedeEsquemas($perm));
        $this->assertFalse(PermisoHerramientasBd::puedeMigraciones($perm));
        $this->assertFalse(PermisoHerramientasBd::puedeEsquemas(0));
    }
}
