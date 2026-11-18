<?php

declare(strict_types=1);

namespace src\devel_db_admin\infrastructure\ui\http;

use src\devel_db_admin\domain\PermisoHerramientasBd;

/**
 * Comprobación de permiso al principio de cada controlador de `src/devel_db_admin`. Va en el controlador y
 * no en `routes.php` porque `PostRequest` despacha los controladores en el mismo proceso sin pasar por las
 * rutas. Lee el permiso de menú de la sesión (`iPermMenus`), el mismo con el que se pinta el menú.
 */
final class PermisoHerramientasBdGuard
{
    public static function permiteEsquemas(): bool
    {
        return PermisoHerramientasBd::puedeEsquemas(self::permisoMenu());
    }

    public static function permiteMigraciones(): bool
    {
        return PermisoHerramientasBd::puedeMigraciones(self::permisoMenu());
    }

    private static function permisoMenu(): int
    {
        $perm = $_SESSION['iPermMenus'] ?? 0;

        return is_int($perm) ? $perm : (is_numeric($perm) ? (int) $perm : 0);
    }
}
