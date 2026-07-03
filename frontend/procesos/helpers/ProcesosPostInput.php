<?php

declare(strict_types=1);

namespace frontend\procesos\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class ProcesosPostInput
{
public static function postString(string $name, string $default = ''): string
{
    return PayloadCoercion::string(filter_input(INPUT_POST, $name), $default);
}

public static function postInt(string $name, int $default = 0): int
{
    $raw = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT);

    return is_int($raw) ? $raw : $default;
}

/**
 * @return array{id: int, id_item: string, id_usuario: int, id_tipo_activ_txt: string, dl_propia: string}
 */
public static function selTokensFromPost(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!is_array($a_sel_raw) || $a_sel_raw === []) {
        return [
            'id' => self::postInt('id_activ'),
            'id_item' => '',
            'id_usuario' => self::postInt('id_usuario'),
            'id_tipo_activ_txt' => self::postString('id_tipo_activ_txt'),
            'dl_propia' => self::postString('dl_propia'),
        ];
    }
    $sel0 = $a_sel_raw[0];
    if (!is_string($sel0) || $sel0 === '') {
        return [
            'id' => self::postInt('id_activ'),
            'id_item' => '',
            'id_usuario' => self::postInt('id_usuario'),
            'id_tipo_activ_txt' => self::postString('id_tipo_activ_txt'),
            'dl_propia' => self::postString('dl_propia'),
        ];
    }
    $parts = explode('#', $sel0, 4);
    $id0 = $parts[0];

    return [
        'id' => is_numeric($id0) ? (int) $id0 : 0,
        'id_item' => PayloadCoercion::string($parts[1] ?? ''),
        'id_usuario' => is_numeric($id0) ? (int) $id0 : 0,
        'id_tipo_activ_txt' => PayloadCoercion::string($parts[2] ?? ''),
        'dl_propia' => PayloadCoercion::string($parts[3] ?? ''),
    ];
}

}
