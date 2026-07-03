<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class NotasPostInput
{
public static function checkedIdsFromPost(mixed $raw): array
{
    if (!is_array($raw)) {
        return [];
    }
    $out = [];
    foreach ($raw as $id) {
        if (is_int($id) || is_string($id)) {
            $out[] = $id;
        }
    }

    return $out;
}

public static function personaFromSelPost(): array
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);
            $idNomRaw = $parts[0];
            $idTabla = $parts[1] ?? '';

            return [
                'id_nom' => is_numeric($idNomRaw) ? (int) $idNomRaw : 0,
                'id_tabla' => $idTabla,
            ];
        }
    }

    $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

    return [
        'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
        'id_tabla' => PayloadCoercion::string(filter_input(INPUT_POST, 'id_tabla')),
    ];
}
}
