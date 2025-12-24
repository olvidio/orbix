<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\personas\domain\entity\Persona;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class CertificadoRecibidoUpload
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
                                     ?string                         $destino): string|CertificadoRecibido
    {
        $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        if (empty($destino)) {
            $destino = $oPersona->getDl();
        }

        $certificadoRecibidoRepository = $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);
        if (empty($Qid_item)) {
            $id_item = $certificadoRecibidoRepository->getNewId_item();
            $oCertificadoRecibido = new CertificadoRecibido();
            $oCertificadoRecibido->setId_item($id_item);
        } else {
            $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
        }
        $oCertificadoRecibido->setDocumento($contenido_doc);
        $oCertificadoRecibido->setId_nom($Qid_nom);
        $oCertificadoRecibido->setNom($nom);
        $oCertificadoRecibido->setDestino($destino);
        $oCertificadoRecibido->setIdioma($Qidioma);
        $oCertificadoRecibido->setCertificado($Qcertificado);
        $oCertificadoRecibido->setFirmado($firmado);
        $oCertificadoRecibido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificadoRecibido->setF_certificado($oF_certificado);
        $oCertificadoRecibido->setF_recibido($oF_recibido);

        if ($certificadoRecibidoRepository->Guardar($oCertificadoRecibido) === false) {
            return $certificadoRecibidoRepository->getErrorTxt();
        }
        return $oCertificadoRecibido;
    }
}