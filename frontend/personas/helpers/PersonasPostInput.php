<?php

declare(strict_types=1);

namespace frontend\personas\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class PersonasPostInput
{
    /**
     * @return array{id_nom: int, id_tabla: string}
     */
    public static function idFromSelPost(): array
    {
        $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (is_array($a_sel_raw) && $a_sel_raw !== []) {
            $sel0 = $a_sel_raw[0];
            if (is_string($sel0) && $sel0 !== '') {
                $parts = explode('#', $sel0, 2);

                return [
                    'id_nom' => is_numeric($parts[0]) ? (int) $parts[0] : 0,
                    'id_tabla' => $parts[1] ?? '',
                ];
            }
        }

        $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

        return [
            'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
            'id_tabla' => \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'id_tabla')),
        ];
    }

    /**
     * @return array{id_pau: int}
     */
    public static function idPauFromSelPost(): array
    {
        $ids = self::idFromSelPost();
        $idPauRaw = filter_input(INPUT_POST, 'id_pau', FILTER_VALIDATE_INT);
        $idPau = is_int($idPauRaw) ? $idPauRaw : 0;

        return [
            'id_pau' => $ids['id_nom'] !== 0 ? $ids['id_nom'] : $idPau,
        ];
    }

    public static function sessionGoToSetTabla(string $objPau): void
    {
        if (!isset($_SESSION['session_go_to']) || !is_array($_SESSION['session_go_to'])) {
            return;
        }
        if (!isset($_SESSION['session_go_to']['sel']) || !is_array($_SESSION['session_go_to']['sel'])) {
            return;
        }
        $_SESSION['session_go_to']['sel']['tabla'] = $objPau;
    }

    public static function posicionIntParam(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }
}
