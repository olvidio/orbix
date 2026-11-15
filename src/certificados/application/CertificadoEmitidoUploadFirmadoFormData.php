<?php

namespace src\certificados\application;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\application\services\PersonaFinderService;
use src\shared\security\HashB;

final class CertificadoEmitidoUploadFirmadoFormData
{
    public function __construct(
        private readonly CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        private readonly PersonaFinderService $personaFinderService,
    ) {
    }

    /**
     * @return array{id_nom: int, nom: string, apellidos_nombre: string, ctx_guardar: string}
     */
    public function execute(int $id_item): array
    {
        $oCertificadoEmitido = $this->certificadoEmitidoRepository->findById($id_item);
        if ($oCertificadoEmitido === null) {
            throw new \RuntimeException(_("No se encuentra el certificado"));
        }

        $id_nom = (int) ($oCertificadoEmitido->getId_nom() ?? 0);
        $nom = (string) ($oCertificadoEmitido->getNom() ?? '');

        $oPersona = $this->personaFinderService->findPersonaEnGlobal($id_nom);
        $apellidos_nombre = $oPersona !== null ? $oPersona->getApellidosNombre() : '';
        $nom_final = $nom === '' ? $apellidos_nombre : $nom;

        return [
            'id_nom' => $id_nom,
            'nom' => $nom_final,
            'apellidos_nombre' => $apellidos_nombre,
            // Subir el PDF firmado de este certificado: `certificado_emitido_pdf_upload` con `solo_pdf`.
            'ctx_guardar' => HashB::sign('certificado_emitido_guardar', ['nuevo' => 0, 'id_item' => $id_item, 'id_nom' => $id_nom]),
        ];
    }
}
