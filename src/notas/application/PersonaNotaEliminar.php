<?php

namespace src\notas\application;

use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\notas\application\support\ActaFirmadaPolicy;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
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
        private readonly ActaFirmadaPolicy $firmadaPolicy,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        try {
            $oPersonaNota = $this->personaNotaInputParser->parse($input, eliminar: true);
            $firmada = $this->mensajeSiNoSePuedeEliminar($oPersonaNota);
            if ($firmada !== '') {
                return $firmada;
            }
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

    /**
     * El POST de borrar (listado) no trae `acta`. Hay que leer la nota persistida.
     * «Examinado» no cierra acta: se puede borrar aunque coincida un PDF firmado.
     */
    private function mensajeSiNoSePuedeEliminar(PersonaNota $oPersonaNota): string
    {
        $existente = $this->personaNotaRepository->findById(
            $oPersonaNota->getId_nom(),
            $oPersonaNota->getId_nivel(),
            $oPersonaNota->getTipo_acta() ?? TipoActa::FORMATO_ACTA,
        );
        if (!$existente instanceof PersonaNota) {
            return '';
        }
        if ($existente->getId_situacion() === NotaSituacion::EXAMINADO) {
            return '';
        }

        return $this->firmadaPolicy->mensajeSiFirmada($existente->getActa());
    }
}
