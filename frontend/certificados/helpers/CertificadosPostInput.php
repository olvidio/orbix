<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

final class CertificadosPostInput
{
    public static function idItemFromSelPost(): int
    {
        $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (is_array($a_sel_raw) && $a_sel_raw !== []) {
            $sel0 = $a_sel_raw[0];
            if (is_string($sel0) && $sel0 !== '') {
                $parts = explode('#', $sel0, 2);

                return is_numeric($parts[0]) ? (int) $parts[0] : 0;
            }
        }
        $idRaw = filter_input(INPUT_POST, 'id_item', FILTER_VALIDATE_INT);

        return is_int($idRaw) ? $idRaw : 0;
    }

    /**
     * Extrae id_nom de un valor sel/id_sel (id_nom, id_nom#id_tabla, #id_nom, checked#id_nom).
     */
    public static function parseIdNomFromSelValue(mixed $raw): int
    {
        if (!is_scalar($raw)) {
            return 0;
        }
        $s = trim((string) $raw);
        if ($s === '') {
            return 0;
        }
        if (str_starts_with($s, 'checked#')) {
            $s = substr($s, 8);
        }
        if (str_starts_with($s, '#')) {
            $s = substr($s, 1);
        }
        $parts = explode('#', $s, 2);
        if (isset($parts[1]) && $parts[1] !== '' && is_numeric($parts[0])) {
            return (int) $parts[0];
        }
        if (is_numeric($parts[0])) {
            return (int) $parts[0];
        }
        if (isset($parts[1]) && is_numeric($parts[1])) {
            return (int) $parts[1];
        }

        return 0;
    }

    public static function idNomFromSelPost(): int
    {
        $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (is_array($a_sel_raw) && $a_sel_raw !== []) {
            $idNom = self::parseIdNomFromSelValue($a_sel_raw[0]);
            if ($idNom > 0) {
                return $idNom;
            }
        }

        $idNom = self::parseIdNomFromSelValue($_POST['id_sel'] ?? null);
        if ($idNom > 0) {
            return $idNom;
        }

        $idRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

        return is_int($idRaw) && $idRaw > 0 ? $idRaw : 0;
    }
}
