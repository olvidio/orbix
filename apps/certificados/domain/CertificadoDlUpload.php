<?php

namespace certificados\domain;

use certificados\domain\entity\CertificadoDl;
use certificados\domain\repositories\CertificadoDlRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use web\DateTimeLocal;
use function core\is_true;

class CertificadoDlUpload
{

    public static function uploadNew(int                 $Qid_nom,
                                     int                 $Qid_item,
                                     false|string        $contenido_doc,
                                     string              $Qidioma,
                                     string              $Qcertificado,
                                     string              $Qfirmado,
                                     false|DateTimeLocal $oF_certificado,
                                     false|DateTimeLocal $oF_recibido): string
    {
        $error_txt = '';
        $oPersona = Persona::NewPersona($Qid_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        $destino = ConfigGlobal::mi_region();

        $certificadoDlRepository = new CertificadoDlRepository();
        if (empty($Qid_item)) {
            $id_item = $certificadoDlRepository->getNewId_item();
            $oCertificadoDl = new CertificadoDl();
            $oCertificadoDl->setId_item($id_item);
        } else {
            $oCertificadoDl = $certificadoDlRepository->findById($Qid_item);
        }
        $oCertificadoDl->setDocumento($contenido_doc);
        $oCertificadoDl->setId_nom($Qid_nom);
        $oCertificadoDl->setNom($nom);
        $oCertificadoDl->setDestino($destino);
        $oCertificadoDl->setIdioma($Qidioma);
        $oCertificadoDl->setCertificado($Qcertificado);
        if (is_true($Qfirmado)) {
            $firmado = TRUE;
        } else {
            $firmado = FALSE;
        }
        $oCertificadoDl->setFirmado($firmado);
        $oCertificadoDl->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificadoDl->setF_certificado($oF_certificado);
        $oCertificadoDl->setF_recibido($oF_recibido);

        if ($certificadoDlRepository->Guardar($oCertificadoDl) === FALSE) {
            $error_txt .= $certificadoDlRepository->getErrorTxt();
        }
        return $error_txt;
    }
}