<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;

/**
 * Opciones del desplegable de colecciones (`lista_colecciones.php`).
 *
 * @return array{a_opciones: mixed}
 */
final class ColeccionesOpcionesData
{
    public static function build(): array
    {
        $repo = $GLOBALS['container']->get(ColeccionRepositoryInterface::class);

        return [
            'a_opciones' => $repo->getArrayColecciones(),
        ];
    }
}
