<?php

declare(strict_types=1);

namespace src\personas\domain;

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Orden alfabético de personas por primer apellido, después segundo apellido y
 * después nombre (sin acentos), independiente de cómo se muestre el nombre
 * (preferencia «Nombre Apellidos» / «Apellidos, Nombre»).
 */
final class OrdenPorApellidos
{
    /**
     * @return array{0: string, 1: string, 2: string}
     */
    public static function campos(object $oPersona): array
    {
        return [
            method_exists($oPersona, 'getApellido1') ? (string) $oPersona->getApellido1() : '',
            method_exists($oPersona, 'getApellido2') ? (string) $oPersona->getApellido2() : '',
            method_exists($oPersona, 'getNom') ? (string) $oPersona->getNom() : '',
        ];
    }

    /**
     * @param array{0: string, 1: string, 2: string} $a
     * @param array{0: string, 1: string, 2: string} $b
     */
    public static function comparar(array $a, array $b): int
    {
        for ($i = 0; $i < 3; $i++) {
            $cmp = FuncTablasSupport::strsinacentocmp(trim($a[$i]), trim($b[$i]));
            if ($cmp !== 0) {
                return $cmp;
            }
        }

        return 0;
    }
}
