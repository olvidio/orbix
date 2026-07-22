<?php

declare(strict_types=1);

namespace frontend\shared\session;

/**
 * Acceso tipado a `$_SESSION['oPerm']` sin `use src\...` en callers.
 * Único punto del frontend que conoce {@see \src\permisos\domain\XPermisos}.
 */
final class SessionPerm
{
    public static function isPresent(): bool
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof \src\permisos\domain\XPermisos;
    }

    private static function perm(): ?\src\permisos\domain\XPermisos
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof \src\permisos\domain\XPermisos ? $oPerm : null;
    }

    public static function havePermOficina(string $oficina): bool
    {
        return self::perm()?->have_perm_oficina($oficina) ?? false;
    }

    public static function onlyPerm(string $permiso): bool
    {
        return self::perm()?->only_perm($permiso) ?? false;
    }

    public static function havePermActiv(string $permiso): bool
    {
        return self::perm()?->have_perm_activ($permiso) ?? false;
    }

    /**
     * Objeto de sesión crudo (solo para bridges legacy que aún lo requieren).
     * Preferir métodos tipados de esta clase.
     */
    public static function raw(): ?object
    {
        return self::perm();
    }
}
