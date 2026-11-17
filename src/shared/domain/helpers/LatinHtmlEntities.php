<?php

declare(strict_types=1);

namespace src\shared\domain\helpers;

/**
 * Digrafos latinos → entidades HTML para impresiones / PDF (actas, certificados, tesseras).
 * Equivalente histórico a `Config::$replace`.
 */
final class LatinHtmlEntities
{
    /** @var array<string, string> */
    private const REPLACE = [
        'AE' => '&#0198;',
        'Ae' => '&#0198;',
        'ae' => '&#0230;',
        'aE' => '&#0230;',
        'OE' => '&#0338;',
        'Oe' => '&#0338;',
        'oe' => '&#0339;',
        'oE' => '&#0339;',
    ];

    /**
     * @return array<string, string>
     */
    public static function replaceMap(): array
    {
        return self::REPLACE;
    }
}
