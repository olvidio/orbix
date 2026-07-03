<?php

declare(strict_types=1);

namespace frontend\cambios\helpers;

use src\permisos\domain\XPermisos;

final class CambiosPermSupport
{
    public static function oPerm(): ?XPermisos
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof XPermisos ? $oPerm : null;
    }

    public static function isAdmin(): bool
    {
        $oPerm = self::oPerm();
        if ($oPerm === null) {
            return false;
        }

        return $oPerm->only_perm('admin_sf') || $oPerm->only_perm('admin_sv');
    }
}
