<?php

declare(strict_types=1);

namespace frontend\profesores\helpers;

use src\permisos\domain\XPermisos;

final class ProfesoresPermSupport
{
    public static function oPerm(): ?XPermisos
    {
        $oPerm = $_SESSION['oPerm'] ?? null;

        return $oPerm instanceof XPermisos ? $oPerm : null;
    }
}
