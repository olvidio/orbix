<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoRepository;
use src\certificados\domain\entity\Certificado;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

class CertificadoUpload
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

    public function uploadTxtFirmado(int $id_item, string $contenido_doc): string|Certificado
    {
        $error_txt = '';
        $certificadoRepository = new CertificadoRepository();
        if (isset($this->oDbl)) { // para los tests
            $certificadoRepository->setoDbl($this->oDbl);
        }
        $oCertificado = $certificadoRepository->findById($id_item);

        $oCertificado->setDocumento($contenido_doc);
        $oCertificado->setFirmado(TRUE);

        if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
            return $certificadoRepository->getErrorTxt();
        }
        return $oCertificado;
    }

    public function uploadNew(int                             $id_nom,
                              false|string                    $contenido_doc,
                              string                          $idioma,
                              string                          $certificado,
                              bool                            $firmado,
                              DateTimeLocal|NullDateTimeLocal $oF_certificado,
                              DateTimeLocal|NullDateTimeLocal $oF_enviado,
                              ?string                         $destino): string|Certificado
    {
        $oPersona = Persona::NewPersona($id_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        if (empty($destino)) {
            $destino = $oPersona->getDl();
        }

        $certificadoRepository = new CertificadoRepository();
        $certificadoRepository->setoDbl($this->oDbl);
        $id_item = $certificadoRepository->getNewId_item();
        $oCertificado = new Certificado();
        $oCertificado->setId_item($id_item);
        /*
        if (empty($Qid_item)) {
            $id_item = $certificadoRepository->getNewId_item();
            $oCertificado = new Certificado();
            $oCertificado->setId_item($id_item);
        } else {
            $oCertificado = $certificadoRepository->findById($Qid_item);
        }
        */
        $oCertificado->setDocumento($contenido_doc);
        $oCertificado->setId_nom($id_nom);
        $oCertificado->setNom($nom);
        $oCertificado->setDestino($destino);
        $oCertificado->setIdioma($idioma);
        $oCertificado->setCertificado($certificado);
        $oCertificado->setFirmado($firmado);
        $oCertificado->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificado->setF_certificado($oF_certificado);
        $oCertificado->setF_enviado($oF_enviado);

        if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
            return $certificadoRepository->getErrorTxt();
        }
        return $oCertificado;
    }
}