<?php

declare(strict_types=1);

namespace src\shared\infrastructure\ui\http;

use src\shared\domain\PermisoRevisarAyuda;

/**
 * Comprobación de permiso de los controladores de revisión de las consultas a la ayuda. Va en el
 * controlador y no en `routes.php` porque `PostRequest` despacha los controladores en el mismo
 * proceso sin pasar por las rutas. Lee el permiso de menú de la sesión (`iPermMenus`).
 */
final class PermisoRevisarAyudaGuard
{
    public static function permite(): bool
    {
        $perm = $_SESSION['iPermMenus'] ?? 0;

        return PermisoRevisarAyuda::puede(is_int($perm) ? $perm : (is_numeric($perm) ? (int) $perm : 0));
    }
}
