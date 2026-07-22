<?php

namespace src\notas\application;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Crea una nueva `PersonaNota`. Thin wrapper sobre `EditarPersonaNota::nuevo()`.
 *
 * @return array{error: string, mensaje: string, esquema: string}
 */
final class PersonaNotaNueva
{
    public function __construct(
        private readonly PersonaNotaInputParser $personaNotaInputParser,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly DossierRepositoryInterface $dossierRepository,
        private readonly PersonaNotaDlRepositoryInterface $personaNotaDlRepository,
        private readonly MapaPrefijoActaEsquemaRepositoryInterface $mapaPrefijoActaEsquemaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string, mensaje: string, esquema: string}
     */
    public function execute(array $input): array
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
                $this->mapaPrefijoActaEsquemaRepository,
            );
            $rta = $oEditar->nuevo();
            $esquema = (string) ($rta['esquema'] ?? $oEditar->getEsquemaEscritura());

            return [
                'error' => '',
                'mensaje' => sprintf(_("Nota guardada en el esquema %s"), $esquema),
                'esquema' => $esquema,
            ];
        } catch (\RuntimeException $e) {
            return [
                'error' => $e->getMessage(),
                'mensaje' => '',
                'esquema' => '',
            ];
        }
    }
}
