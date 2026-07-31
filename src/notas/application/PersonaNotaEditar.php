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
 * Edita una `PersonaNota` existente.
 *
 * @return array{error: string, mensaje: string, esquema: string}
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
            $id_asignatura_real = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura_real');
            $rta = $oEditar->editar($id_asignatura_real);
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
