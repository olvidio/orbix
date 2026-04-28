<?php

declare(strict_types=1);

namespace src\asignaturas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

final class AsignaturasConSeparadorOpcionesData
{
    /**
     * @return array{a_opciones: array}
     */
    public static function execute(bool $op_genericas = true): array
    {
        $repo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

        return ['a_opciones' => $repo->getArrayAsignaturasConSeparador($op_genericas)];
    }
}
