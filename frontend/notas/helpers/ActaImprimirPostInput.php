<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

final class ActaImprimirPostInput
{
    public static function actaFromPost(): string
    {
        $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (is_array($a_sel_raw) && $a_sel_raw !== []) {
            $sel0 = $a_sel_raw[0];
            if (is_string($sel0) && $sel0 !== '') {
                $parts = explode('#', $sel0, 2);

                return urldecode($parts[0]);
            }

            return '';
        }
        $qacta = filter_input(INPUT_POST, 'acta');
        if (is_string($qacta) && $qacta !== '') {
            return urldecode($qacta);
        }

        return '';
    }

    public static function caraFromPost(): string
    {
        $qcara = filter_input(INPUT_POST, 'cara');
        if (is_string($qcara) && $qcara !== '') {
            return $qcara;
        }

        return 'A';
    }

    public static function actaFromRequest(): string
    {
        $actaGet = filter_input(INPUT_GET, 'acta');
        if (is_string($actaGet) && $actaGet !== '') {
            return urldecode($actaGet);
        }

        return '';
    }
}
