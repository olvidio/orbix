<?php

declare(strict_types=1);

namespace frontend\profesores\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class ProfesoresPostInput
{
    /**
     * @return array{id_nom: int, id_tabla: string}
     */
    public static function idFromSelPost(): array
    {
        $aSelRaw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (is_array($aSelRaw) && $aSelRaw !== []) {
            $sel0 = $aSelRaw[0];
            if (is_string($sel0) && $sel0 !== '') {
                $parts = explode('#', $sel0, 2);

                return [
                    'id_nom' => is_numeric($parts[0]) ? (int) $parts[0] : 0,
                    'id_tabla' => $parts[1] ?? '',
                ];
            }
        }

        $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
        $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
        $idNom = is_int($idNomRaw) ? $idNomRaw : 0;
        $idPau = is_int($idPauRaw) ? $idPauRaw : 0;

        return [
            'id_nom' => $idNom !== 0 ? $idNom : $idPau,
            'id_tabla' => \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'id_tabla')),
        ];
    }
}
