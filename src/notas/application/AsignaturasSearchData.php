<?php

namespace src\notas\application;

use function src\shared\domain\helpers\input_string;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;

/**
 * Autocomplete de asignaturas por nombre. Devuelve el JSON (como
 * cadena) que el repositorio prepara para jQuery-UI autocomplete.
 */
final class AsignaturasSearchData
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $search = input_string($input, 'search');
        $repo = $this->asignaturaRepository;
        return (string)$repo->getJsonAsignaturas(['nombre_asignatura' => $search]);
    }
}
