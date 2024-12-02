<?php

namespace certificados\domain;

use certificados\domain\repositories\CertificadoRepository;
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;

class CertificadoDelete
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
            $CertificadoRepository = new CertificadoRepository();
            if (isset(self::$oDbl)) { // para los tests
                $CertificadoRepository->setoDbl(self::$oDbl);
            }
            $oCertificado = $CertificadoRepository->findById($Qid_item);
            $certificado = $oCertificado->getCertificado();
            if ($CertificadoRepository->Eliminar($oCertificado) === FALSE) {
                $error_txt .= $CertificadoRepository->getErrorTxt();
            }
            // Hay que borrar también el certificado de las notas_otra_region_stgr
            // Se supone que si accedo a esta página es porque soy una región del stgr.
            $esquema_region_stgr = $_SESSION['session_auth']['esquema'];
            $gesPersonaNotaOtraRegionStgr = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
            if (isset(self::$oDbl)) { // para los tests
                $gesPersonaNotaOtraRegionStgr->setoDbl(self::$oDbl);
            }
            $gesPersonaNotaOtraRegionStgr->deleteCertificado($certificado);
        } else {
            $error_txt = _("No se encuentra el certificado");
        }
        return $error_txt;
    }
}