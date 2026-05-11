<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;

/**
 * Opciones del desplegable de tipos de documento (`lista_tipo_doc.php`).
 *
 * @return array{a_opciones: mixed}
 */
final class TipoDocOpcionesData
{
    public static function build(): array
    {
        $repo = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);

        return [
            'a_opciones' => $repo->getArrayTipoDoc(),
        ];
    }
}
