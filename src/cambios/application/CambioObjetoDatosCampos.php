<?php

namespace src\cambios\application;

use src\asistentes\domain\entity\Asistente;
use src\shared\domain\DatosCampo;

/**
 * Resuelve los campos configurables de aviso por tipo de objeto.
 */
final class CambioObjetoDatosCampos
{
    /**
     * @return list<DatosCampo>
     */
    public static function forObjeto(string $objeto): array
    {
        $campos = match ($objeto) {
            'Asistente' => (new Asistente())->getDatosCampos(),
            default => [],
        };

        return array_values(array_filter(
            $campos,
            static fn (mixed $campo): bool => $campo instanceof DatosCampo
        ));
    }
}
