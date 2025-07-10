<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoEmitidoRepository;
use src\certificados\domain\entity\CertificadoEmitido;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class CertificadoEmitidoUpload
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

    public function uploadTxtFirmado(int $id_item, string $contenido_doc): string|CertificadoEmitido
    {
        $error_txt = '';
        $certificadoEmitidoRepository = new CertificadoEmitidoRepository();
        if (isset($this->oDbl)) { // para los tests
            $certificadoEmitidoRepository->setoDbl($this->oDbl);
        }
        $oCertificadoEmitido = $certificadoEmitidoRepository->findById($id_item);

        $oCertificadoEmitido->setDocumento($contenido_doc);
        $oCertificadoEmitido->setFirmado(TRUE);

        if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === FALSE) {
            return $certificadoEmitidoRepository->getErrorTxt();
        }
        return $oCertificadoEmitido;
    }

    public function uploadNew(int                             $id_nom,
                              false|string                    $contenido_doc,
                              string                          $idioma,
                              string                          $certificado,
                              bool                            $firmado,
                              DateTimeLocal|NullDateTimeLocal $oF_certificado,
                              DateTimeLocal|NullDateTimeLocal $oF_enviado,
                              ?string                         $destino): string|CertificadoEmitido
    {
        $oPersona = Persona::NewPersona($id_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        if (empty($destino)) {
            $destino = $oPersona->getDl();
        }

        $certificadoEmitidoRepository = new CertificadoEmitidoRepository();
        $certificadoEmitidoRepository->setoDbl($this->oDbl);
        $id_item = $certificadoEmitidoRepository->getNewId_item();
        $oCertificadoEmitido = new CertificadoEmitido();
        $oCertificadoEmitido->setId_item($id_item);
        $oCertificadoEmitido->setDocumento($contenido_doc);
        $oCertificadoEmitido->setId_nom($id_nom);
        $oCertificadoEmitido->setNom($nom);
        $oCertificadoEmitido->setDestino($destino);
        $oCertificadoEmitido->setIdioma($idioma);
        $oCertificadoEmitido->setCertificado($certificado);
        $oCertificadoEmitido->setFirmado($firmado);
        $oCertificadoEmitido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificadoEmitido->setF_certificado($oF_certificado);
        $oCertificadoEmitido->setF_enviado($oF_enviado);

        if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === FALSE) {
            return $certificadoEmitidoRepository->getErrorTxt();
        }
        return $oCertificadoEmitido;
    }
}