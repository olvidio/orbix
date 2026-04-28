<?php

namespace src\certificados\application;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;

final class CertificadoEmitidoUploadFirmadoFormData
{
    /**
     * @return array{id_nom: int, nom: string, apellidos_nombre: string}
     */
    public static function execute(int $id_item): array
    {
        $certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
        $oCertificadoEmitido = $certificadoEmitidoRepository->findById($id_item);

        $id_nom = (int)($oCertificadoEmitido->getId_nom() ?? 0);
        $nom = (string)($oCertificadoEmitido->getNom() ?? '');

        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        $apellidos_nombre = $oPersona !== null ? $oPersona->getApellidosNombre() : '';
        $nom_final = $nom === '' ? $apellidos_nombre : $nom;

        return [
            'id_nom' => $id_nom,
            'nom' => $nom_final,
            'apellidos_nombre' => $apellidos_nombre,
        ];
    }
}
