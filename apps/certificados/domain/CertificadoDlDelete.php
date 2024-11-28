<?php

namespace certificados\domain;

use certificados\domain\repositories\CertificadoDlRepository;

class CertificadoDlDelete
{

    /**
     * @param int $Qid_item
     * @return string
     */
    public static function delete(int $Qid_item): string
    {
        $error_txt = '';
        if (!empty($Qid_item)) {
            $CertificadoDlRepository = new CertificadoDlRepository();
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