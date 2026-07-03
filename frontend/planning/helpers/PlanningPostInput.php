<?php

declare(strict_types=1);

namespace frontend\planning\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class PlanningPostInput
{
public static function postString(string $name, string $default = ''): string
{
    return \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, $name), $default);
}

public static function postInt(string $name, int $default = 0): int
{
    $raw = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : $default;
}

/**
 * @return list<string>
 */
public static function postStringList(string $name): array
{
    if (!isset($_POST[$name]) || !is_array($_POST[$name])) {
        return [];
    }
    $out = [];
    foreach ($_POST[$name] as $item) {
        if (is_string($item) && $item !== '') {
            $out[] = $item;
        } elseif (is_int($item) || is_float($item)) {
            $out[] = (string) $item;
        }
    }

    return $out;
}

/**
 * Personas seleccionadas en listas SlickGrid (planning persona, etc.).
 * Prioriza `sSeleccionados` (csv) porque PostRequest/HashFront pierden arrays `sel[]`.
 *
 * @return list<string>
 */
public static function collectSelFromPost(): array
{
    $csv = self::postString('sSeleccionados');
    if ($csv !== '') {
        return array_values(array_filter(
            array_map('trim', explode(',', $csv)),
            static fn (string $v): bool => $v !== ''
        ));
    }

    return self::postStringList('sel');
}
}
