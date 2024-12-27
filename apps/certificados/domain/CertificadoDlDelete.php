<?php

namespace certificados\domain;

use certificados\domain\repositories\CertificadoDlRepository;

class CertificadoDlDelete
{
    private static mixed $oDbl;

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

    /**
     * @param int $Qid_item
     * @return string
     */
    public static function delete(int $Qid_item): string
    {
        $error_txt = '';
        if (!empty($Qid_item)) {
            $CertificadoDlRepository = new CertificadoDlRepository();
            if (isset(self::$oDbl)) { // para los tests
                $CertificadoDlRepository->setoDbl(self::$oDbl);
            }
            $oCertificado = $CertificadoDlRepository->findById($Qid_item);
            if ($CertificadoDlRepository->Eliminar($oCertificado) === FALSE) {
                $error_txt .= $CertificadoDlRepository->getErrorTxt();
            }
        } else {
            $error_txt = _("No se encuentra el certificado");
        }
        return $error_txt;
    }
}