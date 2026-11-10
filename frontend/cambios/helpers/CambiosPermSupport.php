<?php

declare(strict_types=1);

namespace frontend\cambios\helpers;

use frontend\shared\session\SessionPerm;
use frontend\shared\session\PermisoOficina;

final class CambiosPermSupport
{
    public static function oPerm(): bool
    {
        return SessionPerm::isPresent();
    }

    public static function isAdmin(): bool
    {
        return SessionPerm::onlyPerm(PermisoOficina::ADMIN_SF) || SessionPerm::onlyPerm(PermisoOficina::ADMIN_SV);
    }
}
