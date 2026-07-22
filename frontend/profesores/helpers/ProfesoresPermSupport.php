<?php

declare(strict_types=1);

namespace frontend\profesores\helpers;

use frontend\shared\session\SessionPerm;

final class ProfesoresPermSupport
{
    public static function oPerm(): bool
    {
        return SessionPerm::isPresent();
    }

    public static function havePermOficina(string $oficina): bool
    {
        return SessionPerm::havePermOficina($oficina);
    }
}
