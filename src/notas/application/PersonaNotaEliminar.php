<?php

namespace src\notas\application;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Elimina una `PersonaNota` a traves de la tabla padre `e_notas`.
 */
final class PersonaNotaEliminar
{
    public function __construct(
        private readonly PersonaNotaInputParser $personaNotaInputParser,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly DossierRepositoryInterface $dossierRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        try {
            $oPersonaNota = $this->personaNotaInputParser->parse($input, eliminar: true);
            $oEditar = new EditarPersonaNota(
                $oPersonaNota,
                $this->personaNotaRepository,
                $this->delegacionRepository,
                $this->dbSchemaRepository,
                $this->dossierRepository,
                $this->personaNotaDlRepository,
            );
            $oEditar->eliminar();

            return '';
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
