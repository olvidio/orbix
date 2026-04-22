<?php

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

/**
 * Autocomplete de asignaturas por nombre. Devuelve el JSON (como
 * cadena) que el repositorio prepara para jQuery-UI autocomplete.
 */
final class AsignaturasSearchData
{
    public static function execute(array $input): string
    {
        $search = (string)($input['search'] ?? '');
        $repo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        return (string)$repo->getJsonAsignaturas(['nombre_asignatura' => $search]);
    }
}
