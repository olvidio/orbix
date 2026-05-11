<?php

namespace src\ubis\domain;

/**
 * Máscaras de tipo de labor (mismo criterio que {@see CuadrosLabor}).
 */
final class CuadrosLaborBits
{
    /**
     * @return array<string, int> etiqueta traducida => bit
     */
    public static function labeledMap(int $miSfsv): array
    {
        $permissions = [
            _('sr') => 512,
            _('n') => 256,
            _('agd') => 128,
            _('sg') => 64,
            _('club') => 16,
            _('bachilleres') => 8,
            _('univ') => 4,
            _('jóvenes') => 2,
            _('mayores') => 1,
        ];
        if ($miSfsv === 1) {
            $permissions[_('sss+')] = 32;
        }
        if ($miSfsv === 2) {
            $permissions[_('nax')] = 32;
        }

        return $permissions;
    }
}
