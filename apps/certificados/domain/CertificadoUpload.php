<?php

namespace certificados\domain;

use certificados\domain\entity\Certificado;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use web\DateTimeLocal;

class CertificadoUpload
{

    public static function uploadTxt(int $id_item, false|string $contenido_doc): string
    {
        $error_txt = '';
        $certificadoDlRepository = new CertificadoRepository();
        $oCertificadoDl = $certificadoDlRepository->findById($id_item);

        $oCertificadoDl->setDocumento($contenido_doc);
        $oCertificadoDl->setFirmado(TRUE);

        if ($certificadoDlRepository->Guardar($oCertificadoDl) === FALSE) {
            $error_txt .= $certificadoDlRepository->getErrorTxt();
        }
        return $error_txt;
    }

    public static function uploadNew(false|string $contenido_doc, int $id_nom, string $certificado, bool $firmado, string $idioma, DateTimeLocal $oF_certificado): string
    {
        $error_txt = '';
        $oPersona = Persona::NewPersona($id_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        $destino = ConfigGlobal::mi_region();

        $certificadoRepository = new CertificadoRepository();
        $id_item = $certificadoRepository->getNewId_item();
        $oCertificado = new Certificado();
        $oCertificado->setId_item($id_item);
        $oCertificado->setDocumento($contenido_doc);
        $oCertificado->setId_nom($id_nom);
        $oCertificado->setNom($nom);
        $oCertificado->setDestino($destino);
        $oCertificado->setIdioma($idioma);
        $oCertificado->setCertificado($certificado);
        $oCertificado->setFirmado($firmado);
        $oCertificado->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificado->setF_certificado($oF_certificado);

        if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
            $error_txt .= $certificadoRepository->getErrorTxt();
        }
        return $error_txt;
    }
}