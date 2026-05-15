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
        $esquema = trim($esquema);
        if ($esquema === '') {
            return '';
        }
        // Primer guión separa región del resto (p. ej. «Cong-crCongv» → Cong + crCongv). limit=2
        // equivale a un solo guión y fija la misma regla si hubiera más guiones en el nombre.
        $a_reg = explode('-', $esquema, 2);
        $dl = $a_reg[1] ?? '';
        if ($dl === '') {
            return '';
        }
        // quita la v o la f final (sf/sv)
        $last = substr($dl, -1);
        if ($last === 'v' || $last === 'f') {
            $dl = substr($dl, 0, -1);
        }

        return $dl;
    }
}
