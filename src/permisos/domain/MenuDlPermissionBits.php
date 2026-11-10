<?php

namespace src\permisos\domain;

/**
 * Definición de máscaras para permisos de menú en contexto DL (formulario y listados).
 * Extraído de {@see PermDl::omplir()} para reutilizar sin cargar {@see XPermisos} en el frontend.
 * Para visibilidad/edición de entradas de menú (valores distintos en admin_*) ver {@see \src\menus\domain\PermisoMenuBits}.
 */
final class MenuDlPermissionBits
{
    /**
     * Nombres de las oficinas/permisos de menú. Usarlos en lugar de literales en
     * `have_perm_oficina()` y `only_perm()`: un literal mal escrito no falla al compilar
     * (y un nombre desconocido deniega siempre, así que el fallo pasaría desapercibido).
     */
    public const ADL = 'adl';
    public const PR = 'pr';
    public const AGD = 'agd';
    public const AOP = 'aop';
    public const DES = 'des';
    public const EST = 'est';
    public const SCDL = 'scdl';
    public const SCR = 'scr';
    public const SG = 'sg';
    public const SM = 'sm';
    public const SOI = 'soi';
    public const SR = 'sr';
    public const VCSD = 'vcsd';
    public const VCSR = 'vcsr';
    public const DTOR = 'dtor';
    public const OCS = 'ocs';
    public const SDDL = 'sddl';
    public const NAX = 'nax';
    public const CALENDARIO = 'calendario';
    public const CTR = 'ctr';
    public const JEFE_ZONA = 'jefeZona';
    public const SACD = 'sacd';
    public const PERSONA = 'persona';
    public const CASA = 'casa';
    public const ADMIN_SF = 'admin_sf';
    public const ADMIN_SV = 'admin_sv';

    /**
     * Mismo orden y valores que históricamente en PermDl::omplir().
     *
     * @return array<string, int>
     */
    public static function map(): array
    {
        return [
            self::ADL => 1,
            self::PR => 1,
            self::AGD => 1 << 1,
            self::AOP => 1 << 2,
            self::DES => 1 << 3,
            self::EST => 1 << 4,
            self::SCDL => 1 << 5,
            self::SCR => 1 << 5,
            self::SG => 1 << 6,
            self::SM => 1 << 7,
            self::SOI => 1 << 8,
            self::SR => 1 << 9,
            self::VCSD => 1 << 10,
            self::VCSR => 1 << 10,
            self::DTOR => 1 << 11,
            self::OCS => 1 << 12,
            self::SDDL => 1 << 13,
            self::NAX => 1 << 14,
            self::CALENDARIO => 1 << 15,
            self::CTR => 1 << 16,
            self::JEFE_ZONA => 1 << 17,
            self::SACD => 1 << 18,
            self::PERSONA => 1 << 19,
            self::CASA => 1 << 20,
            self::ADMIN_SF => 16776183,
            self::ADMIN_SV => -1,
        ];
    }

    /**
     * Equivalente a {@see XPermisos::lista_txt()} con el mapa DL (una etiqueta si coincide el valor exacto).
     */
    public static function listaTxt(int $bin): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = '';
        $i = 0;
        foreach (self::map() as $nom => $num) {
            if ($bin === $num) {
                $i++;
                if ($i > 1) {
                    $txt .= ', ';
                }
                $txt .= $nom;
            }
        }

        return $txt;
    }

    /**
     * Equivalente a {@see XPermisos::lista_txt2()} con el mapa DL (subconjuntos por bit).
     */
    public static function listaTxt2(int $bin): string
    {
        if (empty($bin)) {
            $bin = 0;
        }
        $txt = '';
        $i = 0;
        foreach (self::map() as $nom => $num) {
            if (($bin & $num) === $num) {
                $i++;
                if ($i > 1) {
                    $txt .= ', ';
                }
                $txt .= $nom;
            }
        }

        return $txt;
    }
}
