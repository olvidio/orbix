<?php

namespace src\certificados\application;

use src\personas\application\services\PersonaFinderService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\ubis\domain\RegionStgrAviso;

/**
 * Datos para el formulario «adjuntar certificado recibido» (solo lectura inicial).
 */
final class CertificadoRecibidoAdjuntarFormData
{
    public function __construct(
        private readonly PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @return array{nom: string, f_recibido: string, ctx_guardar: string}
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
            'f_recibido' => (new DateTimeLocal())->getFromLocal(),
            'ctx_guardar' => HashB::sign('certificado_recibido_guardar', [
                'nuevo' => 1,
                'id_item' => 0,
                'id_nom' => $id_nom,
            ]),
        ];
    }
}
