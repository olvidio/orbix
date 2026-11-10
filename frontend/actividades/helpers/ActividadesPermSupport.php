<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\config\OrbixRuntime;
use frontend\shared\session\SessionConfig;
use frontend\shared\session\SessionPerm;
use frontend\shared\session\SessionPermActividades;
use frontend\shared\session\PermisoOficina;

final class ActividadesPermSupport
{
    public static function oPerm(): bool
    {
        return SessionPerm::isPresent();
    }

    public static function oPermActividades(): bool
    {
        return SessionPermActividades::isPresent();
    }

    public static function permDes(): bool
    {
        return SessionPerm::havePermOficina(PermisoOficina::VCSD) || SessionPerm::havePermOficina(PermisoOficina::DES);
    }

    public static function havePermOficina(string $oficina): bool
    {
        return SessionPerm::havePermOficina($oficina);
    }

    public static function isJefeCalendario(): bool
    {
        return SessionConfig::isJefeCalendario();
    }

    public static function permJefeTipoActiv(): bool
    {
        if (self::isJefeCalendario()) {
            return true;
        }
        if (OrbixRuntime::miSfsv() === 1
            && (self::havePermOficina(PermisoOficina::DES) || self::havePermOficina(PermisoOficina::VCSD))) {
            return true;
        }

        return OrbixRuntime::miSfsv() === 2 && self::havePermOficina(PermisoOficina::ADMIN_SF);
    }
}
