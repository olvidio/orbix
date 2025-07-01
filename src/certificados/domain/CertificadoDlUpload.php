<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoDlRepository;
use src\certificados\domain\entity\CertificadoDl;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

class CertificadoDlUpload
{
    private $oDbl;

    /**
     * Para poder cambiar le conexión en el caso de los tests.
     *
     * @param $oDbl
     * @return void
     */
    public function setoDbl($oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    public function uploadNew(int                             $Qid_item,
                                     int                             $Qid_nom,
                                     false|string                    $contenido_doc,
                                     string                          $Qidioma,
                                     string                          $Qcertificado,
                                     bool                          $firmado,
                                     DateTimeLocal|NullDateTimeLocal $oF_certificado,
                                     DateTimeLocal|NullDateTimeLocal $oF_recibido,
                                     ?string                         $destino): string|CertificadoDl
    {
        $oPersona = Persona::NewPersona($Qid_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        if (empty($destino)) {
            $destino = $oPersona->getDl();
        }

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
        $oCertificadoDl->setFirmado($firmado);
        $oCertificadoDl->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificadoDl->setF_certificado($oF_certificado);
        $oCertificadoDl->setF_recibido($oF_recibido);

        if ($certificadoDlRepository->Guardar($oCertificadoDl) === FALSE) {
            return $certificadoDlRepository->getErrorTxt();
        }
        return $oCertificadoDl;
    }
}