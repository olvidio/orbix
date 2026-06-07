<?php

namespace src\certificados\application;

use src\personas\application\services\PersonaFinderService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\RegionStgrAviso;

/**
 * Datos para el formulario “adjuntar certificado emitido” (solo lectura inicial).
 */
final class CertificadoEmitidoAdjuntarFormData
{
    public function __construct(
        private readonly PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @return array{nom: string, f_enviado: string}
     */
    public function execute(int $id_nom): array
    {
        if ($id_nom <= 0) {
            throw new \RuntimeException(RegionStgrAviso::mensajePersonaNoValida());
        }
        $oPersona = $this->personaFinderService->findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            throw new \RuntimeException(_('persona no encontrada'));
        }

        return [
            'nom' => $oPersona->getApellidosNombre(),
            'f_enviado' => (new DateTimeLocal())->getFromLocal(),
        ];
    }
}
