<?php

namespace src\ubis\application\services;

final class DelegacionUtils
{
    /**
     * Extrae la sigla de dl a partir del nombre de esquema.
     * Paridad con legacy: zzzGestorDelegacionOld::getDlFromSchema()
     */
    public static function getDlFromSchema(string $esquema): string
    {
        $a_reg = explode('-', $esquema);
        $dl = $a_reg[1] ?? '';
        if ($dl === '') { return ''; }
        // quita la v o la f final (sf/sv)
        $last = substr($dl, -1);
        if ($last === 'v' || $last === 'f') {
            $dl = substr($dl, 0, -1);
        }
        return $dl;
    }
}
