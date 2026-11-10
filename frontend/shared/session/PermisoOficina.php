<?php

declare(strict_types=1);

namespace frontend\shared\session;

use src\permisos\domain\MenuDlPermissionBits;

/**
 * Nombres de oficina/permiso de menú para `SessionPerm::havePermOficina()` y similares.
 *
 * Cada constante apunta a la de {@see MenuDlPermissionBits}: no se pueden desincronizar y
 * un nombre mal escrito (`PermisoOficina::DESS`) falla al ejecutar en lugar de denegar en silencio.
 * Es, junto a {@see SessionPerm}, el único sitio del frontend que conoce `src\permisos`.
 */
final class PermisoOficina
{
    public const ADL = MenuDlPermissionBits::ADL;
    public const PR = MenuDlPermissionBits::PR;
    public const AGD = MenuDlPermissionBits::AGD;
    public const AOP = MenuDlPermissionBits::AOP;
    public const DES = MenuDlPermissionBits::DES;
    public const EST = MenuDlPermissionBits::EST;
    public const SCDL = MenuDlPermissionBits::SCDL;
    public const SCR = MenuDlPermissionBits::SCR;
    public const SG = MenuDlPermissionBits::SG;
    public const SM = MenuDlPermissionBits::SM;
    public const SOI = MenuDlPermissionBits::SOI;
    public const SR = MenuDlPermissionBits::SR;
    public const VCSD = MenuDlPermissionBits::VCSD;
    public const VCSR = MenuDlPermissionBits::VCSR;
    public const DTOR = MenuDlPermissionBits::DTOR;
    public const OCS = MenuDlPermissionBits::OCS;
    public const SDDL = MenuDlPermissionBits::SDDL;
    public const NAX = MenuDlPermissionBits::NAX;
    public const CALENDARIO = MenuDlPermissionBits::CALENDARIO;
    public const CTR = MenuDlPermissionBits::CTR;
    public const JEFE_ZONA = MenuDlPermissionBits::JEFE_ZONA;
    public const SACD = MenuDlPermissionBits::SACD;
    public const PERSONA = MenuDlPermissionBits::PERSONA;
    public const CASA = MenuDlPermissionBits::CASA;
    public const ADMIN_SF = MenuDlPermissionBits::ADMIN_SF;
    public const ADMIN_SV = MenuDlPermissionBits::ADMIN_SV;
}
