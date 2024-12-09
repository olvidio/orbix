<?php

namespace certificados\domain;

use certificados\domain\entity\Certificado;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class CertificadoUpload
{

    private static $oDbl;

    /**
     * Para poder cambiar le conexión en el caso de los tests.
     *
     * @param $oDbl
     * @return void
     */
    public static function setoDbl($oDbl): void
    {
        self::$oDbl = $oDbl;
    }

    public static function uploadTxtFirmado(int $id_item, false|string $contenido_doc): string|Certificado
    {
        $error_txt = '';
        $certificadoRepository = new CertificadoRepository();
        if (isset(self::$oDbl)) { // para los tests
            $certificadoRepository->setoDbl(self::$oDbl);
        }
        $oCertificado = $certificadoRepository->findById($id_item);

        $oCertificado->setDocumento($contenido_doc);
        $oCertificado->setFirmado(TRUE);

        if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
            return $certificadoRepository->getErrorTxt();
        }
        return $oCertificado;
    }

    public static function uploadNew(int                             $id_nom,
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
        if (isset(self::$oDbl)) { // para los tests
            $certificadoRepository->setoDbl(self::$oDbl);
        }
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
        $oCertificado->setF_enviado($oF_enviado);

        if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
            return $certificadoRepository->getErrorTxt();
        }
        return $oCertificado;
    }
}