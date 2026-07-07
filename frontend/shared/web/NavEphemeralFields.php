<?php

declare(strict_types=1);

namespace frontend\shared\web;

/**
 * Campos que no forman parte de identity/state de la pila de navegación v2.
 */
final class NavEphemeralFields
{
    /** @var list<string> */
    public const NAMES = [
        'h',
        'hh',
        'hhc',
        'hpos',
        'stack',
        'Gstack',
        'PHPSESSID',
        'nav',
        'submit',
        'nav_patch',
    ];

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function strip(array $data): array
    {
        foreach (self::NAMES as $name) {
            unset($data[$name]);
        }

        return $data;
    }
}
