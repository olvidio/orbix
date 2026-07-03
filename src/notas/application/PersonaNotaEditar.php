<?php

namespace src\notas\application;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Edita una `PersonaNota` existente. Ataca siempre a la tabla padre
 * `e_notas` (la clase `EditarPersonaNota` ya resuelve el repositorio
 * correcto en funcion del tipo de acta).
 */
final class PersonaNotaEditar
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
            $oPersonaNota = $this->personaNotaInputParser->parse($input);
            $oEditar = new EditarPersonaNota(
                $oPersonaNota,
                $this->personaNotaRepository,
                $this->delegacionRepository,
                $this->dbSchemaRepository,
                $this->dossierRepository,
                $this->personaNotaDlRepository,
            );
            $id_asignatura_real = FuncTablasSupport::inputInt($input, 'id_asignatura_real');
            $oEditar->editar($id_asignatura_real);

            return '';
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
