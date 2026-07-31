<?php

declare(strict_types=1);

namespace frontend\cambios\helpers;

use frontend\shared\session\SessionPerm;

final class CambiosPermSupport
{
    public static function oPerm(): bool
    {
        return SessionPerm::isPresent();
    }

    public static function isAdmin(): bool
    {
        return SessionPerm::onlyPerm('admin_sf') || SessionPerm::onlyPerm('admin_sv');
    }
}
