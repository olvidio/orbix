<?php

declare(strict_types=1);

namespace src\devel_db_admin\domain;

/**
 * Siglas de región y dl, y nombres de esquema, que llegan de los formularios de las herramientas de
 * esquemas. Con ellas se montan nombres de esquema y de rol (`"$region-$dl"`) y literales de DDL que se
 * ejecutan con el usuario de mantenimiento: solo se admiten los caracteres de las siglas reales
 * (letras, dígitos, «_» y «+», como `crs+`; el esquema añade el «-» que separa región y dl).
 */
final class SiglasEsquema
{
    private const SIGLA = '/^[A-Za-z0-9_+]+$/';

    private const ESQUEMA = '/^[A-Za-z0-9_+-]+$/';

    public static function validarSiglas(string $region, string $dl): void
    {
        if (preg_match(self::SIGLA, $region) !== 1 || preg_match(self::SIGLA, $dl) !== 1) {
            throw new \InvalidArgumentException(_("Región o delegación no válida: solo letras, dígitos, «_» y «+»."));
        }
    }

    public static function validarEsquema(string $esquema): void
    {
        if (preg_match(self::ESQUEMA, $esquema) !== 1) {
            throw new \InvalidArgumentException(_("Nombre de esquema no válido."));
        }
    }
}
