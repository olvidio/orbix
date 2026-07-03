<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class NotasFormSupport
{
public static function formScalar(mixed $value): int|string
{
    if (is_int($value)) {
        return $value;
    }
    if (is_string($value)) {
        return $value;
    }
    if (is_bool($value)) {
        return $value ? '1' : '';
    }
    if (is_float($value)) {
        return (string) $value;
    }

    return '';
}

public static function formBoolOrString(mixed $value): bool|string
{
    if (is_bool($value)) {
        return $value;
    }

    return PayloadCoercion::string($value);
}

public static function desplegableOpciones(mixed $raw): array
{
    if (!is_array($raw) || $raw === []) {
        return is_array($raw) ? [] : [];
    }
    if (array_is_list($raw) && is_array($raw[0])) {
        $out = [];
        foreach ($raw as $pair) {
            if (!is_array($pair) || count($pair) < 2) {
                continue;
            }
            $key = is_int($pair[0]) ? $pair[0] : PayloadCoercion::string($pair[0]);
            $out[$key] = PayloadCoercion::string($pair[1]);
        }

        return $out;
    }
    $out = [];
    foreach ($raw as $key => $value) {
        if (is_int($key)) {
            $out[$key] = PayloadCoercion::string($value);
        } elseif (is_string($key)) {
            $out[$key] = PayloadCoercion::string($value);
        }
    }

    return $out;
}
}
