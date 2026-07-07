<?php

namespace src\cambios\application;

use src\cambios\domain\AvisoObjetoCatalog;
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
        if ($objeto === '') {
            return [];
        }

        $class = AvisoObjetoCatalog::getFullPathObj($objeto);
        if ($class === '' || !class_exists($class)) {
            return [];
        }

        $instance = new $class();
        if (!method_exists($instance, 'getDatosCampos')) {
            return [];
        }

        $raw = $instance->getDatosCampos();
        if (!is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $item) {
            if ($item instanceof DatosCampo) {
                $out[] = $item;
            }
        }

        return $out;
    }
}
