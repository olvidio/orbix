<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\config\OrbixRuntime;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;

final class ActividadesPermSupport
{
public static function oPerm(): ?XPermisos
{
    $oPerm = $_SESSION['oPerm'] ?? null;

    return $oPerm instanceof XPermisos ? $oPerm : null;
}

public static function oPermActividades(): ?PermisosActividades
{
    $oPerm = $_SESSION['oPermActividades'] ?? null;

    return $oPerm instanceof PermisosActividades ? $oPerm : null;
}

public static function permDes(): bool
{
    $oPerm = self::oPerm();
    if ($oPerm === null) {
        return false;
    }

    return $oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des');
}

public static function havePermOficina(string $oficina): bool
{
    $oPerm = self::oPerm();

    return $oPerm !== null && $oPerm->have_perm_oficina($oficina);
}

public static function isJefeCalendario(): bool
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot && $oConfig->is_jefeCalendario();
}

public static function permJefeTipoActiv(): bool
{
    if (self::isJefeCalendario()) {
        return true;
    }
    if (OrbixRuntime::miSfsv() === 1
        && (self::havePermOficina('des') || self::havePermOficina('vcsd'))) {
        return true;
    }

    return OrbixRuntime::miSfsv() === 2 && self::havePermOficina('admin_sf');
}
}
